<div>
    {{-- LIVEWIRE + PROYECTO:
         Vista Blade del componente Livewire Admin\UserManager.
         Aquí se administran usuarios (crear, editar, eliminar). --}}
    <div class="flex-between mb-2">
        <h2>Gestión de Usuarios</h2>
        {{-- LIVEWIRE:
             wire:click llama al método crear() del componente PHP. --}}
        <button wire:click="crear" class="btn btn-primary">Nuevo usuario</button>
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if($mostrarFormulario)
        {{-- LIVEWIRE + PROYECTO:
             Modal: wire:click.self cierra al pinchar fuera del contenido. --}}
        <div class="modal-overlay" wire:click.self="$set('mostrarFormulario', false)">
            <div class="modal">
                <h3 class="mb-2">{{ $idEdicion ? 'Editar' : 'Nuevo' }} usuario</h3>

                <div class="grid-2">
                    <div class="form-group">
                        <label>DNI</label>
                        {{-- LIVEWIRE:
                             wire:model enlaza el input con la propiedad PHP $dni. --}}
                        <input type="text" wire:model="dni">
                        @error('dni') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Usuario</label>
                        <input type="text" wire:model="usuario">
                        @error('usuario') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Nombre completo</label>
                    <input type="text" wire:model="nombre_completo">
                    @error('nombre_completo') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" wire:model="email">
                        @error('email') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Contraseña {{ $idEdicion ? '(dejar vacío para no cambiar)' : '' }}</label>
                        <input type="password" wire:model="contrasena">
                        @error('contrasena') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Rol</label>
                        <select wire:model="id_rol">
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ ucfirst($rol->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Categorías</label>
                        @foreach($categorias as $categoria)
                            <label style="display:flex;align-items:center;gap:0.3rem;font-weight:normal;margin-bottom:0.2rem;">
                                <input type="checkbox" wire:model="ids_categorias" value="{{ $categoria->id }}">
                                {{ $categoria->name }}
                                <span class="text-muted" style="font-size:0.75rem;">({{ $categoria->election->title }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-1 mt-2">
                    <button wire:click="guardar" class="btn btn-success">Guardar</button>
                    {{-- LIVEWIRE:
                         $set es útil de Livewire para cambiar propiedades desde la vista. --}}
                    <button wire:click="$set('mostrarFormulario', false)" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Categorías</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->dni }}</td>
                        <td>{{ $usuario->username }}</td>
                        <td>{{ $usuario->full_name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td><span class="badge {{ $usuario->role->name === 'admin' ? 'badge-active' : 'badge-pending' }}">{{ ucfirst($usuario->role->name) }}</span></td>
                        <td>
                            @foreach($usuario->categories as $categoria)
                                <span class="badge badge-active" style="font-size:0.7rem;">{{ $categoria->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            {{-- LIVEWIRE + PROYECTO:
                                 Acciones Livewire sobre el componente PHP. --}}
                            <button wire:click="editar({{ $usuario->id }})" class="btn btn-primary btn-sm">Editar</button>
                            <button wire:click="eliminar({{ $usuario->id }})" wire:confirm="Eliminar este usuario?" class="btn btn-danger btn-sm">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
