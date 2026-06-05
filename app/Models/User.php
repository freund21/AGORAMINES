<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// BASE LARAVEL:
// Modelo Eloquent de Laravel para la tabla users.
// Laravel ya trae un modelo User parecido cuando se crea un proyecto con autenticación.
// Eloquent = capa ORM: trabajar con filas de BD como objetos PHP.
//
// NOTA LIVEWIRE:
// Livewire no crea este modelo por defecto. Los componentes Livewire del proyecto
// lo usan para leer usuarios, comprobar roles, votos, etc., pero el modelo pertenece a Laravel.
class User extends Authenticatable
{
    // BASE LARAVEL:
    // HasFactory permite crear usuarios falsos en tests/seeders.
    // Notifiable permite enviar notificaciones al usuario.
    use HasFactory, Notifiable;

    // MEZCLA BASE LARAVEL + PROYECTO:
    // Laravel suele traer name, email y password.
    // En este proyecto se han personalizado los campos:
    // dni, username, full_name y role_id son propios de la aplicación.
    protected $fillable = [
        'dni',
        'username',
        'full_name',
        'email',
        'password',
        'role_id',
    ];

    // BASE LARAVEL:
    // Campos que no se exponen al convertir el usuario a array/JSON.
    // password y remember_token suelen venir protegidos por defecto.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // BASE LARAVEL:
    // Conversores automáticos de Laravel.
    // password => hashed: al guardar, Laravel aplica hash automáticamente.
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // PROYECTO:
    // Relación propia de esta aplicación: un usuario pertenece a un rol.
    // Sirve para distinguir admin/votante.
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // PROYECTO:
    // Relación muchos a muchos con categorías.
    // Representa las categorías en las que este usuario puede votar.
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // PROYECTO:
    // Relación propia del sistema de votaciones: un usuario tiene muchos votos.
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // PROYECTO:
    // Participaciones del usuario (registro de "ya votó", independiente del voto).
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    // PROYECTO:
    // Lógica de negocio propia para saber si el usuario tiene rol administrador.
    // Se usa el operador null-safe (?->) para no fallar si el rol es nulo.
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    // PROYECTO:
    // Comprueba si este usuario ya votó en una categoría concreta.
    // Se consulta participations (no votes) para que funcione también en
    // votaciones anónimas, donde el voto no guarda el user_id.
    public function hasVotedInCategory(int $categoryId): bool
    {
        return $this->participations()->where('category_id', $categoryId)->exists();
    }
}
