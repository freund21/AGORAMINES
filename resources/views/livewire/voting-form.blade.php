<div>
    {{-- LIVEWIRE + PROYECTO:
         Vista Blade del componente VotingForm.
         Los datos principales llegan desde app/Livewire/VotingForm.php --}}
    <h2 style="margin-bottom: 0.5rem;">{{ $eleccion->title }}</h2>
    <p class="text-muted mb-2">{{ $eleccion->description }}</p>

    @if($haVotado)
        <div class="card">
            <div class="alert alert-success">Tu voto ha sido registrado correctamente.</div>

            @foreach($comprobantes as $comprobante)
                <div class="receipt-box mb-2">
                    <p style="margin-bottom: 0.5rem;"><strong>{{ $comprobante['categoria'] }}</strong></p>
                    <p style="margin-bottom: 0.3rem;">Código de verificación:</p>
                    <p class="receipt-code">{{ $comprobante['codigo_comprobante'] }}</p>
                    <p class="text-muted mt-1" style="font-size: 0.8rem;">Guarda este código para verificar tu voto</p>
                </div>
            @endforeach

            <div class="mt-2">
                <a href="{{ route('elections.index') }}" class="btn btn-primary">Volver a votaciones</a>
                <a href="{{ route('elections.results', $eleccion) }}" class="btn btn-secondary" style="margin-left: 0.5rem;">Ver resultados</a>
            </div>
        </div>
    @else
        @error('voto')
            <div class="alert alert-error">{{ $message }}</div>
        @enderror

        @foreach($categorias as $categoria)
            <div class="card">
                <h3>{{ $categoria->name }}</h3>
                <p class="text-muted mb-1" style="font-size: 0.85rem;">
                    Selecciona {{ $categoria->max_selections }} opción{{ $categoria->max_selections > 1 ? 'es' : '' }}
                </p>

                @if(auth()->user()->hasVotedInCategory($categoria->id))
                    <div class="alert alert-info">Ya has votado en esta categoría.</div>
                @else
                    <div class="grid-2 mt-1">
                        @foreach($categoria->options as $opcion)
                            @php
                                // BASE LARAVEL + PROYECTO:
                                // PHP dentro de Blade:
                                // comprueba si la opción actual ya está seleccionada.
                                $estaSeleccionada = in_array($opcion->id, $opcionesSeleccionadas[$categoria->id] ?? []);
                            @endphp
                            <div class="option-card {{ $estaSeleccionada ? 'selected' : '' }}"
                                 wire:click="seleccionarOpcion({{ $categoria->id }}, {{ $opcion->id }})">
                                {{-- LIVEWIRE:
                                     wire:click es de Livewire:
                                     al pulsar llama al método PHP seleccionarOpcion() --}}
                                <input type="{{ $categoria->max_selections > 1 ? 'checkbox' : 'radio' }}"
                                       {{ $estaSeleccionada ? 'checked' : '' }}
                                       readonly>
                                <strong>{{ $opcion->label }}</strong>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        <div class="mt-2">
            {{-- LIVEWIRE + PROYECTO:
                 Este botón ejecuta el método PHP enviarVotos() sin recargar la página entera. --}}
            <button wire:click="enviarVotos" class="btn btn-success" wire:loading.attr="disabled">
                <span wire:loading.remove>Confirmar voto</span>
                <span wire:loading>Procesando...</span>
            </button>
            <a href="{{ route('elections.index') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">Cancelar</a>
        </div>
    @endif
</div>
