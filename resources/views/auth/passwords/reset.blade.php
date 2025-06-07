<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecer Contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background:#eef5ff;">
  <div class="card p-4" style="min-width:320px;">
    <h3 class="text-center mb-3">🔑 Restablecer Contraseña</h3>

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="mb-3">
        <input type="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="Correo electrónico"
               value="{{ $email ?? old('email') }}"
               required autofocus>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <input type="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Nueva contraseña"
               required>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <input type="password"
               name="password_confirmation"
               class="form-control"
               placeholder="Confirmar contraseña"
               required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Restablecer contraseña</button>
      <a href="{{ route('login') }}" class="d-block text-center mt-2">← Volver al inicio de sesión</a>
    </form>
  </div>
</body>
</html>
