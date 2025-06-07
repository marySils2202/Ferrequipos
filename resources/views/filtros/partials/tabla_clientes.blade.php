
<table class="table table-striped">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Dirección</th>
      <th>Teléfono</th>
    </tr>
  </thead>
  <tbody>
    @forelse($clientes as $c)
      <tr>
        <td>{{ $c->nombre }}</td>
        <td>{{ $c->direccion ?? '—' }}</td>
        <td>{{ $c->telefono ?? '—' }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="4" class="text-center">– Sin resultados –</td>
      </tr>
    @endforelse
  </tbody>
</table>
