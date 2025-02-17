<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->string('identificacion')->unique()->after('nombre');
        });
    }

    public function down()
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropColumn('identificacion');
        });
    }
};
