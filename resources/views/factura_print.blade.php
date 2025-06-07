<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura #{{ $factura->id_factura }}</title>
  <style>
    @page { margin: 0.5cm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; color: #333; padding: 0; }
    .header { display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f3f3f3; border-bottom: 3px solid #4f46e5; }
    .header h1 { font-size: 1.6rem; color: #4f46e5; }
    .subtitle, .location { text-align: center; margin: .5rem 0; font-size: 0.9rem; color: #555; }
    .subtitle { border-bottom: 1px solid #4f46e5; padding-bottom: .3rem; }
    .invoice-info { display: flex; justify-content: space-between; font-size: .9rem; padding: 1rem; }
    .invoice-info div { flex: 1; }
    .invoice-info .right { text-align: right; }
    table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: .9rem; }
    thead th { background: #4f46e5; color: #fff; padding: .6rem; border: 1px solid #ddd; }
    tbody tr:nth-child(even) { background: #f9f9f9; }
    tbody td { padding: .5rem; border: 1px solid #ddd; }
    .text-right { text-align: right; }
    tfoot td { padding: .6rem; border: 1px solid #ddd; font-weight: bold; }
    .footer { text-align: center; font-size: .8rem; color: #999; margin: 2rem 0; }
  </style>
</head>
<body>

  <div class="header">
    <h1>¡Gracias por su compra!</h1>
  </div>

  <div class="subtitle">Moto Repuestos Quinteros</div>
  <div class="location">Matagalpa – Frente a la Bomba de Agua, 2 varas a la derecha</div>

  <div class="invoice-info">
    <div class="left">
      <p><strong>Factura #</strong> {{ $factura->id_factura }}</p>
      <p><strong>Fecha:</strong> {{ $factura->fecha->format('Y-m-d H:i') }}</p>
      <p><strong>Cliente:</strong> {{ $factura->cliente->nombre }}</p>
      <p><strong>Tipo de pago:</strong> {{ ucfirst($factura->metodo_pago) }}</p>

      @if($factura->metodo_pago === 'credito' && $factura->credito)
        <p>
          <strong>Monto de crédito:</strong>
          C$ {{ number_format($factura->credito->monto_total, 2) }}
        </p>
      @endif
    </div>

    <div class="right">
      @if($factura->mano_obra > 0)
        <p>
          <strong>Mano de obra:</strong>
          C$ {{ number_format($factura->mano_obra, 2) }}
        </p>
      @endif
      <p>
        <strong>Total:</strong>
        C$ {{ number_format($factura->total, 2) }}
      </p>
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
          $subtotal = $d->subtotal        ?? ($d->cantidad * $precio);
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
      @if( isset($factura->descuento) && $factura->descuento > 0 )
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

  <div class="footer">
    &copy; {{ date('Y') }} Moto Repuestos Quinteros – Todos los derechos reservados.
  </div>

</body>
</html>
