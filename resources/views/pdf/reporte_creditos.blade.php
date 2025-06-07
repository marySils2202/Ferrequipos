<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Créditos</title>
  <style>
    body { font-family:sans-serif; color:#333; margin:0; padding:0; }
    .header { padding:20px; background:#f3f3f3; border-bottom:2px solid #4f46e5; }
    .header h1 { margin:0; color:#4f46e5; font-size:1.8rem; }
    .subtitle { text-align:center; color:#666; margin:10px 0; }
    table { width:100%; border-collapse:collapse; margin:20px 0; }
    thead th { background:#4f46e5; color:#fff; padding:8px; border:1px solid #ddd; text-align:left;}
    tbody td { padding:8px; border:1px solid #ddd; font-size:.9rem; }
    tbody tr:nth-child(even) { background:#f9f9f9; }
    .footer { text-align:center; color:#999; margin:30px 0; font-size:.8rem; }
  </style>
</head>
<body>

  <div class="header">
    <h1>Reporte de Créditos</h1>
  </div>

  <div class="subtitle">
    Generado: {{ now()->format('Y-m-d H:i') }}
  </div>

  @if($items->isEmpty())
    <p style="text-align:center; color:#666; margin-top:2rem;">
      No hay créditos para mostrar.
    </p>
  @else
    <table>
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Monto Total</th>
          <th>Monto Pagado</th>
          <th>Saldo Pendiente</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $c)
          <tr>
            <td>{{ $c->factura->cliente->nombre }}</td>
            <td>CS {{ number_format($c->monto_total,2) }}</td>
            <td>CS {{ number_format($c->monto_pagado,2) }}</td>
            <td>CS {{ number_format($c->monto_total - $c->monto_pagado,2) }}</td>
            <td>
              @if($c->monto_pagado >= $c->monto_total)
                Pagado
              @else
                Pendiente
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <div class="footer">
    &copy; {{ date('Y') }} Moto Repuestos Quinteros – Todos los derechos reservados.
  </div>
</body>
</html>
