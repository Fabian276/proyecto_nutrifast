{{-- Sirve para consulta nueva ($consulta = null) y para ver una anterior en sólo lectura. --}}
@extends('layouts.app')
@php
  $ro = (bool) $consulta;
  $m = $consulta?->medicion;
  $campo = fn (string $nombre, $delModelo = null) => $ro ? $delModelo : old($nombre);
  $dis = $ro ? 'disabled' : '';
  $opcionales = [
    ['porcentaje_grasa', '% grasa corporal', '0.1'],
    ['agua_corporal', '% agua corporal', '0.1'],
    ['masa_muscular', 'Masa muscular (kg)', '0.1'],
    ['masa_osea', 'Masa ósea (kg)', '0.1'],
    ['grasa_visceral', 'Grasa visceral (nivel)', '1'],
    ['tasa_metabolica_basal', 'Tasa metabólica basal (kcal)', '1'],
    ['edad_metabolica', 'Edad metabólica (años)', '1'],
    ['valoracion_fisica', 'Valoración física (1–9)', '1'],
  ];
  $hayOpcional = collect($opcionales)->contains(fn ($o) => $campo($o[0], $m?->{$o[0]}) !== null && $campo($o[0], $m?->{$o[0]}) !== '')
      || collect($opcionales)->contains(fn ($o) => $errors->has($o[0]));
  $filasMed = $ro
      ? $consulta->medicamentos->map(fn ($x) => ['nombre' => $x->nombre, 'dosis' => $x->dosis, 'frecuencia' => $x->frecuencia])->all()
      : array_values(old('medicamentos', []));
  $fechaVal = $ro ? $consulta->fecha_consulta->format('Y-m-d\TH:i') : old('fecha', now()->format('Y-m-d\TH:i'));
  $citaActual = $paciente->proximaCita ?? null;
@endphp
@section('title', ($ro ? 'Consulta' : 'Nueva consulta').' · '.$paciente->nombre_completo.' · NutriFzst')

