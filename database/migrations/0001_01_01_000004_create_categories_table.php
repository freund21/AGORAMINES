<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PROYECTO + BASE LARAVEL:
// Migración de categorías y tabla pivote categoria-subcategoria.
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

        // PROYECTO:
        // Relación many-to-many: qué subcategorías pueden votar en cada categoría.
        Schema::create('category_subcategory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained()->onDelete('cascade');
            $table->unique(['category_id', 'subcategory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_subcategory');
        Schema::dropIfExists('categories');
    }
};
