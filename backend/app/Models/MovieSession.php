<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieSession extends Model
{
    use HasFactory;

    protected $table = 'movieSessions';

    protected $fillable = [
        'movie_id',
        'fecha',
        'hora',
        'estado',
        'dia_espectador',
        'fila_vip_activa',
    ];

    protected $casts = [
        'fecha' => 'date',
        'dia_espectador' => 'boolean',
        'fila_vip_activa' => 'boolean',
    ];

    // Relaciones

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
    
    public function tickets()
    {
        return $this->hasMany(Ticket::class , 'movieSession_id');
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

}