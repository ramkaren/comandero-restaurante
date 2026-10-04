<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Comanda;
use App\Models\DetalleComanda;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CocinaFlowTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $roleName, string $email, array $permissions = []): User
    {
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

        foreach ($permissions as $permissionName) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']));
        }

        $user = User::factory()->create(['email' => $email]);
        $user->assignRole($role);

        return $user;
    }

    private function order(string $status = 'guardada'): Comanda
    {
        $waiter = User::factory()->create();
        $table = Mesa::create(['numero' => fake()->unique()->numberBetween(1, 99), 'capacidad' => 4, 'estado' => 'ocupada', 'activa' => true, 'mesero_id' => $waiter->id]);
        $category = Categoria::create(['nombre' => 'Cocina '.fake()->unique()->randomNumber(6), 'activa' => true]);
        $product = Producto::create(['categoria_id' => $category->id, 'nombre' => 'Platillo de prueba', 'precio' => 80, 'disponible' => true, 'activo' => true]);
        $order = Comanda::create(['mesa_id' => $table->id, 'mesero_id' => $waiter->id, 'estado' => $status, 'total' => 160]);
        DetalleComanda::create(['comanda_id' => $order->id, 'producto_id' => $product->id, 'cantidad' => 2, 'precio_unitario' => 80, 'subtotal' => 160, 'observaciones' => 'Sin cebolla']);

        return $order;
    }

    public function test_cook_can_view_pending_orders_and_details(): void
    {
        $cook = $this->userWithRole('cocinero', 'cook@example.com', ['kitchen.view', 'kitchen.update-status']);
        $order = $this->order();

        $this->actingAs($cook)->get(route('cocina.index'))
            ->assertOk()
            ->assertSee('Mesa '.$order->mesa->numero)
            ->assertSee('Platillo de prueba')
            ->assertSee('2 × Platillo de prueba');

        $this->actingAs($cook)->get(route('cocina.show', $order))
            ->assertOk()
            ->assertSee('Sin cebolla');
    }

    public function test_cook_can_move_order_from_pending_to_preparing_to_ready(): void
    {
        $cook = $this->userWithRole('cocinero', 'cook@example.com', ['kitchen.view', 'kitchen.update-status']);
        $order = $this->order();

        $this->actingAs($cook)->post(route('cocina.start', $order))->assertRedirect();
        $this->assertSame('en_preparacion', $order->refresh()->estado);

        $this->actingAs($cook)->post(route('cocina.finish', $order))->assertRedirect();
        $this->assertSame('lista', $order->refresh()->estado);
    }

    public function test_non_cook_cannot_access_kitchen_even_with_permission(): void
    {
        $waiter = $this->userWithRole('mesero', 'waiter-kitchen@example.com', ['kitchen.view', 'kitchen.update-status']);
        $order = $this->order();

        $this->actingAs($waiter)->get(route('cocina.index'))->assertForbidden();
        $this->actingAs($waiter)->post(route('cocina.start', $order))->assertForbidden();
    }

    public function test_cook_cannot_change_order_contents(): void
    {
        $cook = $this->userWithRole('cocinero', 'cook-readonly@example.com', ['kitchen.view', 'kitchen.update-status']);
        $order = $this->order();
        $detail = $order->detalles()->firstOrFail();

        $this->actingAs($cook)->post(route('mesero.comandas.add', $order), ['producto_id' => $detail->producto_id, 'cantidad' => 1])->assertForbidden();
        $this->assertSame(2, $detail->refresh()->cantidad);
    }

    public function test_invalid_kitchen_transition_is_rejected(): void
    {
        $cook = $this->userWithRole('cocinero', 'cook-invalid@example.com', ['kitchen.view', 'kitchen.update-status']);
        $order = $this->order('lista');

        $this->actingAs($cook)->post(route('cocina.start', $order))->assertStatus(409);
    }
}
