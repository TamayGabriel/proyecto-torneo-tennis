<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jugadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha_nacimiento');
            $table->unsignedTinyInteger('habilidad'); // 1-100
            $table->enum('tipo', ['masculino', 'femenino']);
            $table->unsignedTinyInteger('reaccion')->nullable(); // Solo para femenino
            $table->unsignedTinyInteger('fuerza')->nullable();   // Solo para masculino
            $table->unsignedTinyInteger('velocidad')->nullable(); // Solo para masculino
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jugadores');
    }
};