<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestor de Backups</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @livewireStyles
  <link rel="stylesheet" href="{{ asset('css/Respaldo.css') }}">
</head>
<body>
  <a href="{{ route('sistema') }}"
     class="back-corner"
     title="Volver a Inicio">
    ←
  </a>

  <div class="livewire-wrapper">
    <livewire:db-manager />
  </div>
  @livewireScripts
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