@section('content')
<main class="nf-content">
  <nav aria-label="Ruta"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('pacientes.index') }}">Pacientes</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pacientes.show', $paciente) }}">{{ $paciente->nombre_completo }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Consulta</li>
  </ol></nav>
  <div class="nf-page-head"><div>
    <h1>{{ $ro ? 'Consulta del '.$consulta->fecha_consulta->format('d/m/Y') : 'Nueva consulta' }}</h1>
    <div class="nf-sub">{{ $paciente->nombre_completo }}</div>
  </div></div>

  @if($ro)<div class="alert alert-secondary">Consulta guardada (sólo lectura).</div>@endif

  <form id="form-consulta" method="POST" action="{{ route('consultas.store', $paciente) }}" novalidate data-estatura="{{ $paciente->estatura_m }}">
    @csrf
    <section class="nf-section">
      <h2><span class="nf-step">1</span>Mediciones</h2>
      <div class="row g-3 align-items-start">
        <div class="col-6 col-md-3">
          <label for="peso" class="form-label req">Peso (kg)</label>
          <input id="peso" name="peso" type="number" step="0.1" min="0.1" max="400" inputmode="decimal" value="{{ $campo('peso', $m?->peso) }}" class="form-control form-control-lg nf-key-input @error('peso') is-invalid @enderror" {{ $dis }}>
          @error('peso')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6 col-md-3">
          <label for="imc" class="form-label">IMC</label>
          <input id="imc" type="text" value="{{ $ro && $m ? number_format($m->imc, 2) : '' }}" class="form-control form-control-lg nf-key-input bg-body-tertiary" readonly tabindex="-1">
          <div class="form-text">Estatura registrada: {{ number_format($paciente->estatura_m, 2) }} m</div>
        </div>
        <div class="col-md-6"><div class="nf-prev mt-md-4 pt-md-2">
          @if($anterior && $anterior->medicion)
            Anterior ({{ $anterior->fecha_consulta->format('d/m/Y') }}): {{ number_format($anterior->medicion->peso, 1) }} kg · IMC {{ number_format($anterior->medicion->imc, 1) }}
          @else
            Primera consulta del paciente.
          @endif
        </div></div>
      </div>

      <details class="nf-optional mt-3" @if($hayOpcional) open @endif>
        <summary>Composición corporal (opcional)</summary>
        <div class="row g-3 mt-1">
          @foreach($opcionales as [$nombre, $etiqueta, $paso])
            <div class="col-6 col-md-3">
              <label for="m-{{ $nombre }}" class="form-label small">{{ $etiqueta }}</label>
              <input id="m-{{ $nombre }}" name="{{ $nombre }}" type="number" step="{{ $paso }}" inputmode="decimal" value="{{ $campo($nombre, $m?->{$nombre}) }}" class="form-control @error($nombre) is-invalid @enderror" {{ $dis }}>
              @error($nombre)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          @endforeach
        </div>
      </details>
    </section>

    <section class="nf-section">
      <h2><span class="nf-step">2</span>Datos de la consulta</h2>
      <div class="row g-3">
        <div class="col-md-4">
          <label for="fecha" class="form-label req">Fecha y hora</label>
          <input id="fecha" name="fecha" type="datetime-local" value="{{ $fechaVal }}" class="form-control @error('fecha') is-invalid @enderror" {{ $dis }}>
          @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
          <label for="observaciones" class="form-label">Observaciones</label>
          <textarea id="observaciones" name="observaciones" rows="2" class="form-control" {{ $dis }}>{{ $campo('observaciones', $consulta?->observaciones) }}</textarea>
        </div>
      </div>
    </section>

    <section class="nf-section">
      <h2><span class="nf-step">3</span>Plan de alimentación</h2>
      <label for="plan" class="visually-hidden">Plan de alimentación</label>
      <textarea id="plan" name="plan" rows="5" class="form-control" {{ $dis }}
        @if(! $ro && $anterior?->plan_alimentacion) placeholder="Plan anterior: {{ $anterior->plan_alimentacion }}" @endif>{{ $campo('plan', $consulta?->plan_alimentacion) }}</textarea>
    </section>

    <section class="nf-section">
      <h2><span class="nf-step">4</span>Medicamentos registrados</h2>
      <div id="lista-medicamentos">
        @foreach($filasMed as $i => $f)
          <div class="row g-2 align-items-end mb-2 nf-med">
            <div class="col-md-4"><label class="form-label small">Medicamento</label><input class="form-control" name="medicamentos[{{ $i }}][nombre]" value="{{ $f['nombre'] ?? '' }}" {{ $dis }}></div>
            <div class="col-6 col-md-3"><label class="form-label small">Dosis</label><input class="form-control" name="medicamentos[{{ $i }}][dosis]" value="{{ $f['dosis'] ?? '' }}" {{ $dis }}></div>
            <div class="col-6 col-md-3"><label class="form-label small">Frecuencia</label><input class="form-control" name="medicamentos[{{ $i }}][frecuencia]" value="{{ $f['frecuencia'] ?? '' }}" {{ $dis }}></div>
            @unless($ro)<div class="col-md-2"><button type="button" class="btn btn-outline-secondary w-100" data-quitar>Quitar</button></div>@endunless
          </div>
        @endforeach
      </div>
      @unless($ro)<button type="button" class="btn btn-sm btn-outline-primary" id="btn-agregar-med" data-siguiente="{{ count($filasMed) ? max(array_keys($filasMed)) + 1 : 0 }}">+ Agregar medicamento</button>@endunless
    </section>

    @unless($ro)
    <section class="nf-section">
      <h2><span class="nf-step">5</span>Próxima cita</h2>
      <div class="row g-3">
        <div class="col-md-4">
          <label for="cita_fecha" class="form-label">Fecha y hora</label>
          <input id="cita_fecha" name="cita_fecha" type="datetime-local" value="{{ old('cita_fecha') }}" class="form-control @error('cita_fecha') is-invalid @enderror">
          @error('cita_fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
          <label for="cita_motivo" class="form-label">Motivo</label>
          <input id="cita_motivo" name="cita_motivo" value="{{ old('cita_motivo') }}" class="form-control" placeholder="Seguimiento" maxlength="150">
        </div>
      </div>
      @if($citaActual)<div class="form-text">Cita ya programada: {{ $citaActual->fecha_hora->format('d/m/Y H:i') }}. Si capturas otra fecha, se reemplaza.</div>@endif
    </section>
    @endunless
  </form>
</main>
@endsection

@section('savebar')
<div class="nf-savebar">
  @unless($ro)<span class="me-auto align-self-center small text-body-secondary d-none d-md-inline">Ctrl + Enter para guardar</span>@endunless
  <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-outline-secondary btn-lg">{{ $ro ? 'Volver al expediente' : 'Cancelar' }}</a>
  @unless($ro)<button type="submit" form="form-consulta" class="btn btn-primary btn-lg px-5">Guardar consulta</button>@endunless
</div>
@endsection

@push('scripts')
@unless($ro)<script src="{{ asset('js/consulta.js') }}"></script>@endunless
@endpush
