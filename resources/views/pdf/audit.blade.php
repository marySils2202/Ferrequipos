<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Auditoría {{ $hoy6am->format('Y-m-d H:i') }} → {{ $maniana6am->format('Y-m-d H:i') }}</title>
  <style>
    @page { margin: 0.5cm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; color: #333; font-size: 0.85rem; }
    .header {
      text-align: center;
      padding: 1rem 0;
      background: #f3f3f3;
      border-bottom: 3px solid #4f46e5;
    }
    .header h1 { color: #4f46e5; font-size: 1.6rem; }
    .subtitle, .window {
      text-align: center;
      margin: 0.5rem 0;
      color: #555;
      font-size: 0.9rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 1rem 0;
    }
    thead th {
      background: #4f46e5;
      color: #fff;
      padding: 0.6rem;
      border: 1px solid #ddd;
      text-align: left;
    }
    tbody tr:nth-child(even) { background: #f9f9f9; }
    tbody td {
      padding: 0.5rem;
      border: 1px solid #ddd;
    }
    .text-right { text-align: right; }
    .footer {
      text-align: center;
      margin-top: 2rem;
      font-size: 0.8rem;
      color: #999;
    }
    @media print {
      body { margin: 0; }
      .header, .subtitle, .window, .footer { page-break-inside: avoid; }
      thead { display: table-header-group; }
      tfoot { display: table-footer-group; }
      tr { page-break-inside: avoid; }
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>Auditoría de Movimientos</h1>
  </div>

  <div class="window">
    Desde <strong>{{ $hoy6am->format('Y-m-d H:i') }}</strong>
    hasta <strong>{{ $maniana6am->format('Y-m-d H:i') }}</strong>
  </div>

  <table>
    <thead>
      <tr>
        <th># Factura</th>
        <th>Cliente</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio U.</th>
        <th>Fecha Factura</th>
      </tr>
    </thead>
    <tbody>
      @foreach($movimientos as $m)
        @php
          $precio = $m->precio_unitario ?? $m->producto->precio_venta;
        @endphp
        <tr>
          <td class="text-right">{{ $m->factura->id_factura }}</td>
          {{-- Usamos el atributo correcto de Cliente --}}
          <td>{{ $m->factura->cliente->nombre }}</td>
          <td>{{ $m->producto->nombre }}</td>
          <td class="text-right">{{ $m->cantidad }}</td>
          <td class="text-right">C$ {{ number_format($precio, 2) }}</td>
          <td>{{ $m->factura->fecha->format('Y-m-d H:i') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="footer">
    &copy; {{ date('Y') }} Moto Repuestos Quinteros – Todos los derechos reservados.
  </div>

</body>
</html>
