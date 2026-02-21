<div class="modal fade" id="modalEliminarMecanico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Eliminar Mecánico</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Selecciona un mecánico para eliminar:</p>
          <ul class="list-group">
            @foreach($mechanics as $me)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $me->nombre }}
                <form method="POST" action="{{ route('mecanicos.destroy', $me->id_mecanico) }}" onsubmit="return confirm('¿Eliminar {{ $me->nombre }}?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
