<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesa_id')->constrained('mesas')->restrictOnDelete();
            $table->foreignId('mesero_id')->constrained('users')->restrictOnDelete();
            $table->string('estado')->default('borrador')->index();
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['mesa_id', 'estado']);
            $table->index(['mesero_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comandas');
    }
};