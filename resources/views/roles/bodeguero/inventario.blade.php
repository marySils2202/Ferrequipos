<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario Actual</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/Inventario.Css') }}">
</head>
<body>
  <div class="inv-card">
    <a href="{{ route('productos') }}" class="back-corner" title="Volver">←</a>

    <form method="GET" action="{{ route('inventario') }}" class="filter-bar">
      <select name="categoria_id" class="filter-input">
        <option value="">Todas categorías</option>
        @foreach($categorias as $cat)
          <option value="{{ $cat->id_categoria }}"
            @if(request('categoria_id') == $cat->id_categoria) selected @endif>
            {{ $cat->nombre_categoria }}
          </option>
        @endforeach
      </select>
      <input
        type="text"
        name="filtro"
        class="filter-input"
        placeholder="🔍 Buscar por Nombre o Descripción"
        value="{{ request('filtro') }}">

      <button type="submit" class="filter-btn">Filtrar</button>
<label class="filter-checkbox">
  <input 
    type="checkbox" 
    name="solo_bajo" 
    value="1"
    @if(request('solo_bajo')) checked @endif
  >
  Solo stock bajo
</label>

      <a href="{{ route('inventario') }}" class="filter-btn" style="background:#6c757d;">
        Limpiar
      </a>

      <a href="{{ route('inventario.pdf', array_merge(request()->only('filtro','categoria_id'), ['solo_bajo' => request('solo_bajo')])) }}"
         class="download-btn">
        📄 Descargar PDF
      </a>

      <button type="button" id="toggleAll" class="filter-btn" style="background:#17a2b8;">
        ↕ Mostrar todo
      </button>
    </form>

    <h2>📦 Inventario Actual</h2>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($inventarios->isEmpty())
      <div class="alert alert-warning">No hay productos en el inventario.</div>
    @else
      <div class="table-wrap collapsed">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Producto</th>
              <th>Descripción</th>
              <th>Stock</th>
              <th>Actualizado</th>
            </tr>
          </thead>
          <tbody>
            @foreach($inventarios as $item)
              @if($item->producto)
                @php
                  $prod   = $item->producto;
                  $minimo = $prod->stock_minimo;
                  $stock  = $item->cantidad_stock;
                  $level  = $stock < $minimo
                           ? 'low'
                           : ($stock <= $minimo * 1.2 ? 'warn' : 'ok');
                @endphp
                <tr class="{{ $level }}-stock">
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $prod->nombre }}</td>
                  <td>{{ $prod->descripcion }}</td>
                  <td>
                    {{ $stock }}
                    <span class="badge-stock badge-{{ $level }}">
                      @if($level == 'low') Bajo
                      @elseif($level == 'warn') Atento
                      @else Ok
                      @endif
                    </span>
                  </td>
                  <td>{{ $item->fecha_actualizacion->setTimezone('America/Managua')->format('Y-m-d H:i') }}</td>
                </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

  </div>
 <script src="{{ asset('js/Bodega/Inventario.js') }}"></script>
</body>
</html>
