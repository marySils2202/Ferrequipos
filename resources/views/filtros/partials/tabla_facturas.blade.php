
<table class="table table-striped">
  <thead>
    <tr>
      <th># Factura</th>
      <th>Cliente</th>
      <th>Fecha</th>
      <th>Total</th>
      <th>Monto Pago</th>
      <th>Vuelto</th>
    </tr>
  </thead>
  <tbody>
    @forelse($facturas as $f)
      <tr>
        <td>{{ $f->id_factura }}</td>
        <td>{{ $f->cliente->nombre ?? '—' }}</td>
        <td>{{ $f->fecha->format('Y-m-d') }}</td>
        <td>₵S {{ number_format($f->total,2) }}</td>
        <td>₵S {{ number_format($f->monto_pago,2) }}</td>
        <td>₵S {{ number_format($f->vuelto,2) }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="text-center">– Sin resultados –</td>
      </tr>
    @endforelse
  </tbody>
</table>
