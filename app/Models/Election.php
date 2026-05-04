<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PROYECTO + BASE LARAVEL:
// Modelo Eloquent creado para este proyecto para la tabla elections.
class Election extends Model
{
    // BASE LARAVEL + PROYECTO:
    // $fillable es de Laravel; estos campos concretos definen la votacion del proyecto.
    protected $fillable = [
        'title',
        'description',
        'type',
        'is_anonymous',
        'realtime_results_enabled',
        'start_date',
        'end_date',
        'status',
    ];

    // BASE LARAVEL + PROYECTO:
    // Casts de Laravel: convierte tipos automaticamente para estos campos de votacion.
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_anonymous' => 'boolean',
            'realtime_results_enabled' => 'boolean',
        ];
    }

    // PROYECTO:
    // Relacion: una eleccion tiene muchas categorias.
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // PROYECTO:
    // Relacion: una eleccion tiene muchos votos.
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // PROYECTO:
    // Regla de negocio:
    // una eleccion esta abierta solo si:
    // - status = active
    // - fecha actual entre inicio y fin
    public function isOpen(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date);
    }
}
