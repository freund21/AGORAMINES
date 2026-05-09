<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PROYECTO + BASE LARAVEL:
// Migración creada para las votaciones (elections) de este proyecto.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('standard');
            // PROYECTO:
            // Si es anónima, los votos pueden guardarse sin user_id.
            $table->boolean('is_anonymous')->default(false);
            // PROYECTO:
            // Controla si se muestran resultados mientras sigue abierta.
            $table->boolean('realtime_results_enabled')->default(true);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            // PROYECTO:
            // Estado de flujo de la votación.
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};
