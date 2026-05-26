<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Election;
use App\Models\Option;
use Livewire\Component;

// LIVEWIRE + PROYECTO:
// Componente Livewire creado para gestionar votaciones, categorías y opciones.
// LIVEWIRE maneja estado reactivo de formularios.
// BASE LARAVEL maneja validación, ORM Eloquent y tablas pivote con sync().
// PROYECTO define las acciones concretas del panel admin.
class ElectionManager extends Component
{
    // LIVEWIRE + PROYECTO:
    // Estado visual de la interfaz.
    public bool $mostrarFormulario = false;
    public bool $mostrarFormularioCategoria = false;
    public ?int $idEdicion = null;
    public ?int $idEleccionGestionada = null;
    public ?int $idCategoriaEdicion = null;

    // LIVEWIRE + PROYECTO:
    // Datos del formulario de votación.
    public string $titulo = '';
    public string $descripcion = '';
    public string $tipo = 'standard';
    public bool $es_anonima = false;
    public bool $resultados_tiempo_real = true;
    public string $fecha_inicio = '';
    public string $fecha_fin = '';
    public string $estado = 'pending';

    // LIVEWIRE + PROYECTO:
    // Datos del formulario de categoría.
    public string $nombre_categoria = '';
    public int $maximo_selecciones_categoria = 1;
    public string $texto_opciones_categoria = '';

    // PROYECTO + LIVEWIRE:
    // Prepara el formulario para crear una nueva votación.
    public function crear()
    {
        $this->reset(['idEdicion', 'titulo', 'descripcion', 'tipo', 'es_anonima', 'resultados_tiempo_real', 'fecha_inicio', 'fecha_fin', 'estado']);
        $this->estado = 'pending';
        $this->resultados_tiempo_real = true;
        $this->mostrarFormulario = true;
    }

    // PROYECTO:
    // Carga una votación existente dentro del formulario.
    public function editar(int $id)
    {
        $eleccion = Election::findOrFail($id);
        $this->idEdicion = $eleccion->id;
        $this->titulo = $eleccion->title;
        $this->descripcion = $eleccion->description ?? '';
        $this->tipo = $eleccion->type;
        $this->es_anonima = $eleccion->is_anonymous;
        $this->resultados_tiempo_real = $eleccion->realtime_results_enabled;
        $this->fecha_inicio = $eleccion->start_date->format('Y-m-d\TH:i');
        $this->fecha_fin = $eleccion->end_date->format('Y-m-d\TH:i');
        $this->estado = $eleccion->status;
        $this->mostrarFormulario = true;
    }

    // PROYECTO + BASE LARAVEL:
    // Guarda una votación nueva o actualiza una existente.
    public function guardar()
    {
        $this->validate([
            'titulo' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado' => 'required|in:pending,active,closed',
        ]);

        $datos = [
            'title' => $this->titulo,
            'description' => $this->descripcion,
            'type' => $this->tipo,
            'is_anonymous' => $this->es_anonima,
            'realtime_results_enabled' => $this->resultados_tiempo_real,
            'start_date' => $this->fecha_inicio,
            'end_date' => $this->fecha_fin,
            'status' => $this->estado,
        ];

        if ($this->idEdicion) {
            Election::findOrFail($this->idEdicion)->update($datos);
        } else {
            Election::create($datos);
        }

        $this->mostrarFormulario = false;
        session()->flash('message', 'Votación guardada correctamente.');
    }

    // PROYECTO:
    // Elimina una votación.
    public function eliminar(int $id)
    {
        Election::findOrFail($id)->delete();
        session()->flash('message', 'Votación eliminada.');
    }

    // LIVEWIRE + PROYECTO:
    // Abre la zona de categorías de una votación concreta.
    public function gestionarCategorias(int $id)
    {
        $this->idEleccionGestionada = $id;
    }

    // LIVEWIRE:
    // Cierra el panel de categorías.
    public function cerrarCategorias()
    {
        $this->idEleccionGestionada = null;
    }

    // PROYECTO + LIVEWIRE:
    // Prepara el formulario para crear una categoría.
    public function crearCategoria()
    {
        $this->reset(['idCategoriaEdicion', 'nombre_categoria', 'maximo_selecciones_categoria', 'texto_opciones_categoria']);
        $this->maximo_selecciones_categoria = 1;
        $this->mostrarFormularioCategoria = true;
    }

    // PROYECTO:
    // Carga una categoría en el formulario de edición.
    public function editarCategoria(int $id)
    {
        $categoria = Category::with('options')->findOrFail($id);
        $this->idCategoriaEdicion = $categoria->id;
        $this->nombre_categoria = $categoria->name;
        $this->maximo_selecciones_categoria = $categoria->max_selections;
        $this->texto_opciones_categoria = $categoria->options->pluck('label')->implode("\n");
        $this->mostrarFormularioCategoria = true;
    }

    // PROYECTO + BASE LARAVEL:
    // Guarda la categoría y sus opciones.
    public function guardarCategoria()
    {
        $this->validate([
            'nombre_categoria' => 'required|string',
            'maximo_selecciones_categoria' => 'required|integer|min:1',
            'texto_opciones_categoria' => 'required|string',
        ]);

        $datos = [
            'election_id' => $this->idEleccionGestionada,
            'name' => $this->nombre_categoria,
            'max_selections' => $this->maximo_selecciones_categoria,
        ];

        if ($this->idCategoriaEdicion) {
            $categoria = Category::findOrFail($this->idCategoriaEdicion);
            $categoria->update($datos);
        } else {
            $categoria = Category::create($datos);
        }

        // PROYECTO:
        // Se borran opciones antiguas y se recrean desde el textarea.
        $categoria->options()->delete();
        foreach (array_filter(array_map('trim', explode("\n", $this->texto_opciones_categoria))) as $etiqueta) {
            Option::create(['category_id' => $categoria->id, 'label' => $etiqueta]);
        }

        $this->mostrarFormularioCategoria = false;
        session()->flash('message', 'Categoría guardada correctamente.');
    }

    // PROYECTO:
    // Elimina una categoría.
    public function eliminarCategoria(int $id)
    {
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Categoría eliminada.');
    }

    // LIVEWIRE + PROYECTO:
    // Renderiza la tabla principal de votaciones y, si hace falta, el panel de categorías.
    public function render()
    {
        $elecciones = Election::with('categories.options')->orderBy('created_at', 'desc')->get();
        $eleccionGestionada = $this->idEleccionGestionada
            ? Election::with('categories.options')->find($this->idEleccionGestionada)
            : null;

        return view('livewire.admin.election-manager', [
            'elecciones' => $elecciones,
            'eleccionGestionada' => $eleccionGestionada,
        ])->layout('layouts.app', ['title' => 'Gestión de Votaciones']);
    }
}
