
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Confirma tu Contraseña | MotoRepuestos Quinteros</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #eef5ff;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .confirm-card {
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 420px;
    }
    .confirm-card h2 {
      color: #4f46e5;
      margin-bottom: 1.5rem;
      text-align: center;
    }
    .btn-primary {
      background: #4f46e5;
      border: none;
    }
    .btn-primary:hover {
      background: #3e3cb8;
    }
    .btn-link {
      font-size: .9rem;
      color: #4f46e5;
    }
    .btn-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="confirm-card">
    <h2>🔒 Confirma tu Contraseña</h2>
    <p>Por favor confirma tu contraseña antes de continuar.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
      @csrf

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input
          id="password"
          type="password"
          name="password"
          class="form-control @error('password') is-invalid @enderror"
          required autocomplete="current-password"
          placeholder="••••••••"
        >
        @error('password')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>

      <div class="d-grid mb-2">
        <button type="submit" class="btn btn-primary">Confirmar Contraseña</button>
      </div>

      @if (Route::has('password.request'))
        <div class="text-center">
          <a href="{{ route('password.request') }}" class="btn btn-link">
            ¿Olvidaste tu contraseña?
          </a>
        </div>
      @endif
    </form>
  </div>

</body>
</html>
