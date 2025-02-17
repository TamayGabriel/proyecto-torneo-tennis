<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('torneos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ubicacion');
            $table->enum('estado', ['creado', 'en curso', 'finalizado']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('sexo', ['Masculino', 'Femenino']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('torneos');
    }
};
