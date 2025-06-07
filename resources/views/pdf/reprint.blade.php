<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura #{{ $factura->id_factura }}</title>
  <style>
    @page { margin: 0.5cm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; color: #333; }
    .header { display:flex; justify-content:space-between; align-items:center; padding:1rem; background:#f3f3f3; border-bottom:3px solid #4f46e5; }
    .header h1 { font-size:1.6rem; color:#4f46e5; }
    .subtitle, .location { text-align:center; font-size:.9rem; color:#555; margin:.5rem 0; }
    .subtitle { border-bottom:1px solid #4f46e5; padding-bottom:.3rem; }
    .invoice-info { display:flex; justify-content:space-between; font-size:.9rem; padding:1rem; }
    .invoice-info .right { text-align:right; }
    table { width:100%; border-collapse:collapse; margin:1rem 0; font-size:.9rem; }
    thead th { background:#4f46e5; color:#fff; padding:.6rem; border:1px solid #ddd; text-align:left; }
    tbody tr:nth-child(even) { background:#f9f9f9; }
    tbody td { padding:.5rem; border:1px solid #ddd; }
    .text-right { text-align:right; }
    tfoot td { padding:.6rem; border:1px solid #ddd; font-weight:bold; }
    .footer { text-align:center; font-size:.8rem; color:#999; margin:2rem 0; }
    @media print {
      .header, .subtitle, .location, .invoice-info, .footer { page-break-inside:avoid; }
      table { page-break-inside:auto; }
      thead { display:table-header-group; }
      tfoot { display:table-footer-group; }
      tr { page-break-inside:avoid; }
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>¡Gracias por su compra!</h1>
  </div>

  <div class="subtitle">Moto Repuestos Quinteros</div>
  <div class="location">Matagalpa – Frente a la Bomba de Agua, 2 varas a la derecha</div>

  <div class="invoice-info">
    <div>
      <p><strong>Factura #</strong> {{ $factura->id_factura }}</p>
      <p><strong>Fecha:</strong> {{ $factura->fecha->format('Y-m-d H:i') }}</p>
      <p><strong>Cliente:</strong> {{ $factura->cliente->nombre }}</p>
      <p><strong>Tipo de pago:</strong>
        @if($factura->metodo_pago === 'credito')
          Crédito
        @else
          {{ ucfirst($factura->metodo_pago) }}
        @endif
      </p>

      @if($factura->metodo_pago === 'credito' && $factura->credito)
        <p>
          <strong>Monto de crédito:</strong>
          C$ {{ number_format($factura->credito->monto_total, 2) }}
        </p>
        <p>
          <strong>Fecha de cancelación:</strong>
          {{ optional($factura->credito->updated_at)->format('Y-m-d') }}
        </p>
      @endif
    </div>

    <div class="right">
      <p><strong>Facturó:</strong> {{ $factura->usuario->nombre }}</p>
      @if($factura->mano_obra > 0)
        <p>
          <strong>Mano de obra:</strong>
          C$ {{ number_format($factura->mano_obra, 2) }}
        </p>
      @endif
      <p><strong>Total:</strong> C$ {{ number_format($factura->total, 2) }}</p>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio U.</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @php $sumDetalle = 0; @endphp
      @foreach($factura->detalles as $d)
        @php
          $precio   = $d->precio_unitario ?? $d->producto->precio_venta;
          $subtotal = $d->cantidad * $precio;
          $sumDetalle += $subtotal;
        @endphp
        <tr>
          <td>{{ $d->producto->nombre }}</td>
          <td class="text-right">{{ $d->cantidad }}</td>
          <td class="text-right">C$ {{ number_format($precio, 2) }}</td>
          <td class="text-right">C$ {{ number_format($subtotal, 2) }}</td>
        </tr>
      @endforeach
    </tbody>

    <tfoot>
      <tr>
        <td colspan="3" class="text-right">Subtotal:</td>
        <td class="text-right">C$ {{ number_format($sumDetalle, 2) }}</td>
      </tr>

      @if(isset($factura->descuento) && $factura->descuento > 0)
        <tr>
          <td colspan="3" class="text-right">Descuento:</td>
          <td class="text-right">- C$ {{ number_format($factura->descuento, 2) }}</td>
        </tr>
      @endif

      @if($factura->mano_obra > 0)
        <tr>
          <td colspan="3" class="text-right">Mano de obra:</td>
          <td class="text-right">C$ {{ number_format($factura->mano_obra, 2) }}</td>
        </tr>
      @endif

      <tr>
        <td colspan="3" class="text-right">Total:</td>
        <td class="text-right">C$ {{ number_format($factura->total, 2) }}</td>
      </tr>
    </tfoot>
  </table>

  <script>window.onload = () => window.print();</script>

  <div class="footer">
    &copy; {{ date('Y') }} Moto Repuestos Quinteros – Todos los derechos reservados.
  </div>

</body>
</html>
