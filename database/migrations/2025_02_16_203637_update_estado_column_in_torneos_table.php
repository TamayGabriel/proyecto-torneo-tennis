<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // 1. Crear el tipo ENUM en PostgreSQL si no existe
        DB::statement("CREATE TYPE estado_torneo AS ENUM ('creado', 'en curso', 'finalizado')");

        // 2. Modificar la columna estado para usar el nuevo ENUM
        Schema::table('torneos', function (Blueprint $table) {
            $table->dropColumn('estado'); // Primero eliminamos la columna
        });

        Schema::table('torneos', function (Blueprint $table) {
            $table->enum('estado', ['creado', 'en curso', 'finalizado'])->default('creado');
        });
    }

    public function down()
    {
        Schema::table('torneos', function (Blueprint $table) {
            $table->dropColumn('estado'); // Eliminamos la columna nuevamente
        });

        // 3. Eliminar el tipo ENUM de PostgreSQL
        DB::statement("DROP TYPE estado_torneo");

        Schema::table('torneos', function (Blueprint $table) {
            $table->string('estado')->default('creado');
        });
    }
};
