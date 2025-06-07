{{-- resources/views/filtros/partials/tabla_creditos.blade.php --}}
<table class="table table-striped table-bordered">
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
    @forelse($creditos as $credito)
      <tr>
        <td>{{ $credito->factura->cliente->nombre }}</td>
        <td>C$ {{ number_format($credito->monto_total, 2) }}</td>
        <td>C$ {{ number_format($credito->monto_pagado, 2) }}</td>
        <td>C$ {{ number_format($credito->monto_total - $credito->monto_pagado, 2) }}</td>
        <td>
          @if($credito->monto_pagado >= $credito->monto_total)
            <span class="badge bg-success">Pagado</span>
          @else
            <span class="badge bg-warning text-dark">Pendiente</span>
          @endif
        </td>
        <td>{{ optional($credito->created_at)->format('Y-m-d H:i') }}</td>
    @empty
      <tr>
        <td colspan="7" class="text-center">No se encontraron créditos que coincidan.</td>
      </tr>
    @endforelse
  </tbody>
</table>
