<div class="position-relative">

    <a href="{{ route('sistema') }}"
       class="d-flex align-items-center justify-content-center"
       title="Volver a Inicio"
       style="
         position: fixed;
         top: 20px;
         left: 20px;
         width: 44px;
         height: 44px;
         background: linear-gradient(90deg, #6D5BFF, #A986FF);
         color: #fff;
         font-size: 1.4rem;
         text-decoration: none;
         border-radius: 50%;
         box-shadow: 0 4px 12px rgba(0,0,0,.15);
         z-index: 1000;
       ">
        ←
    </a>

    <div class="container py-4">

        @if($notification)
            <div class="alert 
                        {{ $notificationType === 'error' ? 'alert-danger' : 'alert-success' }} 
                        alert-dismissible fade show"
                 role="alert"
                 wire:poll.1000ms="$set('notification', null)">
                {{ $notification }}
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>
            </div>
        @endif

        <div class="row mb-3">
            <div class="col">
                <h1 class="h3 fw-bold d-flex align-items-center">
                    <span class="me-2">⚙️</span>
                    <span>Gestión de Respaldos</span>
                </h1>
                <hr>
            </div>
        </div>

        {{-- Tarjeta o cuadro principal --}}
        <div class="card shadow-sm mb-5 rounded-3">
            <div class="card-body">

                {{-- --- BLOQUE: Crear Respaldo --- --}}
                <div class="row align-items-center mb-4 gy-2">
                    <div class="col-12 col-md-auto">
                        <label class="form-label mb-0">
                            <span class="fw-medium">Respaldar Base de Datos:</span>
                        </label>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="button"
                                class="btn btn-primary"
                                wire:click.prevent="downloadBackup"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="downloadBackup">📦 Crear Respaldo</span>
                            <span wire:loading wire:target="downloadBackup">⏳ Procesando…</span>
                        </button>
                    </div>
                </div>

                <hr class="my-4">

                {{-- --- BLOQUE: Restaurar Respaldo --- --}}
                <form wire:submit.prevent="restoreDatabase"
                      enctype="multipart/form-data"
                      class="gy-3">
                    <div class="row align-items-center gy-2">
                        <div class="col-12 col-md-auto">
                            <label class="form-label mb-0">
                                <span class="fw-medium">Restaurar Base de Datos:</span>
                            </label>
                        </div>

                        <div class="col-12 col-md-6">
                            {{-- Input tipo “file” oculto --}}
                            <input type="file"
                                   wire:model="backupFile"
                                   id="backupFileInput"
                                   accept=".bak,.sql"
                                   style="display: none;">

                            {{-- Campo texto que muestra el nombre de archivo o placeholder --}}
                            <div class="input-group">
                                <input type="text"
                                       readonly
                                       class="form-control"
                                       placeholder="{{ $backupFile
                                           ? $backupFile->getClientOriginalName()
                                           : 'Selecciona archivo .bak o .sql' }}"
                                       onclick="document.getElementById('backupFileInput').click()"
                                       style="cursor: pointer;">
                                <button class="btn btn-success"
                                        type="submit"
                                        {{ $backupFile ? '' : 'disabled' }}
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="restoreDatabase">🔄 Restaurar</span>
                                    <span wire:loading wire:target="restoreDatabase">⏳ Procesando…</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Mensaje de error de validación --}}
                    @error('backupFile')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </form>

            </div>
        </div>

    </div>
</div>

{{-- Incluye los estilos y scripts de Livewire + Bootstrap --}}
@push('styles')
    @livewireStyles
@endpush

@push('scripts')
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush
