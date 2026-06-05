<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PROYECTO + BASE LARAVEL:
// Migración de categorías. La asignación de votantes se hace directamente
// usuario <-> categoría (tabla category_user, migración 0007).
return new class extends Migration
{
    public function up(): void
    {
        // PROYECTO:
        // Cada categoría pertenece a una elección.
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->onDelete('cascade');
            $table->string('name');
            // PROYECTO:
            // Máximo de opciones que puede seleccionar un votante en esta categoría.
            $table->integer('max_selections')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
