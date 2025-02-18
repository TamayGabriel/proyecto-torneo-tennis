<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TorneoController;

Route::post('crear-torneo', [TorneoController::class, 'crearTorneo']);
Route::get('/torneos-finalizados', [TorneoController::class, 'obtenerTorneosFinalizados']);

Route::get('/db-test', function () {
    try {
        // Intenta ejecutar una consulta simple para verificar la conexión
        DB::select('SELECT 1');
        return 'Conexión exitosa a PostgreSQL!';
    } catch (\Exception $e) {
        return 'Error de conexión: ' . $e->getMessage();
    }
});