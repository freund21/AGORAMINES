<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PROYECTO + BASE LARAVEL:
// Migracion de votos emitidos creada para este proyecto.
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
            // Puede ser null en votaciones anonimas.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            // PROYECTO:
            // Se guarda contenido cifrado para trazabilidad/verificacion.
            $table->text('encrypted_vote')->nullable();
            // PROYECTO:
            // Codigo unico para que el votante verifique su voto.
            $table->string('receipt_code')->unique();
            $table->timestamps();

            // PROYECTO:
            // Evita doble voto de un mismo usuario en la misma categoria.
            $table->unique(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
