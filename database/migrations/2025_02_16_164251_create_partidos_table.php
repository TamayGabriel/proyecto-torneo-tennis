<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos'); // Relación con la tabla torneos
            $table->dateTime('fecha');
            $table->string('resultado')->nullable();
            $table->integer('ronda');
            $table->boolean('es_semi')->default(false); // ¿Es semifinal?
            $table->boolean('es_final')->default(false); // ¿Es final?
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};