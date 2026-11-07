<?php

namespace App\Http\Controllers;

use App\Http\Requests\PacienteRequest;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $pacientes = Paciente::query()
            ->with(['ultimaConsulta.medicion', 'proximaCita'])
            ->when($q !== '', function ($query) use ($q) {
                // La colación utf8mb4_unicode_ci ignora acentos y mayúsculas.
                $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q).'%';
                $query->whereRaw("CONCAT(nombre, ' ', apellidos) LIKE ?", [$like]);
            })
            ->orderBy('apellidos')->orderBy('nombre')
            ->get();

        return view('pacientes.index', [
            'pacientes' => $pacientes,
            'q' => $q,
            'total' => Paciente::count(),
        ]);
    }

    public function store(PacienteRequest $request): RedirectResponse
    {
        $paciente = Paciente::create($request->validated());

        return redirect()->route('pacientes.show', $paciente)->with('status', 'Paciente registrado.');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load(['consultas.medicion', 'consultas.medicamentos', 'proximaCita']);

        $consultas = $paciente->consultas;          // ya vienen en orden cronológico
        $primera = $consultas->first();
        $ultima = $consultas->last();

        $cambio = null;
        if ($consultas->count() > 1 && $primera->medicion && $ultima->medicion) {
            $cambio = round($ultima->medicion->peso - $primera->medicion->peso, 1);
        }

        return view('pacientes.show', [
            'paciente' => $paciente,
            'consultas' => $consultas->reverse()->values(),   // más reciente primero
            'ultima' => $ultima,
            'primera' => $primera,
            'cambio' => $cambio,
        ]);
    }

    public function update(PacienteRequest $request, Paciente $paciente): RedirectResponse
    {
        $paciente->update($request->validated());

        return redirect()->route('pacientes.show', $paciente)->with('status', 'Datos del paciente actualizados.');
    }
}
