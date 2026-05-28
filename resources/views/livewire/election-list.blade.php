<div>
    {{-- LIVEWIRE + PROYECTO:
         Vista Blade del componente Livewire ElectionList.
         Blade renderiza HTML y el componente PHP prepara los datos. --}}
    <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Votaciones disponibles</h2>

    @if($elecciones->isEmpty())
        <div class="card text-center">
            <p class="text-muted">No hay votaciones disponibles en este momento.</p>
        </div>
    @endif

    @foreach($elecciones as $eleccion)
        <div class="card">
            <div class="flex-between">
                <div>
                    <h3>{{ $eleccion->title }}</h3>
                    <p class="text-muted mb-1">{{ $eleccion->description }}</p>
                    <p class="text-muted" style="font-size: 0.85rem;">
                        {{ $eleccion->start_date->format('d/m/Y H:i') }} - {{ $eleccion->end_date->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div style="text-align: right;">
                    @if($eleccion->status === 'active')
                        <span class="badge badge-active">Abierta</span>
                    @elseif($eleccion->status === 'pending')
                        <span class="badge badge-pending">Pendiente</span>
                    @else
                        <span class="badge badge-closed">Cerrada</span>
                    @endif
                </div>
            </div>

            @if($eleccion->categorias_usuario->isNotEmpty())
                <div class="mt-2" style="font-size: 0.9rem;">
                    <strong>Tus categorías:</strong>
                    @foreach($eleccion->categorias_usuario as $categoria)
                        <span class="badge badge-active" style="margin-left: 0.3rem;">{{ $categoria->name }}</span>
                    @endforeach
                </div>
            @endif

            <div class="mt-2 flex gap-1">
                {{-- PROYECTO + BASE LARAVEL:
                     Botón para ir a votar: enlace Laravel a ruta del componente VotingForm. --}}
                @if($eleccion->isOpen() && $eleccion->categorias_usuario->isNotEmpty() && !$eleccion->usuario_ha_votado_todo)
                    <a href="{{ route('elections.vote', $eleccion) }}" class="btn btn-primary btn-sm">Votar</a>
                @elseif($eleccion->usuario_ha_votado_todo && $eleccion->categorias_usuario->isNotEmpty())
                    <span class="badge badge-active">Ya has votado</span>
                @endif

                {{-- PROYECTO:
                     Resultados visibles si están habilitados en tiempo real o si la votación cerró. --}}
                @if($eleccion->realtime_results_enabled || $eleccion->status === 'closed')
                    <a href="{{ route('elections.results', $eleccion) }}" class="btn btn-secondary btn-sm">Ver resultados</a>
                @endif
            </div>
        </div>
    @endforeach
</div>
