<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PROYECTO:
// Modelo Eloquent de la tabla participations.
// Registra que un usuario ya votó en una categoría concreta, sin guardar
// qué votó. Sirve para impedir el doble voto incluso en votaciones anónimas.
class Participation extends Model
{
    protected $fillable = [
        'election_id',
        'category_id',
        'user_id',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
