<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\Partido;
use App\Enums\EstadoTorneo;
use Illuminate\Http\Request;

class TorneoController extends Controller
{
    public function crearTorneo(Request $request)
    {
        $request->validate([
            'jugadores' => 'required|array|min:2',
            'jugadores.*.identificacion' => 'required|string',
            'jugadores.*.nombre' => 'required|string',
            'jugadores.*.fecha_nacimiento' => 'required|date',
            'jugadores.*.habilidad' => 'required|integer|min:1|max:100',
            'jugadores.*.tipo' => 'required|in:Masculino,Femenino'
        ]);

        $jugadoresData = collect($request->jugadores);

        // Validar que sean todos del mismo tipo y cantidad par
        $tipo = $jugadoresData->first()['tipo'];
        if ($jugadoresData->pluck('tipo')->unique()->count() > 1) {
            return response()->json(['error' => 'Todos los jugadores deben ser del mismo tipo'], 400);
        }
        // Verificar que sea potencia de 2
        if (!self::esPotenciaDeDos($jugadoresData->count()) and $jugadoresData->count() > 0) {
            return response()->json([
                'error' => 'La cantidad de jugadores debe ser mayor a 0 y potencia de 2 (2, 4, 8, 16, 32, ...).'
            ], 400);
        }

        // Crear o actualizar jugadores
        $jugadores = $jugadoresData->map(function ($jugador) {
            return Jugador::updateOrCreate(
                ['identificacion' => $jugador['identificacion']],
                [
                    'nombre' => $jugador['nombre'],
                    'fecha_nacimiento' => $jugador['fecha_nacimiento'],
                    'habilidad' => $jugador['habilidad'],
                    'tipo' => $jugador['tipo']
                ]
            );
        });

        // Crear el torneo con datos aleatorios
        $torneo = Torneo::create([
            'nombre' => 'Torneo ' . now()->format('Y-m-d H:i:s'),
            'ubicacion' => 'Ubicación Random',
            'estado' => EstadoTorneo::CREADO,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(sqrt($jugadores->count())), //se supone una ronda o instancia por dia
            'sexo' => $tipo
        ]);

        // Asociar jugadores al torneo
        $torneo->jugadores()->attach($jugadores->pluck('id'));

        // Simular los partidos y obtener el ganador
        $ganador = $this->simularTorneo($torneo);

        return response()->json([
            'mensaje' => 'Torneo creado y simulado con éxito',
            'torneo' => $torneo->id,
            'ganador' => [
                'nombre' => $ganador->nombre,
                'identificacion' => $ganador->identificacion,
                'tipo' => $ganador->tipo
            ]
        ]);
    }

    private function simularTorneo(Torneo $torneo)
    {
        $torneo->actualizarEstado(EstadoTorneo::EN_CURSO);
        $jugadores = $torneo->jugadores()->get()->shuffle();
        return $this->crearRonda($torneo, $jugadores, 1);
    }

    private function crearRonda(Torneo $torneo, $jugadores, $ronda)
    {
        if ($jugadores->count() < 2) {
            $torneo->actualizarEstado(EstadoTorneo::FINALIZADO);
            return $jugadores->first(); // El último jugador es el campeón
        }

        $partidos = [];
        for ($i = 0; $i < $jugadores->count(); $i += 2) {
            $es_semi = ($jugadores->count() == 4);
            $es_final = ($jugadores->count() == 2);

            $partido = Partido::create([
                'torneo_id' => $torneo->id,
                'fecha' => now()->addDays($ronda),
                'ronda' => $ronda,
                'es_semi' => $es_semi,
                'es_final' => $es_final
            ]);

            $partido->jugadores()->attach([
                $jugadores[$i]->id => ['es_ganador' => false],
                $jugadores[$i + 1]->id => ['es_ganador' => false]
            ]);

            $partidos[] = $partido;
        }

        $ganadores = collect();
        foreach ($partidos as $partido) {
            // Llamar a la nueva función elegirGanador del modelo Partido
            $ganador = $partido->elegirGanador();

            $partido->jugadores()->updateExistingPivot($ganador->id, ['es_ganador' => true]);
            $ganadores->push($ganador);
        }

        return $this->crearRonda($torneo, $ganadores, $ronda + 1);
    }

    // Función para verificar si un número es potencia de 2
    private static function esPotenciaDeDos($n)
    {
        return ($n > 0) && (($n & ($n - 1)) == 0);
    }

    public function obtenerTorneosFinalizados(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'sexo' => 'nullable|in:Masculino,Femenino',
            'nombre' => 'nullable|string'
        ]);

        $fecha = $request->fecha ? Carbon::parse($request->fecha) : null;

        // Obtener los torneos con filtros opcionales
        $torneos = Torneo::with(['jugadores' => function ($query) {
            $query->wherePivot('es_ganador', true);
        }])
            ->when($fecha, function ($query, $fecha) {
                return $query->where('fecha_inicio', '<=', $fecha)
                        ->where('fecha_fin', '>=', $fecha);
            })
            ->when($request->sexo, fn($query, $sexo) => $query->where('sexo', $sexo))
            ->when($request->nombre, fn($query, $nombre) => $query->where('nombre', 'ILIKE', "%$nombre%"))
            ->get();

        if($torneos->count()==0){
            return response()->json('No se han encontrado torneos.');
        }

        // Formatear la respuesta con los torneos y su ganador
        $respuesta = $torneos->map(function ($torneo) {
            return [
                'id' => $torneo->id,
                'nombre' => $torneo->nombre,
                'fecha_inicio' => $torneo->fecha_inicio,
                'estado' => $torneo->estado,
                'sexo' => $torneo->sexo,
                'ganador' => $torneo->jugadores->first() ? [
                    'id' => $torneo->jugadores->first()->id,
                    'nombre' => $torneo->jugadores->first()->nombre,
                    'identificacion' => $torneo->jugadores->first()->identificacion
                ] : null
            ];
        });

        return response()->json($respuesta);
    }
}
