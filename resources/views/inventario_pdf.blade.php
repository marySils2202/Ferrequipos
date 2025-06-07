<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario Actual</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body {
      font-family: sans-serif;
      color: #333;
      margin: 0; padding: 0;
    }
    .header {
      display: flex; align-items: center;
      padding: 20px;
      background: #f3f3f3;
      border-bottom: 2px solid #4f46e5;
    }
    .header img {
      height: 50px; margin-right: 20px;
    }
    .logo-placeholder {
      flex-shrink: 0;
      width: 50px; height: 50px;
      background: #ddd;
      border-radius: 50%;
      text-align: center;
      line-height: 50px;
      color: #888;
      font-size: 0.9rem;
      margin-right: 20px;
    }
    .header h1 {
      font-size: 1.8rem;
      color: #4f46e5;
      margin: 0;
    }
    .subtitle {
      text-align: center;
      margin: 10px 0;
      font-size: 1rem;
      color: #666;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px;
    }
    thead th {
      background: #4f46e5;
      color: #fff;
      padding: 10px;
      text-align: center;
      font-weight: normal;
      border: 1px solid #ddd;
    }
    tbody tr:nth-child(even) {
      background: #f9f9f9;
    }
    tbody td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
      font-size: 0.9rem;
    }
    .footer {
      text-align: center;
      margin: 30px 0;
      font-size: 0.8rem;
      color: #999;
    }
  </style>
</head>
<body>

  <div class="header">
    @if (file_exists(public_path('imagenes/logo_quinteros.png')))
      <img src="{{ public_path('imagenes/logo_quinteros.png') }}" alt="Logo">
    @else
      <div class="logo-placeholder">Logo</div>
    @endif
    <h1>Inventario Actual</h1>
  </div>

  <div class="subtitle">
    Generado: {{ now()->format('Y-m-d H:i') }}
    @if(request()->filled('solo_bajo'))
      <br><em>Mostrando sólo productos con stock bajo</em>
    @endif
  </div>

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
      @php $contador = 0; @endphp

      @foreach($inventarios as $item)
        @php

          $prod   = $item->producto;
          $minimo = $prod->stock_minimo;
          $stock  = $item->cantidad_stock;
        @endphp


        @if(! request()->filled('solo_bajo') || $stock < $minimo)
          @php $contador++; @endphp
          <tr>
            <td>{{ $contador }}</td>
            <td>{{ $prod->nombre }}</td>
            <td>{{ $prod->descripcion }}</td>
            <td>{{ $stock }}</td>
            <td>{{ $item->fecha_actualizacion->format('Y-m-d H:i') }}</td>
          </tr>
        @endif
      @endforeach

      @if(request()->filled('solo_bajo') && $contador === 0)
        <tr>
          <td colspan="5" style="padding: 20px; text-align: center;">
            No hay productos con stock por debajo del mínimo.
          </td>
        </tr>
      @endif
    </tbody>
  </table>

  <div class="footer">
    &copy; {{ date('Y') }} MotoRepuestosQuinteros – Todos los derechos reservados.
  </div>

</body>
</html>
