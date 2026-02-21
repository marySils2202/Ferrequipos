

<h2 class="mb-3">🏷 Agregar Proveedor</h2>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<button class="btn btn-success mb-3"
        data-bs-toggle="collapse"
        data-bs-target="#collapseAgregar">
  ➕ Agregar 
</button>

<div class="collapse mb-4" id="collapseAgregar">
  <form action="{{ route('proveedores.store') }}" method="POST">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label" for="crear-nombre">Nombre</label>
        <input id="crear-nombre" name="nombre" type="text"
               class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre') }}" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label" for="crear-domicilio">Domicilio</label>
        <input id="crear-domicilio" name="domicilio" type="text"
               class="form-control @error('domicilio') is-invalid @enderror"
               value="{{ old('domicilio') }}">
        @error('domicilio')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label" for="crear-telefono">Teléfono</label>
        <input id="crear-telefono" name="telefono" type="text"
               class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono') }}">
        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="mt-3 text-end">
      <button type="button"
              class="btn btn-secondary"
              data-bs-toggle="collapse"
              data-bs-target="#collapseAgregar">Cancelar</button>
      <button type="submit" class="btn btn-success">Agregar</button>
    </div>
  </form>
</div>
