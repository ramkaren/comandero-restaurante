<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Comanda;
use App\Models\Cuenta;
use App\Models\DetalleComanda;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CajeroFlowTest extends TestCase
{
    use RefreshDatabase;

    private function cashier(string $email = 'cashier@example.com'): User
    {
        $role = Role::firstOrCreate(['name' => 'cajero', 'guard_name' => 'web']);
        foreach (['accounts.view', 'payments.view', 'payments.create', 'tickets.view', 'tickets.create', 'sales.view'] as $permission) {
            $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']));
        }

        $user = User::factory()->create(['email' => $email]);
        $user->assignRole($role);

        return $user;
    }

    private function account(string $status = 'lista'): Cuenta
    {
        $waiter = User::factory()->create();
        $table = Mesa::create(['numero' => fake()->unique()->numberBetween(1, 99), 'capacidad' => 4, 'estado' => 'ocupada', 'activa' => true, 'mesero_id' => $waiter->id]);
        $category = Categoria::create(['nombre' => 'Caja '.fake()->unique()->randomNumber(6), 'activa' => true]);
        $product = Producto::create(['categoria_id' => $category->id, 'nombre' => 'Producto cobrable', 'precio' => 100, 'disponible' => true, 'activo' => true]);
        $order = Comanda::create(['mesa_id' => $table->id, 'mesero_id' => $waiter->id, 'estado' => $status, 'total' => 200]);
        DetalleComanda::create(['comanda_id' => $order->id, 'producto_id' => $product->id, 'cantidad' => 2, 'precio_unitario' => 100, 'subtotal' => 200, 'observaciones' => null]);

        return Cuenta::create(['comanda_id' => $order->id, 'mesa_id' => $table->id, 'estado' => 'pendiente', 'subtotal' => 200, 'total' => 200]);
    }

    public function test_cashier_can_view_pending_accounts(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();

        $this->actingAs($cashier)->get(route('cajero.cuentas.index'))
            ->assertOk()
            ->assertSee('Cuenta #'.$account->id)
            ->assertSee('Mesa '.$account->mesa->numero);
    }

    public function test_non_cashier_cannot_access_cashier_module(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('cajero.cuentas.index'))->assertForbidden();
    }

    public function test_cash_payment_calculates_change_and_releases_table(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();

        $response = $this->actingAs($cashier)->post(route('cajero.pagos.store', $account), [
            'metodo' => 'efectivo', 'monto' => 200, 'monto_recibido' => 250,
        ]);

        $payment = Pago::firstOrFail();
        $response->assertRedirect(route('cajero.tickets.show', $payment->cuenta->ticket));
        $this->assertSame('50.00', $payment->cambio);
        $this->assertSame('pagada', $account->refresh()->estado);
        $this->assertSame('disponible', $account->mesa->refresh()->estado);
        $this->assertNull($account->mesa->mesero_id);
        $this->assertDatabaseCount('tickets', 1);
    }

    public function test_insufficient_cash_is_rejected_and_table_stays_occupied(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();

        $response = $this->actingAs($cashier)->from(route('cajero.cuentas.show', $account))->post(route('cajero.pagos.store', $account), [
            'metodo' => 'efectivo', 'monto' => 200, 'monto_recibido' => 199,
        ]);

        $response->assertRedirect(route('cajero.cuentas.show', $account))->assertSessionHasErrors('monto_recibido');
        $this->assertDatabaseCount('pagos', 0);
        $this->assertSame('pendiente', $account->refresh()->estado);
        $this->assertSame('ocupada', $account->mesa->refresh()->estado);
    }

    public function test_cashier_can_register_card_payment(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();

        $this->actingAs($cashier)->post(route('cajero.pagos.store', $account), ['metodo' => 'tarjeta', 'monto' => 200])->assertRedirect();

        $this->assertSame('tarjeta', Pago::firstOrFail()->metodo);
        $this->assertNull(Pago::first()->cambio);
    }

    public function test_cashier_can_register_transfer_payment_with_reference(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();

        $this->actingAs($cashier)->post(route('cajero.pagos.store', $account), ['metodo' => 'transferencia', 'monto' => 200, 'referencia' => 'TRX-001'])->assertRedirect();

        $this->assertSame('transferencia', Pago::firstOrFail()->metodo);
        $this->assertSame('TRX-001', Pago::first()->referencia);
    }

    public function test_paid_account_cannot_be_paid_again(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();
        $this->actingAs($cashier)->post(route('cajero.pagos.store', $account), ['metodo' => 'tarjeta', 'monto' => 200]);

        $response = $this->actingAs($cashier)->from(route('cajero.cuentas.show', $account))->post(route('cajero.pagos.store', $account), ['metodo' => 'tarjeta', 'monto' => 200]);

        $response->assertRedirect(route('cajero.cuentas.show', $account))->assertSessionHasErrors('cuenta');
        $this->assertDatabaseCount('pagos', 1);
    }

    public function test_two_payment_attempts_leave_only_one_payment_for_an_account(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();
        $payload = ['metodo' => 'tarjeta', 'monto' => 200];

        $this->actingAs($cashier)->post(route('cajero.pagos.store', $account), $payload)->assertRedirect();
        $secondResponse = $this->actingAs($cashier)->from(route('cajero.cuentas.show', $account))->post(route('cajero.pagos.store', $account), $payload);

        $secondResponse->assertRedirect(route('cajero.cuentas.show', $account))->assertSessionHasErrors('cuenta');
        $this->assertDatabaseCount('pagos', 1);
        $this->assertDatabaseCount('tickets', 1);
    }

    public function test_ticket_can_be_consulted(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();
        $this->actingAs($cashier)->post(route('cajero.pagos.store', $account), ['metodo' => 'tarjeta', 'monto' => 200]);
        $ticket = $account->refresh()->ticket;

        $this->actingAs($cashier)->get(route('cajero.tickets.show', $ticket))->assertOk()->assertSee($ticket->numero)->assertSee('Producto cobrable');
    }

    public function test_today_sales_show_only_confirmed_payments(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();
        $this->actingAs($cashier)->post(route('cajero.pagos.store', $account), ['metodo' => 'tarjeta', 'monto' => 200]);

        $this->actingAs($cashier)->get(route('cajero.ventas.index'))->assertOk()->assertSee('$200.00');
    }

    public function test_payment_requires_a_valid_amount(): void
    {
        $cashier = $this->cashier();
        $account = $this->account();

        $response = $this->actingAs($cashier)->from(route('cajero.cuentas.show', $account))->post(route('cajero.pagos.store', $account), ['metodo' => 'tarjeta', 'monto' => 0]);

        $response->assertRedirect(route('cajero.cuentas.show', $account))->assertSessionHasErrors('monto');
        $this->assertSame('pendiente', $account->refresh()->estado);
    }
}
