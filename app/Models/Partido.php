<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = ['torneo_id', 'fecha', 'resultado', 'ronda', 'es_semi', 'es_final'];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'partidos_jugadores');
    }


    public function elegirGanador()
    {
        // Obtener los jugadores del partido
        $jugadores = $this->jugadores;

        if ($jugadores->count() !== 2) {
            throw new \Exception("El partido debe tener exactamente 2 jugadores.");
        }

        // Calcular el puntaje de cada jugador
        $puntajes = [];
        foreach ($jugadores as $jugador) {
            $puntajes[$jugador->id] = $this->obtenerPuntaje($jugador);
        }

        // Obtener el jugador con el puntaje más alto
        $ganadorId = array_keys($puntajes, max($puntajes))[0];
        $ganador = $jugadores->where('id', $ganadorId)->first();

        // Marcar el ganador en la tabla pivote
        DB::table('partidos_jugadores')
            ->where('partido_id', $this->id)
            ->where('jugador_id', $ganador->id)
            ->update(['es_ganador' => true]);

        return $ganador;
    }

    public function obtenerPuntaje(Jugador $jugador)
    {
        // Verificar el sexo del torneo y llamar a la función correspondiente
        if ($this->torneo->sexo === 'Masculino') {
            return $this->obtenerPuntajeMasculino($jugador);
        }

        // En caso de que el torneo sea femenino
        return $this->obtenerPuntajeFemenino($jugador);
    }

    public function obtenerPuntajeMasculino(Jugador $jugador)
    {
        // Generar un número aleatorio entre 1 y 100 para la suerte
        $suerte = rand(1, 100);

        // Calcular el puntaje según la fórmula
        $puntaje = (4 * $jugador->habilidad + 2 * $jugador->fuerza + 2 * $jugador->velocidad + 2 * $suerte) / 10;

        return round($puntaje, 2); // Redondeamos a 2 decimales
    }

    public function obtenerPuntajeFemenino(Jugador $jugador)
    {
        // Generar un número aleatorio entre 1 y 100 para la suerte
        $suerte = rand(1, 100);

        // Calcular el puntaje según la fórmula
        $puntaje = (5 * $jugador->habilidad + 3 * $jugador->reaccion + 2 * $suerte) / 10;

        return round($puntaje, 2); // Redondeamos a 2 decimales
    }
}
