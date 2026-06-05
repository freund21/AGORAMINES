<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PROYECTO:
// Registro de participación: marca que un usuario YA votó en una categoría.
// Se separa de la tabla votes a propósito para poder evitar el doble voto
// también en votaciones anónimas (donde votes.user_id es null) sin revelar
// el contenido del voto. Aquí no se guarda qué se votó, solo que se votó.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // PROYECTO:
            // Un usuario solo puede tener una participación por categoría.
            $table->unique(['category_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participations');
    }
};
