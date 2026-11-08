@extends('pdf._layout')
@section('title', 'Indicaciones de la consulta del '.$consulta->fecha_consulta->format('d/m/Y'))

@section('content')
@php $med = $consulta->medicion; @endphp

@if($med)
<h2>Tus mediciones de hoy</h2>
<table class="grid">
  @foreach(\App\Models\Medicion::INDICADORES as $clave => [$nombre, $unidad, $dec])
    @if($med->{$clave} !== null)
      <tr><td class="label">{{ $nombre }}</td><td>{{ number_format($med->{$clave}, $dec) }} {{ $unidad }}</td></tr>
    @endif
  @endforeach
</table>
@endif

<h2>Plan de alimentación</h2>
<div class="box">{{ $consulta->plan_alimentacion ?: 'Sin plan registrado en esta consulta.' }}</div>

@if($consulta->medicamentos->isNotEmpty())
<h2>Medicamentos y suplementos</h2>
<table class="data">
  <thead><tr><th>Medicamento</th><th>Dosis</th><th>Frecuencia</th></tr></thead>
  <tbody>
    @foreach($consulta->medicamentos as $m)
      <tr><td>{{ $m->nombre }}</td><td>{{ $m->dosis ?: '—' }}</td><td>{{ $m->frecuencia ?: '—' }}</td></tr>
    @endforeach
  </tbody>
</table>
@endif

<h2>Próxima cita</h2>
<p>{{ $paciente->proximaCita ? \Illuminate\Support\Str::ucfirst($paciente->proximaCita->fecha_hora->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY, HH:mm [hrs]')) : 'Por agendar.' }}</p>

<div class="firma">{{ $nutriologo }}<br><span class="muted">Nutriólogo(a)</span></div>
@endsection
