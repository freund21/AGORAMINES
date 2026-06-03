<?php

namespace App\Livewire;

use App\Models\Election;
use App\Models\Vote;
use Illuminate\Support\Str;
use Livewire\Component;

// LIVEWIRE + PROYECTO:
// Componente Livewire creado para controlar toda la pantalla de votación.
// LIVEWIRE permite trabajar con PHP en el servidor y actualizar la interfaz
// sin recargar la página completa.
// BASE LARAVEL aporta Eloquent, auth(), session(), redirect(), encrypt(), Str.
// PROYECTO define cómo se seleccionan, guardan y verifican los votos.
class VotingForm extends Component
{
    // LIVEWIRE + PROYECTO:
    // Propiedades públicas del componente.
    // Livewire las sincroniza con la vista Blade automáticamente.
    public Election $eleccion;
    public array $opcionesSeleccionadas = [];
    public array $comprobantes = [];
    public bool $haVotado = false;

    // LIVEWIRE + BASE LARAVEL:
    // mount() funciona como inicialización del componente.
    // Laravel inyecta automáticamente la elección desde la ruta.
    public function mount(Election $eleccion)
    {
        $this->eleccion = $eleccion;

    }

    // LIVEWIRE + PROYECTO:
    // Método llamado desde la vista con wire:click.
    // Sirve para seleccionar o deseleccionar opciones.
    public function seleccionarOpcion($idCategoria, $idOpcion)
    {
        $categoria = $this->eleccion->categories()->findOrFail($idCategoria);

        // PROYECTO:
        // Si solo se puede elegir una opción, se reemplaza directamente.
        if ($categoria->max_selections <= 1) {
            $this->opcionesSeleccionadas[$idCategoria] = [$idOpcion];
            return;
        }

        // PROYECTO:
        // Si se permiten varias, se alterna la opción dentro del array.
        $seleccionActual = $this->opcionesSeleccionadas[$idCategoria] ?? [];

        if (in_array($idOpcion, $seleccionActual)) {
            $seleccionActual = array_values(array_diff($seleccionActual, [$idOpcion]));
        } elseif (count($seleccionActual) < $categoria->max_selections) {
            $seleccionActual[] = $idOpcion;
        }

        $this->opcionesSeleccionadas[$idCategoria] = $seleccionActual;
    }

    // PROYECTO + LIVEWIRE:
    // Guarda los votos del usuario.
    // Lógica de negocio del proyecto:
    // - valida que haya selección
    // - evita doble voto por categoría
    // - guarda voto y comprobante
    public function enviarVotos()
    {
        $usuario = auth()->user();

        // PROYECTO:
        // Solo se cargan las categorías en las que el usuario puede votar.
        $categoriasPermitidas = $this->eleccion->categories()
            ->whereHas('users', function ($consulta) use ($usuario) {
                $consulta->where('users.id', $usuario->id);
            })->get();

        // LIVEWIRE + PROYECTO:
        // Se vacían los comprobantes antes de volver a generarlos.
        $this->comprobantes = [];

        foreach ($categoriasPermitidas as $categoria) {
            // PROYECTO:
            // Si ya ha votado en la categoría, se salta.
            if ($usuario->hasVotedInCategory($categoria->id)) {
                continue;
            }

            $idsOpciones = $this->opcionesSeleccionadas[$categoria->id] ?? [];
            if (empty($idsOpciones)) {
                // LIVEWIRE:
                // addError muestra errores en la vista.
                $this->addError('voto', "Debes seleccionar una opción en: {$categoria->name}");
                return;
            }

            foreach ($idsOpciones as $idOpcion) {
                $opcion = $categoria->options()->findOrFail($idOpcion);

                // BASE LARAVEL + PROYECTO:
                // Str es una utilidad de Laravel.
                // Aquí se crea un código aleatorio para verificar el voto después.
                $codigoComprobante = strtoupper(Str::random(12));

                // BASE LARAVEL + PROYECTO:
                // Eloquent inserta el voto en base de datos.
                Vote::create([
                    'election_id' => $this->eleccion->id,
                    'category_id' => $categoria->id,
                    'option_id' => $opcion->id,
                    'user_id' => $this->eleccion->is_anonymous ? null : $usuario->id,
                    'encrypted_vote' => encrypt($opcion->label),
                    'receipt_code' => $codigoComprobante,
                ]);

                // LIVEWIRE + PROYECTO:
                // Este array es solo para la interfaz, no es una tabla de base de datos.
                $this->comprobantes[] = [
                    'categoria' => $categoria->name,
                    'codigo_comprobante' => $codigoComprobante,
                ];
            }
        }

        // LIVEWIRE:
        // Se usa para cambiar la vista y mostrar el mensaje final.
        $this->haVotado = true;
    }

    // LIVEWIRE:
    // render() prepara los datos que necesita la vista Blade.
    public function render()
    {
        $usuario = auth()->user();

        $categorias = $this->eleccion->categories()
            ->with('options')
            ->whereHas('users', function ($consulta) use ($usuario) {
                $consulta->where('users.id', $usuario->id);
            })->get();

        return view('livewire.voting-form', [
            'categorias' => $categorias,
            'eleccion' => $this->eleccion,
        ])->layout('layouts.app', ['title' => 'Votar - ' . $this->eleccion->title]);
    }
}
