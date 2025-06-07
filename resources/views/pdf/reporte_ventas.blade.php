<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Ventas</title>
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
    body { font-family: 'Segoe UI', sans-serif; color:#333; margin:0; padding:0;}

    /* Cabecera */
    .header{
      text-align:center;
      padding:20px 10px;
      background:#fafafa;
      border-bottom:2px solid #4f46e5;
    }
    .header h1{font-size:2rem;color:#4f46e5;margin:0;}

    /* Subtítulo */
    .subtitle{
      text-align:center;
      font-size:1rem;
      color:#666;
      margin:10px 0;
      border-bottom:1px solid #4f46e5;
      padding-bottom:5px;
    }

    /* Info periodo / totales */
    .info{
      display:flex;
      justify-content:space-between;
      margin:20px 10px;
      font-size:.95rem;
      flex-wrap: wrap;
    }
    .info > div {
      width: calc(33% - 10px);
      margin-bottom: 10px;
    }
    .text-right{text-align:right;}

    /* Tablas */
    table{
      width:100%;
      border-collapse:collapse;
      margin:0 10px 20px;
    }
    thead th{
      background:#4f46e5;
      color:#fff;
      padding:10px;
      text-align:center;
      border:1px solid #ddd;
      font-weight:normal;
    }
    tbody tr:nth-child(even){background:#f5f5f5;}
    tbody td{
      border:1px solid #ddd;
      padding:8px;
      text-align:center;
      font-size:.9rem;
    }
    tfoot th,tfoot td{
      border-top:2px solid #4f46e5;
      padding:10px;
      font-weight:bold;
      text-align:center;
    }

    /* Pie */
    .footer{
      text-align:center;
      font-size:.8rem;
      color:#999;
      margin:30px 0;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>Reporte de Ventas</h1>
  </div>

  <div class="subtitle">Moto Repuestos Quinteros</div>

  <div class="info">

    <div>
      <p><strong>Periodo:</strong> {{ $rangoLabel }}</p>
    </div>


    <div class="text-right">
      <p><strong>Total Vendido:</strong> C$ {{ number_format($totalVentas,2) }}</p>
    </div>


    @if(isset($totalDescuentos) && $totalDescuentos > 0)
      <div class="text-right">
        <p><strong>Total Descuentos:</strong> C$ {{ number_format($totalDescuentos,2) }}</p>
      </div>
    @endif
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Factura</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse($facturas as $i => $f)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $f->id_factura }}</td>
          <td>{{ $f->cliente->nombre }}</td>
          <td>{{ \Carbon\Carbon::parse($f->fecha)->format('Y-m-d H:i') }}</td>
          <td>C$ {{ number_format($f->total,2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5">No hay ventas en el periodo {{ $rangoLabel }}.</td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <th colspan="4" class="text-right">Total general:</th>
        <td>C$ {{ number_format($totalVentas,2) }}</td>
      </tr>
    </tfoot>
  </table>
  <table>
    <thead>
      <tr>
        <th>Método de pago</th>
        <th>Importe</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Efectivo / Contado</td>
        <td class="text-right">C$ {{ number_format($totalesPago['efectivo'],2) }}</td>
      </tr>
      <tr>
        <td>Tarjeta</td>
        <td class="text-right">C$ {{ number_format($totalesPago['tarjeta'],2) }}</td>
      </tr>
      <tr>
        <td>Crédito</td>
        <td class="text-right">C$ {{ number_format($totalesPago['credito'],2) }}</td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <th>Total métodos</th>
        <td class="text-right">
          C$ {{ number_format(
               $totalesPago['efectivo']
             + $totalesPago['tarjeta']
             + $totalesPago['credito'], 2) }}
        </td>
      </tr>
    </tfoot>
  </table>

  <div class="footer">
    &copy; {{ date('Y') }} Moto Repuestos Quinteros &ndash; Todos los derechos reservados.
  </div>

</body>
</html>
