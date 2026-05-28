<div>
    <h2 style="margin-bottom: 1.5rem;">Verificar voto</h2>

    <div class="card">
        <div class="form-group">
            <label for="comprobante">Código de verificación</label>
            <div class="flex gap-1">
                {{-- LIVEWIRE:
                     wire:model sincroniza el input con la propiedad $codigoComprobante del componente PHP. --}}
                <input type="text" id="comprobante" wire:model="codigoComprobante" placeholder="Introduce tu código..."
                       {{-- LIVEWIRE:
                            al pulsar Enter, llama al método verificar() del componente. --}}
                       wire:keydown.enter="verificar" style="flex: 1;">
                {{-- LIVEWIRE:
                     ejecuta verificar() sin recargar página completa. --}}
                <button wire:click="verificar" class="btn btn-primary">Verificar</button>
            </div>
        </div>

        @if($buscado && !$informacionVoto)
            <div class="alert alert-error">No se encontró ningún voto con ese código de verificación.</div>
        @endif

        @if($informacionVoto)
            <div class="receipt-box">
                <p style="font-size: 1.1rem; margin-bottom: 1rem;"><strong>Voto verificado correctamente</strong></p>
                <table style="text-align: left; margin: 0 auto; max-width: 400px;">
                    <tr><td style="padding: 0.3rem 1rem;"><strong>Votación:</strong></td><td>{{ $informacionVoto['eleccion'] }}</td></tr>
                    <tr><td style="padding: 0.3rem 1rem;"><strong>Categoría:</strong></td><td>{{ $informacionVoto['categoria'] }}</td></tr>
                    <tr><td style="padding: 0.3rem 1rem;"><strong>Opción:</strong></td><td>{{ $informacionVoto['opcion'] }}</td></tr>
                    <tr><td style="padding: 0.3rem 1rem;"><strong>Fecha:</strong></td><td>{{ $informacionVoto['fecha'] }}</td></tr>
                    <tr><td style="padding: 0.3rem 1rem;"><strong>Código:</strong></td><td class="receipt-code" style="font-size: 1rem;">{{ $informacionVoto['codigo_comprobante'] }}</td></tr>
                </table>
            </div>
        @endif
    </div>
</div>
