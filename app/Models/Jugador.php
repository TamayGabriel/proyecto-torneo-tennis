<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    use HasFactory;

    protected $table = 'jugadores'; // Laravel ya lo haría automáticamente, pero lo aclaramos

    protected $fillable = [
        'nombre',
        'identificacion',
        'fecha_nacimiento',
        'habilidad',
        'tipo',
        'reaccion',
        'fuerza',
        'velocidad'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // Relación con los partidos a través de la tabla pivote
    public function partidos()
    {
        return $this->belongsToMany(Partido::class, 'partidos_jugadores');
    }
}