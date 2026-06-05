<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PROYECTO + BASE LARAVEL:
// Migración de votos emitidos creada para este proyecto.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('option_id')->constrained()->onDelete('cascade');
            // PROYECTO:
            // Puede ser null en votaciones anónimas.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            // PROYECTO:
            // Se guarda contenido cifrado para trazabilidad/verificación.
            $table->text('encrypted_vote')->nullable();
            // PROYECTO:
            // Código único para que el votante verifique su voto.
            $table->string('receipt_code')->unique();
            $table->timestamps();

            // PROYECTO:
            // Índices para acelerar el conteo de resultados por categoría/opción.
            $table->index('category_id');
            $table->index('option_id');

            // NOTA: el control de "ya ha votado" se hace en la tabla participations
            // (migración 0008). No se usa unique(user_id, category_id) porque:
            //  - en votaciones anónimas user_id es null y varios null se consideran distintos,
            //  - con max_selections > 1 un usuario genera varias filas legítimas en la categoría.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
