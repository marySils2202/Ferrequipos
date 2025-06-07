
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenido | MotoRepuestos Quinteros</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    
  >
  <link rel="stylesheet" href="{{ asset('css/Home.css') }}">
</head>
<body>

  <div class="bienvenida-box">

    <div class="logo-empresa">
      @if(file_exists(public_path('imagenes/logo_quinteros.png')))
        <img src="{{ asset('imagenes/logo_quinteros.png') }}" alt="Logo MotoRepuestos Quinteros">
      @else
        <img src="https://via.placeholder.com/160?text=Logo" alt="Logo">
      @endif
    </div>

    <h1>Bienvenido</h1>

    @guest
      <p>Accede al sistema.</p>
      <a href="{{ route('login') }}" class="btn btn-primary">Iniciar Sesión</a>
    @endguest

    @auth
      <p>Has iniciado sesión como <strong>{{ Auth::user()->nombre }}</strong>.</p>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
      </form>
    @endauth

  </div>

</body>
</html>
