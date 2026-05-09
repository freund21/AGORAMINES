<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PROYECTO + BASE LARAVEL:
// Modelo Eloquent creado para este proyecto para la tabla categories.
// Una categoría pertenece a una elección y contiene opciones de voto.
class Category extends Model
{
    // BASE LARAVEL + PROYECTO:
    // $fillable es de Laravel; estos campos concretos son de la aplicación.
    protected $fillable = ['election_id', 'name', 'max_selections'];

    // PROYECTO:
    // Relación: esta categoría pertenece a una elección.
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    // PROYECTO:
    // Relación: una categoría tiene muchas opciones.
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    // PROYECTO:
    // Relación muchos a muchos: usuarios habilitados para votar aquí.
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // PROYECTO:
    // Relación: votos emitidos en esta categoría.
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
