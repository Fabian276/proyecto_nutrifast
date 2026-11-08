@extends('pdf._layout')
@section('title', 'Expediente del paciente')

@section('content')
@php
  $med = $ultima?->medicion;
  $fmt = fn ($v, $dec = 1) => $v === null ? '—' : number_format($v, $dec);
@endphp

<h2>Datos del paciente</h2>
<table class="grid">
  <tr><td class="label">Nombre</td><td>{{ $paciente->nombre_completo }}</td></tr>
  <tr><td class="label">Fecha de nacimiento</td><td>{{ $paciente->fecha_nacimiento->format('d/m/Y') }} ({{ $paciente->edad }} años)</td></tr>
  <tr><td class="label">Sexo</td><td>{{ $paciente->sexo === 'F' ? 'Femenino' : 'Masculino' }}</td></tr>
  <tr><td class="label">Estatura</td><td>{{ number_format($paciente->estatura_m, 2) }} m</td></tr>
  <tr><td class="label">Teléfono</td><td>{{ $paciente->telefono ?: '—' }}</td></tr>
  <tr><td class="label">Correo</td><td>{{ $paciente->correo ?: '—' }}</td></tr>
  <tr><td class="label">Próxima cita</td><td>{{ $paciente->proximaCita ? $paciente->proximaCita->fecha_hora->format('d/m/Y H:i') : 'Sin cita programada' }}</td></tr>
</table>

<h2>Antecedentes</h2>
<div class="box">{{ $paciente->antecedentes ?: 'Sin antecedentes registrados.' }}</div>

@if($med)
<h2>Última medición ({{ $ultima->fecha_consulta->format('d/m/Y') }})</h2>
<table class="grid">
  @foreach(\App\Models\Medicion::INDICADORES as $clave => [$nombre, $unidad, $dec])
    @if($med->{$clave} !== null)
      <tr><td class="label">{{ $nombre }}</td><td>{{ $fmt($med->{$clave}, $dec) }} {{ $unidad }}</td></tr>
    @endif
  @endforeach
</table>
@endif

<h2>Plan de alimentación vigente</h2>
<div class="box">{{ $ultima?->plan_alimentacion ?: 'Sin plan registrado.' }}</div>

<h2>Medicamentos de la última consulta</h2>
@if($ultima && $ultima->medicamentos->isNotEmpty())
  <table class="data">
    <thead><tr><th>Medicamento</th><th>Dosis</th><th>Frecuencia</th></tr></thead>
    <tbody>
      @foreach($ultima->medicamentos as $m)
        <tr><td>{{ $m->nombre }}</td><td>{{ $m->dosis ?: '—' }}</td><td>{{ $m->frecuencia ?: '—' }}</td></tr>
      @endforeach
    </tbody>
  </table>
@else
  <p class="muted">Sin medicamentos registrados.</p>
@endif

<h2>Historial de consultas</h2>
@if($consultas->isEmpty())
  <p class="muted">Sin consultas registradas.</p>
@else
  <table class="data">
    <thead><tr><th>Fecha</th><th class="num">Peso (kg)</th><th class="num">IMC</th><th class="num">% grasa</th><th>Observaciones</th></tr></thead>
    <tbody>
      @foreach($consultas as $c)
        <tr>
          <td>{{ $c->fecha_consulta->format('d/m/Y') }}</td>
          <td class="num">{{ $fmt($c->medicion?->peso) }}</td>
          <td class="num">{{ $fmt($c->medicion?->imc) }}</td>
          <td class="num">{{ $fmt($c->medicion?->porcentaje_grasa) }}</td>
          <td>{{ $c->observaciones ?: '—' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
@endsection
