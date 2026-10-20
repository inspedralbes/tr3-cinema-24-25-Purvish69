<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatHold extends Model
{
    use HasFactory;

    protected $table = 'seat_holds';

    protected $fillable = [
        'funcion_id',
        'asiento',
        'user_id',
        'hold_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class, 'funcion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
