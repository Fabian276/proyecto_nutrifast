<?php

namespace App\Http\Controllers;

use App\Models\Medicion;
use App\Models\Paciente;

class ProgresoController extends Controller
{
    public function show(Paciente $paciente)
    {
        $consultas = $paciente->consultas()->with('medicion')->get()->filter(fn ($c) => $c->medicion);

        if ($consultas->count() < 2) {
            return redirect()->route('pacientes.show', $paciente)
                ->with('status', 'Se necesitan al menos dos consultas para ver el progreso.');
        }

        // Estructura que consume public/js/progreso.js
        $puntos = $consultas->map(fn ($c) => array_merge(
            ['fecha' => $c->fecha_consulta->format('Y-m-d')],
            collect(array_keys(Medicion::INDICADORES))
                ->mapWithKeys(fn ($k) => [$k => $c->medicion->{$k}])
                ->all()
        ))->values();

        $indicadores = collect(Medicion::INDICADORES)
            ->map(fn ($i, $k) => ['k' => $k, 'n' => $i[0], 'u' => $i[1], 'd' => $i[2]])
            ->values();

        return view('progreso.show', [
            'paciente' => $paciente,
            'datos' => ['indicadores' => $indicadores, 'puntos' => $puntos],
        ]);
    }
}
