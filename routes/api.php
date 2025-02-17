<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TorneoController;

Route::post('crear-torneo', [TorneoController::class, 'crearTorneo']);
Route::get('/torneos-finalizados', [TorneoController::class, 'obtenerTorneosFinalizados']);