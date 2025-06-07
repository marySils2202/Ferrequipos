
<h2 class="mb-3">➕ Agregar Producto</h2>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<button
  class="btn btn-success mb-4"
  data-bs-toggle="collapse"
  data-bs-target="#collapseAgregar"
>
  ➕ Agregar 
</button>

<div class="collapse mb-4" id="collapseAgregar">
  <form method="POST" action="{{ route('productos.store') }}">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Nombre</label>
        <input name="nombre" type="text"
               class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre') }}" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">Descripción</label>
        <input name="descripcion" type="text"
               class="form-control @error('descripcion') is-invalid @enderror"
               value="{{ old('descripcion') }}">
        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">Categoría</label>
        <select name="id_categoria"
                class="form-select @error('id_categoria') is-invalid @enderror"
                required>
          <option value="">-- Seleccione --</option>
          @foreach($categorias as $cat)
            <option value="{{ $cat->id_categoria }}"
              {{ old('id_categoria') == $cat->id_categoria ? 'selected' : '' }}>
              {{ $cat->nombre_categoria }}
            </option>
          @endforeach
        </select>
        @error('id_categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">Estado</label>
        <select name="estado"
                class="form-select @error('estado') is-invalid @enderror"
                required>
          <option value="">-- Seleccione --</option>
          <option value="1" {{ old('estado')==='1' ? 'selected' : '' }}>Activo</option>
          <option value="0" {{ old('estado')==='0' ? 'selected' : '' }}>Inactivo</option>
        </select>
        @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

<div class="col-md-3">
  <label class="form-label">Stock Mínimo</label>
  <input name="stock_minimo"
         type="number"
         min="0"
         class="form-control @error('stock_minimo') is-invalid @enderror"
         value="{{ old('stock_minimo', 0) }}"
         required>
  @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>


<div class="col-md-3">
  <label class="form-label">Precio Venta</label>
  <input name="precio_venta"
         type="number"
         step="0.01"
         min="0"
         class="form-control @error('precio_venta') is-invalid @enderror"
         value="{{ old('precio_venta') }}"
         required>
  @error('precio_venta')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>


    <div class="mt-4 text-end">
      <button type="button"
              class="btn btn-secondary"
              data-bs-toggle="collapse"
              data-bs-target="#collapseAgregar">
        Cancelar
      </button>
      <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
  </form>
</div>


