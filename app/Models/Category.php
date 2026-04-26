<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PROYECTO + BASE LARAVEL:
// Modelo Eloquent creado para este proyecto para la tabla categories.
// Una categoria pertenece a una eleccion y contiene opciones de voto.
class Category extends Model
{
    // BASE LARAVEL + PROYECTO:
    // $fillable es de Laravel; estos campos concretos son de la aplicacion.
    protected $fillable = ['election_id', 'name', 'max_selections'];

    // PROYECTO:
    // Relacion: esta categoria pertenece a una eleccion.
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    // PROYECTO:
    // Relacion: una categoria tiene muchas opciones.
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    // PROYECTO:
    // Relacion muchos a muchos: usuarios habilitados para votar aqui.
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // PROYECTO:
    // Relacion: votos emitidos en esta categoria.
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
