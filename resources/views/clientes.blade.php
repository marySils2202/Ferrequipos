<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>👥 Gestión de Clientes </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/Aggcliente.css') }}">
</head>
<body>
   <a href="{{ route('factura') }}" class="back-corner" title="Volver">←</a>

  <div class="container">
    <h2>👥 Gestión de Clientes</h2>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form class="row g-2 form-inline" method="POST" action="{{ route('clientes.store') }}">
      @csrf
      <div class="col"><input name="nombre" class="form-control" placeholder="Nombre" required></div>
      <div class="col"><input name="direccion" class="form-control" placeholder="Dirección"></div>
      <div class="col"><input name="telefono"  class="form-control" placeholder="Teléfono"></div>
      <div class="col-auto"><button class="btn btn-success">➕ Agregar</button></div>
    </form>

    <input id="filterCliente" type="text" class="form-control" placeholder="🔍 Buscar por Nombre…">

    <button id="toggleAll">↕ Mostrar todo</button>
    <div class="table-wrap collapsed">
      <table id="tablaClientes" class="table">
        <thead>
          <tr>
            <th>#</th><th>Nombre</th><th>Dirección</th><th>Teléfono</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($clientes as $c)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $c->nombre }}</td>
              <td>{{ $c->direccion }}</td>
              <td>{{ $c->telefono }}</td>
              <td>
                <button class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#edit-{{ $c->id_cliente }}">✏️</button>
                <form class="d-inline" method="POST" action="{{ route('clientes.destroy', $c->id_cliente) }}" onsubmit="return confirm('¿Eliminar este cliente?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger">🗑️</button>
                </form>
              </td>
            </tr>
            <tr class="collapse" id="edit-{{ $c->id_cliente }}">
              <td colspan="5">
                <form class="row g-2 form-inline" method="POST" action="{{ route('clientes.update', $c->id_cliente) }}">
                  @csrf @method('PUT')
                  <div class="col"><input name="nombre"    class="form-control" value="{{ $c->nombre }}" required></div>
                  <div class="col"><input name="direccion" class="form-control" value="{{ $c->direccion }}"></div>
                  <div class="col"><input name="telefono"  class="form-control" value="{{ $c->telefono }}"></div>
                  <div class="col-auto"><button class="btn btn-success">Actualizar</button></div>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 <script src="{{ asset('js/Facturacion/Clientes.js') }}"></script>
</body>
</html>
