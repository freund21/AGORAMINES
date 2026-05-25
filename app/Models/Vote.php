<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PROYECTO + BASE LARAVEL:
// Modelo Eloquent creado para este proyecto para la tabla votes.
// Cada fila representa un voto guardado.
class Vote extends Model
{
    // BASE LARAVEL + PROYECTO:
    // $fillable es de Laravel; estos campos concretos guardan el voto del sistema.
    protected $fillable = [
        'election_id',
        'category_id',
        'option_id',
        'user_id',
        'encrypted_vote',
        'receipt_code',
    ];

    // PROYECTO:
    // Relación: este voto pertenece a una elección.
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    // PROYECTO:
    // Relación: este voto pertenece a una categoría.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // PROYECTO:
    // Relación: este voto apunta a una opción concreta.
    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    // PROYECTO:
    // Relación: usuario que emitió el voto (puede ser null si es anónimo).
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
