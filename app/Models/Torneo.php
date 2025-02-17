<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'ubicacion', 'estado', 'fecha_inicio', 'fecha_fin', 'sexo'];

    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }

    public function actualizarEstado($nuevoEstado)
    {
        $this->estado = $nuevoEstado;
        $this->save();
    }
}