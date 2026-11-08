<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultaRequest;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsultaController extends Controller
{
    public function create(Paciente $paciente)
    {
        $paciente->load('proximaCita');

        return view('consultas.form', [
            'paciente' => $paciente,
            'consulta' => null,
            'anterior' => $paciente->consultas()->with('medicion')->get()->last(),
        ]);
    }

    public function store(ConsultaRequest $request, Paciente $paciente): RedirectResponse
    {
        $datos = $request->validated();

        // El IMC SIEMPRE se calcula en el servidor con la estatura registrada.
        $imc = round($datos['peso'] / ($paciente->estatura_m ** 2), 2);
        if ($imc <= 0 || $imc > 90) {
            throw ValidationException::withMessages([
                'peso' => 'El peso no es compatible con la estatura registrada (IMC fuera de rango).',
            ]);
        }

        $medicamentos = collect($datos['medicamentos'] ?? [])
            ->map(fn ($m) => [
                'nombre' => trim((string) ($m['nombre'] ?? '')),
                'dosis' => trim((string) ($m['dosis'] ?? '')) ?: null,
                'frecuencia' => trim((string) ($m['frecuencia'] ?? '')) ?: null,
            ])
            ->filter(fn ($m) => $m['nombre'] !== '');

        $consulta = DB::transaction(function () use ($datos, $paciente, $imc, $medicamentos) {
            $consulta = $paciente->consultas()->create([
                'fecha_consulta' => $datos['fecha'],
                'observaciones' => $datos['observaciones'] ?? null,
                'plan_alimentacion' => $datos['plan'] ?? null,
            ]);

            $consulta->medicion()->create([
                'peso' => $datos['peso'],
                'imc' => $imc,
                'porcentaje_grasa' => $datos['porcentaje_grasa'] ?? null,
                'agua_corporal' => $datos['agua_corporal'] ?? null,
                'masa_muscular' => $datos['masa_muscular'] ?? null,
                'masa_osea' => $datos['masa_osea'] ?? null,
                'grasa_visceral' => $datos['grasa_visceral'] ?? null,
                'tasa_metabolica_basal' => $datos['tasa_metabolica_basal'] ?? null,
                'edad_metabolica' => $datos['edad_metabolica'] ?? null,
                'valoracion_fisica' => $datos['valoracion_fisica'] ?? null,
            ]);

            foreach ($medicamentos as $m) {
                $consulta->medicamentos()->create($m);
            }

            if (! empty($datos['cita_fecha'])) {
                // Una sola próxima cita vigente: la nueva reemplaza a las programadas pendientes.
                Cita::where('paciente_id', $paciente->id)
                    ->where('estado', 'programada')
                    ->where('fecha_hora', '>', now())
                    ->update(['estado' => 'cancelada']);

                $paciente->citas()->create([
                    'fecha_hora' => $datos['cita_fecha'],
                    'motivo' => $datos['cita_motivo'] ?? null,
                    'estado' => 'programada',
                ]);
            }

            return $consulta;
        });

        $aviso = 'Consulta guardada'.(! empty($datos['cita_fecha']) ? ' y próxima cita registrada.' : '.');

        // "Guardar y descargar PDF": se abre la consulta guardada y desde ahí se descarga la hoja.
        if ($request->input('accion') === 'pdf') {
            return redirect()->route('consultas.show', [$paciente, $consulta])
                ->with('status', $aviso)->with('descargar_pdf', true);
        }

        return redirect()->route('pacientes.show', $paciente)->with('status', $aviso);
    }

    /** Consulta ya guardada: sólo lectura. */
    public function show(Paciente $paciente, Consulta $consulta)
    {
        $consulta->load(['medicion', 'medicamentos']);

        $anterior = $paciente->consultas()->with('medicion')
            ->where('fecha_consulta', '<', $consulta->fecha_consulta)->get()->last();

        return view('consultas.form', compact('paciente', 'consulta', 'anterior'));
    }
}
