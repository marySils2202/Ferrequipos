<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <script>
    function toggleEditForm(id) {
      const form = document.getElementById('edit-form-' + id);
      form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
  </script>
  <link rel="stylesheet" href="{{ asset('css/Users.css') }}">
</head>
<body>

  <div class="container-usuarios">

    <h1>Usuarios del Sistema</h1>

    @if (session('success'))
      <div class="alert alert-success text-center mt-3">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <h3>Agregar / Administrar usuario</h3>

    <form class="form-agregar" method="POST" action="{{ route('personal.store') }}">
      @csrf
      <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
      <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
      <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>

      <input
        type="password"
        name="password"
        class="form-control mb-2 @error('password') is-invalid @enderror"
        placeholder="Contraseña (mín. 6 caracteres)"
        minlength="6"
        required
      >
      @error('password')
        <div class="invalid-feedback mb-2">{{ $message }}</div>
      @enderror

      <select name="rol" class="form-control mb-3" required>
        <option value="admin">Admin</option>
        <option value="facturador">Facturador</option>
        <option value="bodeguero">Bodeguero</option>
      </select>
      <button type="submit" class="btn btn-success w-100">Agregar</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Username</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($usuarios as $usuario)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $usuario->username }}</td>
          <td>{{ $usuario->nombre }}</td>
          <td>{{ $usuario->email }}</td>
          <td>{{ ucfirst($usuario->rol) }}</td>
          <td>
            <button class="btn btn-primary btn-sm" type="button"
                    onclick="toggleEditForm('{{ $usuario->id_usuario }}')">Editar</button>

            <form action="{{ route('personal.destroy', $usuario->id_usuario) }}"
                  method="POST" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm"
                      onclick="return confirm('¿Deseas eliminar este usuario?')">Eliminar</button>
            </form>

            <div class="edit-form" id="edit-form-{{ $usuario->id_usuario }}" style="display: none;">
              <form method="POST" action="{{ route('personal.update', $usuario->id_usuario) }}">
                @csrf
                @method('PUT')
                <input type="text" name="username" value="{{ $usuario->username }}" class="form-control mb-2" required>
                <input type="email" name="email" value="{{ $usuario->email }}" class="form-control mb-2" required>
                <input type="text" name="nombre" value="{{ $usuario->nombre }}" class="form-control mb-2" required>

                <input
                  type="password"
                  name="password"
                  class="form-control mb-2 @error('password') is-invalid @enderror"
                  placeholder="Nueva contraseña (mín. 6 caracteres)"
                  minlength="6"
                >
                @error('password')
                  <div class="invalid-feedback mb-2">{{ $message }}</div>
                @enderror

                <select name="rol" class="form-control mb-2" required>
                  <option value="admin" {{ $usuario->rol == 'admin' ? 'selected' : '' }}>Admin</option>
                  <option value="facturador" {{ $usuario->rol == 'facturador' ? 'selected' : '' }}>Facturador</option>
                  <option value="bodeguero" {{ $usuario->rol == 'bodeguero' ? 'selected' : '' }}>Bodeguero</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">Guardar cambios</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="back-button">
      <a href="{{ url('sistema') }}" class="btn btn-outline-dark">← Home</a>
    </div>

  </div>

</body>
</html>
