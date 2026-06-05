<?php

namespace App\Livewire;

use App\Models\Election;
use App\Models\Vote;
use Livewire\Component;

// LIVEWIRE + PROYECTO:
// Componente Livewire creado para la pantalla de resultados.
class ElectionResults extends Component
{
    // LIVEWIRE:
    // Propiedad pública: se comparte con la vista.
    public Election $election;

    // LIVEWIRE + BASE LARAVEL:
    // mount() se ejecuta al inicializar el componente.
    // Laravel inyecta la elección desde la ruta.
    public function mount(Election $election)
    {
        // PROYECTO:
        // Control de acceso: los resultados solo se pueden ver si están
        // habilitados en tiempo real o si la votación ya está cerrada.
        // Esto evita ver resultados ocultos entrando directo a la URL.
        if (! $election->realtime_results_enabled && $election->status !== 'closed') {
            abort(403, 'Los resultados de esta votación no están disponibles todavía.');
        }

        $this->election = $election;
    }

    // LIVEWIRE + PROYECTO:
    // Propiedad computada de Livewire:
    // puede usarse como $this->results en render().
    public function getResultsProperty()
    {
        $results = [];

        foreach ($this->election->categories()->with('options')->get() as $category) {
            // PROYECTO:
            // Una sola consulta agrupada por opción en vez de un count por opción
            // (evita el problema N+1). Devuelve [option_id => nº de votos].
            $conteoPorOpcion = Vote::where('category_id', $category->id)
                ->selectRaw('option_id, COUNT(*) as total')
                ->groupBy('option_id')
                ->pluck('total', 'option_id');

            // PROYECTO:
            // Total de votos emitidos en la categoría.
            $totalVotes = (int) $conteoPorOpcion->sum();

            // PROYECTO:
            // Conteo y porcentaje por opción.
            $options = $category->options->map(function ($option) use ($conteoPorOpcion, $totalVotes) {
                $count = (int) ($conteoPorOpcion[$option->id] ?? 0);
                return [
                    'label' => $option->label,
                    'votes' => $count,
                    'percentage' => $totalVotes > 0 ? round(($count / $totalVotes) * 100, 1) : 0,
                ];
            });

            // PROYECTO:
            // Censo potencial: usuarios asignados directamente a esta categoría.
            $eligibleVoters = $category->users()->count();

            // PROYECTO:
            // participation = participación sobre censo potencial.
            $results[] = [
                'category' => $category->name,
                'category_id' => $category->id,
                'options' => $options->toArray(),
                'total_votes' => $totalVotes,
                'eligible_voters' => $eligibleVoters,
                'participation' => $eligibleVoters > 0 ? round(($totalVotes / $eligibleVoters) * 100, 1) : 0,
            ];
        }

        return $results;
    }

    public function render()
    {
        // LIVEWIRE:
        // Pasa results a Blade y aplica layout principal.
        return view('livewire.election-results', ['results' => $this->results])
            ->layout('layouts.app', ['title' => 'Resultados - ' . $this->election->title]);
    }
}
