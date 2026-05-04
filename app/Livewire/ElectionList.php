<?php

namespace App\Livewire;

use App\Models\Election;
use Livewire\Component;

// LIVEWIRE + PROYECTO:
// Componente Livewire creado para listar votaciones disponibles.
// - LIVEWIRE: clase que extiende Component y renderiza una vista reactiva.
// - BASE LARAVEL: consultas Eloquent y auth().
// - PROYECTO: filtro de categorias disponibles para cada usuario.
class ElectionList extends Component
{
    // LIVEWIRE:
    // render() se ejecuta para preparar datos y pintar la vista.
    public function render()
    {
        $usuario = auth()->user();
        $idsCategoriasUsuario = $usuario->categories->pluck('id');

        // PROYECTO + BASE LARAVEL:
        // Trae elecciones con sus categorias y votos para evitar consultas repetidas.
        $elecciones = Election::with(['categories.votes'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($eleccion) use ($usuario, $idsCategoriasUsuario) {
                // PROYECTO:
                // categorias_usuario = solo categorias asignadas directamente a este usuario.
                $eleccion->categorias_usuario = $eleccion->categories->filter(function ($categoria) use ($idsCategoriasUsuario) {
                    return $idsCategoriasUsuario->contains($categoria->id);
                });

                // PROYECTO:
                // usuario_ha_votado_todo = true si ya voto en todas sus categorias permitidas.
                $eleccion->usuario_ha_votado_todo = $eleccion->categorias_usuario->every(function ($categoria) use ($usuario) {
                    return $usuario->hasVotedInCategory($categoria->id);
                });

                return $eleccion;
            });

        // LIVEWIRE:
        // layout() usa el layout principal de Blade.
        return view('livewire.election-list', ['elecciones' => $elecciones])
            ->layout('layouts.app', ['title' => 'Votaciones disponibles']);
    }
}
