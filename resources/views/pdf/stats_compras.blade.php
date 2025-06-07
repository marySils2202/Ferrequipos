<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Estadísticas de Compras</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family: sans-serif; color: #333; margin: 0; padding: 0; }
    .header { display: flex; align-items: center; padding: 20px; background: #f3f3f3; border-bottom: 2px solid #4f46e5; }
    .header img { height: 50px; margin-right: 20px; }
    .logo-placeholder { flex-shrink: 0; width: 50px; height: 50px; background: #ddd; border-radius: 50%; text-align: center; line-height: 50px; color: #888; font-size: .9rem; margin-right: 20px; }
    .header h1 { font-size: 1.8rem; color: #4f46e5; margin: 0; }
    .subtitle { text-align: center; margin: 10px 0; font-size: 1rem; color: #666; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    thead th { background: #4f46e5; color: #fff; padding: 10px; text-align: center; border: 1px solid #ddd; }
    tbody tr:nth-child(even) { background: #f9f9f9; }
    tbody td { border: 1px solid #ddd; padding: 8px; text-align: center; font-size: .9rem; }
    .footer { text-align: center; margin: 30px 0; font-size: .8rem; color: #999; }
  </style>
</head>
<body>

  <div class="header">
    @if (file_exists(public_path('imagenes/logo_quinteros.png')))
      <img src="{{ public_path('imagenes/logo_quinteros.png') }}" alt="Logo">
    @else
      <div class="logo-placeholder">Logo</div>
    @endif
    <h1>Estadísticas de Compras</h1>
  </div>

  <div class="subtitle">
    <p><strong>Filtro Producto:</strong> {{ $labelProd }}</p>
    <p><strong>Filtro Categoría:</strong> {{ $labelCat }}</p>
    <p>Generado: {{ now()->format('Y-m-d H:i') }}</p>
  </div>

  <table>
    <thead>
      <tr>
        <th>Producto</th>
        <th>Categoría</th>
        <th>Mínimo</th>
        <th>Máximo</th>
        <th>Promedio</th>
        <th># Compras</th>
      </tr>
    </thead>
    <tbody>
      @foreach($statsComprasPorProducto as $stat)
        <tr>
          <td>{{ $stat->producto->nombre }}</td>
          <td>{{ $stat->producto->categoria->nombre_categoria ?? '—' }}</td>
          <td>C${{ number_format($stat->min_precio, 2) }}</td>
          <td>C${{ number_format($stat->max_precio, 2) }}</td>
          <td>C${{ number_format($stat->avg_precio, 2) }}</td>
          <td>{{ $stat->num_registros }}</td>
        </tr>
      @endforeach
      @if($statsComprasPorProducto->isEmpty())
        <tr>
          <td colspan="6" class="text-center">No hay datos para estos filtros.</td>
        </tr>
      @endif
    </tbody>
  </table>

  <div class="footer">
    &copy; {{ date('Y') }} MotoRepuestosQuinteros – Todos los derechos reservados.
  </div>

</body>
</html>
