<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PROYECTO + BASE LARAVEL:
// Modelo Eloquent creado para este proyecto para la tabla options.
// Cada opción es una alternativa de voto dentro de una categoría.
class Option extends Model
{
    // BASE LARAVEL + PROYECTO:
    // $fillable es de Laravel; category_id y label son campos propios del dominio.
    protected $fillable = ['category_id', 'label'];

    // PROYECTO:
    // Relación: una opción pertenece a una categoría.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // PROYECTO:
    // Relación: votos recibidos por esta opción.
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
