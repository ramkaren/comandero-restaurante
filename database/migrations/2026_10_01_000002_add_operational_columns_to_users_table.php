<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->nullable()->after('password');
            $table->boolean('is_active')->default(true)->after('area_id');
            $table->date('last_login')->nullable()->after('is_active');
            $table->foreign('area_id')->references('area_id')->on('areas');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn(['area_id', 'is_active', 'last_login']);
        });
    }
};