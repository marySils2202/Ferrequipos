<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión | MotoRepuestos Quinteros</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/Login.css') }}">
</head>
<body>
  <div class="login-card">
    <div class="logo-wrapper">
      @if(file_exists(public_path('imagenes/logo_quinteros.png')))
        <img src="{{ asset('imagenes/logo_quinteros.png') }}" alt="Logo">
      @else
        <img src="https://via.placeholder.com/200?text=Logo" alt="Logo">
      @endif
    </div>
    @if(session('warning'))
  <div class="alert alert-warning">
    {{ session('warning') }}
  </div>
@endif

    <h2 class="login-title">🔐 Iniciar Sesión</h2>

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <input type="text"
             name="username"
             class="form-control"
             placeholder="Nombre de usuario"
             value="{{ old('username') }}"
             required
             autofocus>

      <input type="password"
             name="password"
             class="form-control"
             placeholder="Contraseña"
             required>

      <button type="submit" class="btn btn-primary btn-login">Entrar</button>

      <a href="{{ route('password.request') }}" class="forgot-password">
        ¿Olvidaste tu contraseña?
      </a>

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </form>
  </div>
</body>
</html>
