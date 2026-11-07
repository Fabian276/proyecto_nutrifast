<!doctype html>
<html lang="es">
<head>
@include('layouts._head')
</head>
<body>
<main class="nf-login">
  <div class="nf-login-card">
    <div class="text-center mb-4">
      <span class="nf-brand-mark mx-auto mb-2" style="width:52px;height:52px;font-size:1.3rem;border-radius:14px" aria-hidden="true">NF</span>
      <div class="h3 fw-bold mb-0">NutriFzst</div>
      <div class="text-body-secondary">Expediente y seguimiento nutricional</div>
    </div>
    <div class="nf-card nf-card-pad p-4">
      <h1 class="h5 mb-3">Iniciar sesión</h1>
      <form method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf
        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" autocomplete="username" required autofocus>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input id="password" name="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" autocomplete="current-password" required>
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        @if(session('login_error'))
          <div class="alert alert-danger py-2" role="alert">{{ session('login_error') }}</div>
        @endif
        <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
      </form>
    </div>
  </div>
</main>
</body>
</html>
