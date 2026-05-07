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
            // Total de votos emitidos en la categoría.
            $totalVotes = Vote::where('category_id', $category->id)->count();

            // PROYECTO:
            // Conteo y porcentaje por opción.
            $options = $category->options->map(function ($option) use ($totalVotes) {
                $count = Vote::where('option_id', $option->id)->count();
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
