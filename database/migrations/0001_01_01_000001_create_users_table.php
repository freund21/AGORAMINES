<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// MEZCLA BASE LARAVEL + PROYECTO:
// users: Laravel trae una tabla de usuarios, pero aquí se ha personalizado
// con dni, username, full_name y role_id.
// sessions: tabla base de Laravel para sesiones activas.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('dni')->unique();
            $table->string('username')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password');
            // PROYECTO:
            // Relación con roles.id; por defecto 2 (votante en este proyecto).
            $table->foreignId('role_id')->constrained('roles')->default(2);
            $table->rememberToken();
            $table->timestamps();
        });

        // BASE LARAVEL:
        // Tabla de sesiones para mantener login entre peticiones.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        // BASE LARAVEL:
        // Orden inverso por dependencia.
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
