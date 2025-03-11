<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'movieSession_id',
        'seat_id',
        'precio',
        'codigo_entrada',
    ];

    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class, 'movieSession_id'); // Especificar la clave foránea
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}