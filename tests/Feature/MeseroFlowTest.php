<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Comanda;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MeseroFlowTest extends TestCase
{
    use RefreshDatabase;

    private function waiter(string $email): User
    {
        $role = Role::firstOrCreate(['name' => 'mesero', 'guard_name' => 'web']);
        foreach (['tables.view', 'tables.select', 'tables.release', 'orders.view', 'orders.create', 'orders.delete-product'] as $name) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']));
        }

        $user = User::factory()->create(['email' => $email]);
        $user->assignRole($role);

        return $user;
    }

    private function product(): Producto
    {
        $category = Categoria::create(['nombre' => 'Pruebas', 'activa' => true]);

        return Producto::create([
            'categoria_id' => $category->id,
            'nombre' => 'Producto de prueba',
            'precio' => 25.50,
            'disponible' => true,
            'activo' => true,
        ]);
    }

    public function test_waiter_can_select_available_table_and_create_order(): void
    {
        $waiter = $this->waiter('waiter-a@example.com');
        $table = Mesa::create(['numero' => 1, 'capacidad' => 4, 'estado' => 'disponible', 'activa' => true]);

        $response = $this->actingAs($waiter)->post(route('mesero.mesas.select', $table));

        $order = Comanda::first();
        $response->assertRedirect(route('mesero.comandas.edit', $order));
        $this->assertSame('ocupada', $table->refresh()->estado);
        $this->assertSame($waiter->id, $table->refresh()->mesero_id);
        $this->assertSame($waiter->id, $order->mesero_id);
    }

    public function test_second_waiter_cannot_select_table_owned_by_first_waiter(): void
    {
        $first = $this->waiter('waiter-a@example.com');
        $second = $this->waiter('waiter-b@example.com');
        $table = Mesa::create(['numero' => 2, 'capacidad' => 4, 'estado' => 'disponible', 'activa' => true]);

        $this->actingAs($first)->post(route('mesero.mesas.select', $table));
        $response = $this->actingAs($second)->from(route('mesero.mesas'))->post(route('mesero.mesas.select', $table));

        $response->assertRedirect(route('mesero.mesas'));
        $response->assertSessionHasErrors('mesa');
        $this->assertSame($first->id, $table->refresh()->mesero_id);
        $this->assertCount(1, Comanda::all());
    }

    public function test_waiter_can_add_adjust_and_save_order(): void
    {
        $waiter = $this->waiter('waiter-a@example.com');
        $table = Mesa::create(['numero' => 3, 'capacidad' => 2, 'estado' => 'disponible', 'activa' => true]);
        $product = $this->product();
        $this->actingAs($waiter)->post(route('mesero.mesas.select', $table));
        $order = Comanda::firstOrFail();

        $this->actingAs($waiter)->post(route('mesero.comandas.add', $order), ['producto_id' => $product->id, 'cantidad' => 2])->assertRedirect();
        $detail = $order->detalles()->firstOrFail();
        $this->assertSame('51.00', $detail->subtotal);
        $this->assertSame('51.00', $order->refresh()->total);

        $this->actingAs($waiter)->patch(route('mesero.comandas.adjust', [$order, $detail]), ['cambio' => -1])->assertRedirect();
        $this->assertSame(1, $detail->refresh()->cantidad);
        $this->assertSame('25.50', $order->refresh()->total);

        $this->actingAs($waiter)->post(route('mesero.comandas.save', $order))->assertRedirect(route('mesero.comandas'));
        $this->assertSame('guardada', $order->refresh()->estado);
    }

    public function test_waiter_cannot_modify_another_waiters_order(): void
    {
        $first = $this->waiter('waiter-a@example.com');
        $second = $this->waiter('waiter-b@example.com');
        $table = Mesa::create(['numero' => 4, 'capacidad' => 4, 'estado' => 'disponible', 'activa' => true]);
        $product = $this->product();
        $this->actingAs($first)->post(route('mesero.mesas.select', $table));
        $order = Comanda::firstOrFail();

        $response = $this->actingAs($second)->post(route('mesero.comandas.add', $order), ['producto_id' => $product->id, 'cantidad' => 1]);

        $response->assertForbidden();
        $this->assertDatabaseCount('detalle_comandas', 0);
    }

    public function test_waiter_can_release_owned_table(): void
    {
        $waiter = $this->waiter('waiter-a@example.com');
        $table = Mesa::create(['numero' => 5, 'capacidad' => 4, 'estado' => 'disponible', 'activa' => true]);
        $this->actingAs($waiter)->post(route('mesero.mesas.select', $table));

        $this->actingAs($waiter)->post(route('mesero.mesas.release', $table))->assertRedirect(route('mesero.mesas'));

        $this->assertSame('disponible', $table->refresh()->estado);
        $this->assertNull($table->mesero_id);
    }
}
