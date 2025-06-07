
<table class="table table-striped">
  <thead>
    <tr>

      <th>Nombre</th>
      <th>Domicilio</th>
      <th>Teléfono</th>
    </tr>
  </thead>
  <tbody>
    @forelse($proveedores as $p)
      <tr>
        <td>{{ $p->nombre }}</td>
        <td>{{ $p->Domicilio ?? '—' }}</td>
        <td>{{ $p->telefono ?? '—' }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="4" class="text-center">– Sin resultados –</td>
      </tr>
    @endforelse
  </tbody>
</table>
