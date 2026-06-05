<div>
    {{-- LIVEWIRE + PROYECTO:
         Vista Blade del componente Livewire Admin\ElectionManager.
         Aquí el admin gestiona votaciones, categorías y opciones. --}}
    <div class="flex-between mb-2">
        <h2>Gestión de Votaciones</h2>
        {{-- LIVEWIRE:
             Al pulsar, ejecuta crear() en el componente PHP. --}}
        <button wire:click="crear" class="btn btn-primary">Nueva votación</button>
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if($mostrarFormulario)
        {{-- LIVEWIRE + PROYECTO:
             Modal de formulario principal de votación. --}}
        <div class="modal-overlay" wire:click.self="$set('mostrarFormulario', false)">
            <div class="modal">
                <h3 class="mb-2">{{ $idEdicion ? 'Editar' : 'Nueva' }} votación</h3>

                <div class="form-group">
                    <label>Título</label>
                    <input type="text" wire:model="titulo">
                    @error('titulo') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea wire:model="descripcion" rows="3"></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Tipo</label>
                        <input type="text" wire:model="tipo">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select wire:model="estado">
                            <option value="pending">Pendiente</option>
                            <option value="active">Activa</option>
                            <option value="closed">Cerrada</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Fecha inicio</label>
                        {{-- LIVEWIRE:
                             wire:model enlaza este campo con la propiedad $fecha_inicio (PHP). --}}
                        <input type="datetime-local" wire:model="fecha_inicio">
                        @error('fecha_inicio') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Fecha fin</label>
                        <input type="datetime-local" wire:model="fecha_fin">
                        @error('fecha_fin') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;">
                            <input type="checkbox" wire:model="es_anonima"> Votación anónima
                        </label>
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;">
                            <input type="checkbox" wire:model="resultados_tiempo_real"> Resultados en tiempo real
                        </label>
                    </div>
                </div>

                <div class="flex gap-1 mt-2">
                    <button wire:click="guardar" class="btn btn-success">Guardar</button>
                    <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>
    @endif

    @if($eleccionGestionada)
        {{-- LIVEWIRE + PROYECTO:
             Panel secundario para editar categorías de la elección seleccionada. --}}
        <div class="card mb-2">
            <div class="flex-between">
                <h3>Categorías de: {{ $eleccionGestionada->title }}</h3>
                <div class="flex gap-1">
                    <button wire:click="crearCategoria" class="btn btn-primary btn-sm">Nueva categoría</button>
                    <button wire:click="cerrarCategorias" class="btn btn-secondary btn-sm">Cerrar</button>
                </div>
            </div>

            @if($mostrarFormularioCategoria)
                {{-- PROYECTO:
                     Formulario interno de categoría (nombre y opciones). --}}
                <div class="card mt-2" style="background: #f9fafb;">
                    <h4 class="mb-1">{{ $idCategoriaEdicion ? 'Editar' : 'Nueva' }} categoría</h4>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" wire:model="nombre_categoria">
                        </div>
                        <div class="form-group">
                            <label>Max. selecciones</label>
                            <input type="number" wire:model="maximo_selecciones_categoria" min="1">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Opciones (una por línea)</label>
                        <textarea wire:model="texto_opciones_categoria" rows="4" placeholder="Candidato 1&#10;Candidato 2&#10;Voto en blanco"></textarea>
                    </div>

                    {{-- LIVEWIRE + PROYECTO:
                         Asignación de votantes habilitados para esta categoría,
                         con casilla para seleccionar a todos de una vez. --}}
                    <div class="form-group">
                        <div class="flex-between mb-1">
                            <label style="margin:0;">Votantes habilitados ({{ count($usuarios_categoria) }}/{{ $votantes->count() }})</label>
                            <label style="display:flex;align-items:center;gap:0.3rem;font-weight:normal;font-size:0.85rem;cursor:pointer;">
                                <input type="checkbox" wire:click="alternarTodosUsuarios"
                                       @checked($votantes->count() > 0 && count($usuarios_categoria) === $votantes->count())>
                                Seleccionar todos
                            </label>
                        </div>
                        <div style="max-height:180px;overflow-y:auto;border:1px solid #d1d5db;border-radius:6px;padding:0.5rem;">
                            @forelse($votantes as $votante)
                                <label style="display:flex;align-items:center;gap:0.4rem;font-weight:normal;margin-bottom:0.25rem;cursor:pointer;">
                                    <input type="checkbox" wire:model.live="usuarios_categoria" value="{{ $votante->id }}">
                                    {{ $votante->full_name }}
                                    <span class="text-muted" style="font-size:0.75rem;">({{ $votante->username }})</span>
                                </label>
                            @empty
                                <p class="text-muted" style="font-size:0.85rem;">No hay votantes creados todavía. Créalos en "Administrar usuarios".</p>
                            @endforelse
                        </div>
                        <p class="text-muted mt-1" style="font-size:0.8rem;">Solo los usuarios marcados podrán votar en esta categoría.</p>
                    </div>

                    <div class="flex gap-1">
                        <button wire:click="guardarCategoria" class="btn btn-success btn-sm">Guardar</button>
                        <button wire:click="$set('mostrarFormularioCategoria', false)" class="btn btn-secondary btn-sm">Cancelar</button>
                    </div>
                </div>
            @endif

            @foreach($eleccionGestionada->categories as $categoria)
                <div class="card mt-1" style="border: 1px solid #e5e7eb;">
                    <div class="flex-between">
                        <div>
                            <strong>{{ $categoria->name }}</strong>
                            <span class="text-muted" style="font-size:0.85rem;">(max: {{ $categoria->max_selections }})</span>
                            <div class="mt-1 text-muted" style="font-size:0.85rem;">
                                Opciones: {{ $categoria->options->pluck('label')->implode(', ') }}
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button wire:click="editarCategoria({{ $categoria->id }})" class="btn btn-primary btn-sm">Editar</button>
                            <button wire:click="eliminarCategoria({{ $categoria->id }})" wire:confirm="Eliminar esta categoría?" class="btn btn-danger btn-sm">Eliminar</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fechas</th>
                    <th>Categorías</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($elecciones as $eleccion)
                    <tr>
                        <td><strong>{{ $eleccion->title }}</strong></td>
                        <td>
                            @if($eleccion->status === 'active')
                                <span class="badge badge-active">Activa</span>
                            @elseif($eleccion->status === 'pending')
                                <span class="badge badge-pending">Pendiente</span>
                            @else
                                <span class="badge badge-closed">Cerrada</span>
                            @endif
                        </td>
                        <td style="font-size:0.85rem;">
                            {{ $eleccion->start_date->format('d/m/Y H:i') }}<br>
                            {{ $eleccion->end_date->format('d/m/Y H:i') }}
                        </td>
                        <td>{{ $eleccion->categories->count() }}</td>
                        <td>
                            <div class="flex gap-1">
                                {{-- LIVEWIRE + PROYECTO:
                                     Botones Livewire para gestionar esta elección. --}}
                                <button wire:click="gestionarCategorias({{ $eleccion->id }})" class="btn btn-secondary btn-sm">Categorías</button>
                                <button wire:click="editar({{ $eleccion->id }})" class="btn btn-primary btn-sm">Editar</button>
                                <button wire:click="eliminar({{ $eleccion->id }})" wire:confirm="Eliminar esta votación?" class="btn btn-danger btn-sm">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
