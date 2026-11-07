@extends('layouts.app')
@section('title', 'Agenda · NutriFzst')

@section('content')
<main class="nf-content">
  <div class="nf-page-head">
    <div><h1>Agenda</h1><div class="nf-sub">Próximas citas programadas</div></div>
    <div class="btn-group" role="group" aria-label="Periodo">
      @foreach(['hoy' => 'Hoy', 'semana' => '7 días', 'todas' => 'Todas'] as $clave => $texto)
        <a href="{{ route('citas.index', ['rango' => $clave]) }}" class="btn {{ $rango === $clave ? 'btn-primary' : 'btn-outline-primary' }}" aria-pressed="{{ $rango === $clave ? 'true' : 'false' }}">{{ $texto }}</a>
      @endforeach
    </div>
  </div>

  @forelse($citas as $dia => $delDia)
    <div class="nf-day">{{ \Illuminate\Support\Str::ucfirst($delDia->first()->fecha_hora->locale('es')->isoFormat('dddd D [de] MMMM')) }}</div>
    <div class="nf-card">
      @foreach($delDia as $c)
        <a class="nf-appt" href="{{ route('pacientes.show', $c->paciente) }}">
          <span class="nf-appt-time">{{ $c->fecha_hora->format('H:i') }}</span>
          <span class="flex-grow-1"><span class="fw-semibold">{{ $c->paciente->nombre_completo }}</span><br><span class="text-body-secondary small">{{ $c->motivo ?: 'Sin motivo' }}</span></span>
          <span class="badge badge-prog">Programada</span>
        </a>
      @endforeach
    </div>
  @empty
    <div class="nf-card nf-empty">No hay citas programadas {{ $rango === 'hoy' ? 'para hoy' : ($rango === 'semana' ? 'en los próximos 7 días' : '') }}.</div>
  @endforelse

  <p class="small text-body-secondary mt-3">Las citas se registran al final del formulario de consulta.</p>
</main>
@endsection
