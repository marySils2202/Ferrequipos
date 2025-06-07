<table class="table table-striped">
  <thead>
    <tr>
      <th>Productos</th>
      <th>Proveedor</th>
      <th>Fecha</th>
      <th>Cantidad Total</th>
      <th>Total (₵)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($compras as $compra)
      @php

        $nombres = $compra->detalles
                          ->map(fn($d) => $d->producto->nombre)
                          ->unique()
                          ->toArray();
      @endphp
      <tr>

        <td>{{ implode(', ', $nombres) }}</td>

        <td>{{ $compra->proveedor->nombre ?? '—' }}</td>

    
        <td>{{ \Illuminate\Support\Carbon::parse($compra->fecha)->format('Y-m-d') }}</td>


        <td>{{ $compra->detalles->sum('cantidad') }}</td>

        <td>₵S {{ number_format($compra->total, 2) }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="5" class="text-center">– Sin resultados –</td>
      </tr>
    @endforelse
  </tbody>
</table>
