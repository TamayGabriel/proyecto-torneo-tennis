<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('partidos_jugadores', function (Blueprint $table) {
            $table->id(); // Define un id en la tabla pivote
            $table->foreignId('partido_id')->constrained('partidos'); // Relación con partidos
            $table->foreignId('jugador_id')->constrained('jugadores'); // Relación con jugadores
            $table->boolean('es_ganador')->default(false); // Agrega el campo es_ganador
            $table->timestamps(); // Agrega los campos de created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidos_jugadores');
    }
};