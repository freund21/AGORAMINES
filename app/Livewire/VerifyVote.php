<?php

namespace App\Livewire;

use App\Models\Vote;
use Livewire\Component;

// LIVEWIRE + PROYECTO:
// Componente Livewire creado para verificar un voto por código.
class VerifyVote extends Component
{
    // LIVEWIRE + PROYECTO:
    // Propiedades públicas sincronizadas con inputs de la vista.
    public string $codigoComprobante = '';
    public ?array $informacionVoto = null;
    public bool $buscado = false;

    // LIVEWIRE + PROYECTO:
    // Método llamado desde la vista con wire:click o Enter.
    public function verificar()
    {
        $this->buscado = true;
        $this->informacionVoto = null;

        // BASE LARAVEL + PROYECTO:
        // Laravel/Eloquent busca voto por receipt_code.
        $voto = Vote::where('receipt_code', $this->codigoComprobante)->first();

        if ($voto) {
            // BASE LARAVEL + PROYECTO:
            // decrypt() usa la APP_KEY de Laravel para recuperar el valor cifrado.
            $this->informacionVoto = [
                'eleccion' => $voto->election->title,
                'categoria' => $voto->category->name,
                'opcion' => decrypt($voto->encrypted_vote),
                'fecha' => $voto->created_at->format('d/m/Y H:i'),
                'codigo_comprobante' => $voto->receipt_code,
            ];
        }
    }

    public function render()
    {
        // LIVEWIRE:
        // Vista Livewire + layout Blade principal.
        return view('livewire.verify-vote')
            ->layout('layouts.app', ['title' => 'Verificar voto']);
    }
}
