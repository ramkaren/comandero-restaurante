<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->unique()->constrained('cuentas')->restrictOnDelete();
            $table->foreignId('cajero_id')->constrained('users')->restrictOnDelete();
            $table->string('metodo');
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_recibido', 10, 2)->nullable();
            $table->decimal('cambio', 10, 2)->nullable();
            $table->string('referencia')->nullable();
            $table->string('estado')->default('confirmado')->index();
            $table->timestamp('pagado_at')->nullable();
            $table->timestamps();

            $table->index(['metodo', 'pagado_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};