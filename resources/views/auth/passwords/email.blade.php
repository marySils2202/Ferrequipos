<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background: #eef5ff">
  <div class="card p-4" style="width: 360px; border-radius: 12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
    <h3 class="text-center mb-3" style="color:#4f46e5">🔒 Recuperar Contraseña</h3>

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="mb-3">
        <input type="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="Tu correo registrado"
               value="{{ old('email') }}"
               required autofocus>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <button class="btn btn-primary w-100">Enviar enlace de recuperación</button>

      <a href="{{ route('login') }}" class="d-block text-center mt-3" style="color:#6c757d">
        ← Volver al inicio de sesión
      </a>
    </form>
  </div>
</body>
</html>
