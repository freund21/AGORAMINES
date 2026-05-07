<div>
    {{-- LIVEWIRE + PROYECTO:
         Vista Blade del componente ElectionResults.
         Blade pinta el HTML y JavaScript usa Chart.js para dibujar las gráficas. --}}
    <div class="flex-between mb-2">
        <h2>Resultados: {{ $election->title }}</h2>
        <a href="{{ route('elections.index') }}" class="btn btn-secondary btn-sm">Volver</a>
    </div>

    {{-- BASE LARAVEL + PROYECTO:
         Aquí Blade convierte el array PHP $results a JSON para que JavaScript pueda leerlo. --}}
    <div id="results-data" data-results='@json($results)' style="display:none;"></div>

    @foreach($results as $indice => $resultado)
        <div class="card">
            <h3>{{ $resultado['category'] }}</h3>

            <div class="flex-between mb-1">
                <span class="text-muted">Participación: {{ $resultado['total_votes'] }} / {{ $resultado['eligible_voters'] }} votantes</span>
                <strong>{{ $resultado['participation'] }}%</strong>
            </div>
            <div class="participation-bar mb-2">
                <div class="participation-fill" style="width: {{ $resultado['participation'] }}%"></div>
            </div>

            {{-- PROYECTO:
                 Cada categoría tiene su propio canvas donde Chart.js pintará la gráfica. --}}
            <div class="chart-container">
                <canvas id="chart-{{ $resultado['category_id'] }}"></canvas>
            </div>
        </div>
    @endforeach

    <script>
        // PROYECTO:
        // Función JavaScript escrita a mano.
        // Recorre los resultados y crea una gráfica circular por cada categoría.
        function renderCharts(results) {
            const colors = [ '#d99d06','#db2777','#059669','#1a56db', '#dc2626',  '#2e0674',  '#0891b2'];
            results.forEach(function(result) {
                const canvasId = 'chart-' + result.category_id;
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;

                // PROYECTO:
                // Si ya había una gráfica previa en ese canvas, se destruye antes de recrearla.
                if (canvas._chartInstance) {
                    canvas._chartInstance.destroy();
                }

                canvas._chartInstance = new Chart(canvas, {
                    type: 'pie',
                    data: {
                        labels: result.options.map(function(o) { return o.label; }),
                        datasets: [{
                            label: 'Votos',
                            data: result.options.map(function(o) { return o.votes; }),
                            backgroundColor: result.options.map(function(_, i) { return colors[i % colors.length]; }),
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true }
                        }
                    }
                });
            });
        }

        // PROYECTO:
        // Cuando el DOM termina de cargar, se leen los resultados y se pintan las gráficas.
        document.addEventListener('DOMContentLoaded', function() {
            var dataEl = document.getElementById('results-data');
            if (dataEl) {
                renderCharts(JSON.parse(dataEl.dataset.results));
            }
        });

        // LIVEWIRE + PROYECTO:
        // Si la votación sigue abierta y tiene resultados en tiempo real,
        // Livewire refresca el componente cada 10 segundos y se repintan las gráficas.
        @if($election->realtime_results_enabled && $election->isOpen())
        setInterval(function() {
            @this.call('$refresh').then(function() {
                setTimeout(function() {
                    var dataEl = document.getElementById('results-data');
                    if (dataEl) {
                        renderCharts(JSON.parse(dataEl.dataset.results));
                    }
                }, 200);
            });
        }, 10000);
        @endif
    </script>
</div>
