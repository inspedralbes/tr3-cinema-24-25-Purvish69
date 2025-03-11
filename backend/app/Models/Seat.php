<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'movieSession_id',
        'fila',
        'numero',
        'tipo',
        'estado',
    ];

    // Relaciones
    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}