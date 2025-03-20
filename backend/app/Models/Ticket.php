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
        'payment_id',
        'precio',
        'codigo_confirmacion'
    ];

    

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la sesión de película
    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }

    // Relación con la butaca
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    // Relación con el pago
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}