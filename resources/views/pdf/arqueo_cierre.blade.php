<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cierre de Caja – Arqueo N°{{ $arqueo->id_arqueo }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    /* Márgenes e impresión */
    @page { margin: 0.5cm; }
    @media print {
      body { margin: 0; }
      .header, .subtitle, .info, .footer { break-inside: avoid; }
      table { page-break-inside: auto; }
      thead { display: table-header-group; }
      tbody { display: table-row-group; }
      tfoot { display: table-footer-group; }
      tr { page-break-inside: avoid; page-break-after: auto; }
    }

    /* Tipografía */
    body { font-family: 'Segoe UI', sans-serif; color: #333; margin: 0; padding: 0; }

    /* Cabecera centrada */
    .header {
      text-align: center;
      padding: 20px 10px;
      background: #fafafa;
      border-bottom: 2px solid #4f46e5;
    }
    .header h1 {
      font-size: 2rem;
      color: #4f46e5;
      margin: 0;
    }
    .header .number {
      display: block;
      font-size: 1rem;
      color: #555;
      margin-top: 4px;
    }

    /* Subtítulo y línea inferior */
    .subtitle {
      text-align: center;
      font-size: 1rem;
      color: #666;
      margin: 10px 0;
      border-bottom: 1px solid #4f46e5;
      padding-bottom: 5px;
    }

    /* Sección de información */
    .info {
      display: flex;
      justify-content: space-between;
      margin: 20px 10px;
      font-size: 0.95rem;
    }
    .info div { width: 48%; }

    /* Tabla de datos */
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 0 10px 20px;
    }
    thead th {
      background: #4f46e5;
      color: #fff;
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
      font-weight: normal;
    }
    tbody tr:nth-child(even) { background: #f5f5f5; }
    tbody td {
      border: 1px solid #ddd;
      padding: 8px;
      font-size: 0.9rem;
    }
    .text-right { text-align: right; }
    tfoot th, tfoot td {
      border-top: 2px solid #4f46e5;
      padding: 10px;
      font-weight: bold;
    }

    /* Pie de página */
    .footer {
      text-align: center;
      font-size: 0.8rem;
      color: #999;
      margin: 30px 0;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>Cierre de Caja</h1>
    <span class="number">Arqueo N° {{ $arqueo->id_arqueo }}</span>
     <p><strong>Impreso por:</strong> {{ $usuario->nombre }}
  </div>

  <div class="subtitle">Moto Repuestos Quinteros</div>

  <div class="info">
    <div>
      <p><strong>Fecha de arqueo:</strong> {{ \Carbon\Carbon::parse($arqueo->fecha)->format('Y-m-d H:i') }}</p>
    </div>
    <div class="text-right">
     
      @if($arqueo->printed_at) el {{ \Carbon\Carbon::parse($arqueo->printed_at)->format('Y-m-d H:i') }} @endif
      </p>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Concepto</th>
        <th>Monto</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>Monto inicial</td><td>{{ number_format($arqueo->monto_inicial, 2) }}</td></tr>
      <tr><td>Monto final</td><td>{{ number_format($arqueo->monto_final ?? 0, 2) }}</td></tr>
      <tr><td>Salida de caja</td><td>{{ number_format($arqueo->salida_caja ?? 0, 2) }}</td></tr>
      <tr><td>Diferencia</td><td>{{ number_format($arqueo->diferencia ?? 0, 2) }}</td></tr>
      <tr><td>Razón de salida</td><td>{{ $arqueo->razon_salida ?? '—' }}</td></tr>
    </tbody>
    <tfoot>
      <tr>
        <th>Total Ganancia Neta</th>
        <td class="text-right">{{ number_format($arqueo->diferencia ?? 0, 2) }}</td>
      </tr>
    </tfoot>
  </table>

  <script>window.onload = () => window.print();</script>

  <div class="footer">
    &copy; {{ date('Y') }} Moto Repuestos Quinteros &ndash; Todos los derechos reservados.
  </div>

</body>
</html>
