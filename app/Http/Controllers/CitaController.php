<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $rango = in_array($request->query('rango'), ['hoy', 'semana', 'todas'], true)
            ? $request->query('rango') : 'semana';

        $desde = now()->startOfDay();

        $citas = Cita::with('paciente')
            ->where('estado', 'programada')
            ->where('fecha_hora', '>=', $desde)
            ->when($rango === 'hoy', fn ($q) => $q->where('fecha_hora', '<', $desde->copy()->addDay()))
            ->when($rango === 'semana', fn ($q) => $q->where('fecha_hora', '<', $desde->copy()->addDays(7)))
            ->orderBy('fecha_hora')
            ->get()
            ->groupBy(fn ($c) => $c->fecha_hora->format('Y-m-d'));

        return view('citas.index', compact('citas', 'rango'));
    }
}
