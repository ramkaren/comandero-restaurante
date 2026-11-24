<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comandas', function (Blueprint $table) {
            $table->string('estado_cocina')->default('guardada')->index()->after('estado');
            $table->timestamp('finalizada_en')->nullable()->after('estado_cocina');
        });
    }

    public function down(): void
    {
        Schema::table('comandas', function (Blueprint $table) {
            $table->dropIndex(['estado_cocina']);
            $table->dropColumn(['estado_cocina', 'finalizada_en']);
        });
    }
};