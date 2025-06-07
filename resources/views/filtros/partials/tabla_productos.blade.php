
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Descripción</th>
      <th>Categoría</th>
      <th>Precio Venta</th>
      <th>Stock Mínimo</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    @forelse($productos as $pr)
      <tr>
        <td>{{ $pr->nombre }}</td>
        <td>{{ $pr->descripcion ?? '—' }}</td>
        <td>{{ $pr->categoria->nombre_categoria ?? '—' }}</td>
        <td>₵S {{ number_format($pr->precio_venta, 2) }}</td>
        <td>{{ $pr->stock_minimo }}</td>
        <td>{{ $pr->estado ? 'Activo' : 'Inactivo' }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="7" class="text-center">No hay productos registrados.</td>
      </tr>
    @endforelse
  </tbody>
</table>

