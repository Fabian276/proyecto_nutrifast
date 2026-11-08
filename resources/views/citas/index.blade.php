@extends('layouts.app')
@section('title', 'Agenda · NutriFzst')

@section('content')
@php
  $icoWa = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.19 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35zM12.04 21.5h-.01a9.4 9.4 0 0 1-4.8-1.32l-.34-.2-3.56.93.95-3.47-.22-.36a9.4 9.4 0 0 1-1.44-5.01c0-5.2 4.23-9.43 9.43-9.43a9.4 9.4 0 0 1 9.42 9.44c0 5.2-4.23 9.42-9.43 9.42zm8.02-17.45A11.27 11.27 0 0 0 12.04.75C5.8.75.72 5.83.72 12.08c0 2 .52 3.94 1.51 5.65L.62 23.6l6.01-1.58a11.3 11.3 0 0 0 5.41 1.38h.01c6.24 0 11.32-5.08 11.33-11.33 0-3.03-1.18-5.87-3.32-8.02z"/></svg>';
@endphp
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
        @php $wa = $c->whatsappUrl(); @endphp
        <div class="nf-appt">
          <a class="nf-appt-link" href="{{ route('pacientes.show', $c->paciente) }}">
            <span class="nf-appt-time">{{ $c->fecha_hora->format('H:i') }}</span>
            <span class="nf-appt-info"><span class="fw-semibold">{{ $c->paciente->nombre_completo }}</span><br><span class="text-body-secondary small">{{ $c->motivo ?: 'Sin motivo' }}</span></span>
            <span class="badge badge-prog">Programada</span>
          </a>
          @if($wa)
            <a class="btn btn-sm btn-wa" href="{{ $wa }}" target="_blank" rel="noopener" title="Enviar recordatorio por WhatsApp a {{ $c->paciente->telefono }}">{!! $icoWa !!}<span class="d-none d-md-inline">Recordar</span></a>
          @else
            <span class="btn btn-sm btn-outline-secondary disabled" title="El paciente no tiene un teléfono válido registrado">Sin teléfono</span>
          @endif
        </div>
      @endforeach
    </div>
  @empty
    <div class="nf-card nf-empty">No hay citas programadas {{ $rango === 'hoy' ? 'para hoy' : ($rango === 'semana' ? 'en los próximos 7 días' : '') }}.</div>
  @endforelse

  <p class="small text-body-secondary mt-3">Las citas se registran al final del formulario de consulta.</p>
</main>
@endsection
