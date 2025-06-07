<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Sistema</title>
    <link rel="stylesheet" href="{{ asset('css/PagInicio.css') }}">
</head>
<body>
  @php
    use App\Models\Inventario;
    use App\Models\Arqueo;

    $userCount = Inventario::join('productos','inventario.id_producto','=','productos.id_producto')
                  ->whereColumn('inventario.cantidad_stock','<','productos.stock_minimo')
                  ->count();

    $abierto  = Arqueo::whereNull('monto_final')->first();
    $disabled = is_null($abierto);
  @endphp

  <div class="dashboard">
    <img src="{{ asset('imagenes/logo_quinteros.png') }}" alt="Mi Logo" class="dashboard-logo">
    <h2>¡Hola, {{ Auth::user()->nombre }}!</h2>

    @unless($abierto)
      <div class="alert-arqueo">
        Debes iniciar un arqueo primero para habilitar las acciones.
      </div>
    @endunless

    <div class="grid mt-4">
      @if(in_array(auth()->user()->rol,['admin','facturador']))
        <a href="{{ $disabled ? '#' : route('factura') }}"
           class="card-link {{ $disabled ? 'disabled' : '' }}"
           @if($disabled) aria-disabled="true" title="Inicia un arqueo primero" @endif>
          <span class="card-emoji">📄</span>
          <span class="card-text">Facturación</span>
        </a>
      @endif

      @if(in_array(auth()->user()->rol,['admin','bodeguero']))
        <a href="{{ $disabled ? '#' : route('productos') }}"
           class="card-link {{ $disabled ? 'disabled' : '' }}"
           @if($disabled) aria-disabled="true" title="Inicia un arqueo primero" @endif>
          <span class="card-emoji">📦</span>
          <span class="card-text">Productos</span>
        </a>
      @endif

      @if(auth()->user()->rol === 'admin')
        <a href="{{ $disabled ? '#' : route('personal.index') }}"
           class="card-link {{ $disabled ? 'disabled' : '' }}"
           @if($disabled) aria-disabled="true" title="Inicia un arqueo primero" @endif>
          <span class="card-emoji">👥</span>
          <span class="card-text">Usuarios</span>
        </a>
      @endif

      @if(in_array(auth()->user()->rol,['admin','facturador']))
        <a href="{{ route('arqueo.index') }}" class="card-link">
          <span class="card-emoji">💰</span>
          <span class="card-text">Arqueo</span>
        </a>
      @endif



    </div>
  </div>

  <div class="fab" id="fabToggle">
    ☰
    @if($userCount)
      <span class="badge-fab">{{ $userCount }}</span>
    @endif
  </div>

  <div class="fab-menu" id="fabMenu">
    <a href="{{ route('notificaciones') }}" title="Notificaciones">
      🔔
      @if($userCount)
        <span class="badge">{{ $userCount }}</span>
      @endif
    </a>

    @if(auth()->user()->rol === 'admin')
      <a href="{{ $disabled ? '#' : route('filtros.index') }}"
         class="{{ $disabled ? 'disabled' : '' }}"
         title="{{ $disabled ? 'Inicia un arqueo primero' : 'Filtro administrativo' }}"
         @if($disabled) aria-disabled="true" @endif>
        🔍
      </a>
    @endif

    @if(in_array(auth()->user()->rol,['admin','bodeguero']))
      <a href="{{ $disabled ? '#' : route('movimientos.index') }}"
         class="{{ $disabled ? 'disabled' : '' }}"
         title="{{ $disabled ? 'Inicia un arqueo primero' : 'Movimientos' }}"
         @if($disabled) aria-disabled="true" @endif>
        📈
      </a>
    @endif

@if(in_array(auth()->user()->rol, ['admin', 'facturador']))
  <a href="{{ route('libro-ventas.index') }}"
     class="asistencia btn btn-success"
     title="Libro de Ventas">
    📘
  </a>
@endif

        @if(in_array(auth()->user()->rol,['admin']))
     <a href="{{ route('backup.manager') }}"
         class="asistencia btn {{ $disabled ?? false ? 'disabled' : 'btn-success' }}"
         title="{{ ($disabled ?? false) ? 'Inicia un arqueo primero' : 'Respaldo' }}"
         @if($disabled ?? false) aria-disabled="true" @endif>
        📁🔙
      </a>

    @endif

    <a href="#"
       class="logout"
       title="Salir"
       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
      ⏻
    </a>
  </div>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
  </form>

  <script>
    document.getElementById('fabToggle')
      .addEventListener('click', () =>
        document.getElementById('fabMenu').classList.toggle('show')
      );
      
  </script>
</body>

</html>
