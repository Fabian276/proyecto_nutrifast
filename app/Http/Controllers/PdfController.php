<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    /** Expediente del paciente: datos, última medición, plan vigente e historial. */
    public function paciente(Paciente $paciente)
    {
        $paciente->load(['consultas.medicion', 'consultas.medicamentos', 'proximaCita']);

        $pdf = Pdf::loadView('pdf.paciente', [
            'paciente' => $paciente,
            'consultas' => $paciente->consultas->reverse()->values(),
            'ultima' => $paciente->consultas->last(),
        ]);

        return $pdf->download('expediente-'.Str::slug($paciente->nombre_completo).'.pdf');
    }

    /** Hoja para entregar al paciente con lo indicado en una consulta. */
    public function consulta(Paciente $paciente, Consulta $consulta)
    {
        $consulta->load(['medicion', 'medicamentos']);
        $paciente->load('proximaCita');

        $pdf = Pdf::loadView('pdf.consulta', [
            'paciente' => $paciente,
            'consulta' => $consulta,
            'nutriologo' => auth()->user()->nombre,
        ]);

        return $pdf->download('consulta-'.Str::slug($paciente->nombre_completo).'-'.$consulta->fecha_consulta->format('Y-m-d').'.pdf');
    }
}
