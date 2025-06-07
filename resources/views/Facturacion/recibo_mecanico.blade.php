<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Ganancias del Mecánico</title>
  <style>
    body { font-family: sans-serif; color: #333; margin: 0; padding: 0; }
    .header {
      display: flex;
      align-items: center;
      padding: 20px;
      background: #f3f3f3;
      border-bottom: 2px solid #4f46e5;
    }
    .header img { height: 50px; margin-right: 20px; }
    .logo-placeholder {
      flex-shrink: 0; width: 50px; height: 50px;
      background: #ddd; border-radius: 50%;
      text-align: center; line-height: 50px;
      color: #888; font-size: .9rem; margin-right: 20px;
    }
    .header h1 {
      font-size: 1.8rem; color: #4f46e5; margin: 0;
    }
    .subtitle {
      text-align: center; margin: 10px 0;
      font-size: 1rem; color: #666;
    }
    table {
      width: 100%; border-collapse: collapse; margin: 20px 0;
    }
    thead th {
      background: #4f46e5; color: #fff; padding: 10px;
      text-align: center; border: 1px solid #ddd;
    }
    tbody tr:nth-child(even) { background: #f9f9f9; }
    tbody td {
      border: 1px solid #ddd; padding: 8px;
      text-align: center; font-size: .9rem;
    }
    .total-row td { font-weight: bold; background: #eef; }
    .signature-block {
      margin: 40px 0 60px; display: flex; justify-content: space-between;
    }
    .signature-line {
      width: 45%; border-top: 1px solid #333;
      text-align: center; padding-top: 6px;
      font-size: .9rem; color: #333;
    }
    .footer {
      text-align: center; margin: 30px 0;
      font-size: .8rem; color: #999;
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
    <h1>Recibo de Ganancias del Mecánico</h1>
  </div>

  <div class="subtitle">
    <p><strong>Semana:</strong> {{ $start->format('Y-m-d') }} – {{ $end->format('Y-m-d') }}</p>
    <p><strong>Generado:</strong> {{ now()->format('Y-m-d H:i') }}</p>
    @if(!empty($mechanic))
      <p><strong>Mecánico:</strong> {{ $mechanic->nombre }}</p>
      <p><em>Recibo para Nomina al mecánico</em></p>
    @else
      <p><em>Recibo general</em></p>
    @endif
  </div>

  <table>
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Ganancia Mecánico (70%)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($daily as $d)
        <tr>
          <td>{{ \Carbon\Carbon::parse($d['date'])->format('Y-m-d') }}</td>
          <td>C$ {{ number_format($d['gain'], 2) }}</td>
        </tr>
      @endforeach
      <tr class="total-row">
        <td>Total:</td>
        <td>C$ {{ number_format($total, 2) }}</td>
      </tr>
    </tbody>
  </table>

        <div class="signature-block">
        <div class="signature-line">Recibi Conforme</div>
        </div>

  <div class="footer">
    &copy; {{ date('Y') }} MotoRepuestosQuinteros – Todos los derechos reservados.
  </div>

</body>
</html>
