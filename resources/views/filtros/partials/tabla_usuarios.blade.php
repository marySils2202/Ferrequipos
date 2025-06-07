
<table class="table table-striped">
  <thead>
    <tr>
      <th>Usuario</th>
      <th>Nombre</th>
      <th>Email</th>
      <th>Rol</th>
    </tr>
  </thead>
  <tbody>
    @forelse($usuarios as $u)
      <tr>
        <td>{{ $u->username }}</td>
        <td>{{ $u->nombre }}</td>
        <td>{{ $u->email }}</td>
        <td>{{ ucfirst($u->rol) }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="5" class="text-center">– Sin resultados –</td>
      </tr>
    @endforelse
  </tbody>
</table>
