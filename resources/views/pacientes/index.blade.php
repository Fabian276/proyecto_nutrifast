@extends('layouts.app')
@section('title', 'Pacientes · NutriFzst')

@section('content')
<main class="nf-content">
  <div class="nf-page-head">
    <div><h1>Pacientes</h1><div class="nf-sub">{{ $total }} {{ $total === 1 ? 'paciente registrado' : 'pacientes registrados' }}</div></div>
    <button type="button" class="btn btn-primary btn-lg" data-abrir-modal>+ Nuevo paciente</button>
  </div>

  <form method="GET" action="{{ route('pacientes.index') }}" role="search" id="form-buscar">
    <label for="buscar" class="visually-hidden">Buscar paciente</label>
    <input id="buscar" name="q" type="search" value="{{ $q }}" class="form-control form-control-lg mb-3" placeholder="Buscar por nombre o apellidos…" autocomplete="off" @if($q !== '') autofocus @endif>
  </form>

  <div class="nf-card overflow-hidden">
    @if($pacientes->isNotEmpty())
      <div class="table-responsive">
        <table class="table nf-table" id="tabla-pacientes">
          <thead><tr><th>Paciente</th><th>Edad</th><th>Última consulta</th><th>Último peso</th><th>Próxima cita</th></tr></thead>
          <tbody>
          @foreach($pacientes as $p)
            @php $ult = $p->ultimaConsulta; @endphp
            <tr class="nf-row-link" tabindex="0" data-href="{{ route('pacientes.show', $p) }}">
              <td><a href="{{ route('pacientes.show', $p) }}" class="fw-semibold text-reset text-decoration-none">{{ $p->nombre_completo }}</a></td>
              <td class="num">{{ $p->edad }}</td>
              <td>{!! $ult ? e($ult->fecha_consulta->format('d/m/Y')) : '<span class="text-body-secondary">Sin consultas</span>' !!}</td>
              <td class="num">{{ $ult?->medicion ? number_format($ult->medicion->peso, 1).' kg' : '—' }}</td>
              <td>{{ $p->proximaCita ? $p->proximaCita->fecha_hora->format('d/m/Y H:i') : '—' }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="nf-empty">
        <p class="mb-3">{{ $q !== '' ? 'No se encontró ningún paciente con “'.$q.'”.' : 'Aún no hay pacientes registrados.' }}</p>
        <button type="button" class="btn btn-outline-primary" data-abrir-modal data-prellenar="{{ $q }}">Dar de alta a este paciente</button>
      </div>
    @endif
  </div>
</main>
@include('pacientes._form', ['paciente' => null])
@endsection
