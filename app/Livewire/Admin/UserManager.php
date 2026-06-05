<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;

// LIVEWIRE + PROYECTO:
// Componente Livewire creado para el panel admin de usuarios.
// LIVEWIRE: estado de interfaz + métodos llamados desde botones.
// BASE LARAVEL: validación, Eloquent, relaciones y sesiones flash.
// PROYECTO: alta, edición, borrado y asignación de roles/categorías.
class UserManager extends Component
{
    // LIVEWIRE + PROYECTO:
    // Estado del formulario y del usuario en edición.
    public bool $mostrarFormulario = false;
    public ?int $idEdicion = null;

    // LIVEWIRE + PROYECTO:
    // Datos del formulario.
    public string $dni = '';
    public string $usuario = '';
    public string $nombre_completo = '';
    public string $email = '';
    public string $contrasena = '';
    public int $id_rol = 2;
    public array $ids_categorias = [];

    // PROYECTO + LIVEWIRE:
    // Prepara el formulario para un nuevo usuario.
    public function crear()
    {
        $this->reset(['idEdicion', 'dni', 'usuario', 'nombre_completo', 'email', 'contrasena', 'id_rol', 'ids_categorias']);
        $this->id_rol = 2;
        $this->mostrarFormulario = true;
    }

    // PROYECTO:
    // Carga un usuario existente para editarlo.
    public function editar(int $id)
    {
        $usuario = User::findOrFail($id);
        $this->idEdicion = $usuario->id;
        $this->dni = $usuario->dni;
        $this->usuario = $usuario->username;
        $this->nombre_completo = $usuario->full_name;
        $this->email = $usuario->email;
        $this->contrasena = '';
        $this->id_rol = $usuario->role_id;
        $this->ids_categorias = $usuario->categories->pluck('id')->toArray();
        $this->mostrarFormulario = true;
    }

    // PROYECTO + BASE LARAVEL:
    // Guarda el usuario nuevo o actualiza uno existente.
    public function guardar()
    {
        $reglas = [
            'dni' => 'required|string|unique:users,dni,' . $this->idEdicion,
            'usuario' => 'required|string|unique:users,username,' . $this->idEdicion,
            'nombre_completo' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $this->idEdicion,
            'id_rol' => 'required|exists:roles,id',
        ];

        if (! $this->idEdicion) {
            $reglas['contrasena'] = 'required|min:6';
        }

        $this->validate($reglas);

        $datos = [
            'dni' => $this->dni,
            'username' => $this->usuario,
            'full_name' => $this->nombre_completo,
            'email' => $this->email,
            'role_id' => $this->id_rol,
        ];

        if ($this->contrasena) {
            $datos['password'] = $this->contrasena;
        }

        if ($this->idEdicion) {
            $usuario = User::findOrFail($this->idEdicion);
            $usuario->update($datos);
        } else {
            $usuario = User::create($datos);
        }

        // BASE LARAVEL + PROYECTO:
        // sync() actualiza la relación muchos a muchos con categorías.
        // Un usuario puede estar relacionado con varias categorías de voto.
        $usuario->categories()->sync($this->ids_categorias);

        $this->mostrarFormulario = false;
        session()->flash('message', 'Usuario guardado correctamente.');
    }

    // PROYECTO:
    // Elimina un usuario.
    public function eliminar(int $id)
    {
        // PROYECTO:
        // Un administrador no puede eliminar su propia cuenta (evita quedarse
        // sin acceso o dejar el sistema sin administradores por error).
        if ($id === auth()->id()) {
            session()->flash('message', 'No puedes eliminar tu propia cuenta.');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('message', 'Usuario eliminado.');
    }

    // LIVEWIRE + PROYECTO:
    // Prepara datos para la tabla del panel admin.
    public function render()
    {
        return view('livewire.admin.user-manager', [
            'usuarios' => User::with(['role', 'categories.election'])->get(),
            'roles' => Role::all(),
            'categorias' => Category::with('election')->orderBy('election_id')->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Gestión de Usuarios']);
    }
}
