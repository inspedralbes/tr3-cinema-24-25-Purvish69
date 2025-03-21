<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MovieSession;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'calificacion',
        'director',
        'actores',
        'duracion',
        'clasificacion',
        'genero',
        'lenguaje',
        'imagen',
        'poster',
        'trailer',
        'omdb_id',
    ];

    protected $casts = [
        'actores' => 'array',
    ];  

    // Relaciones

    public function sessions()
    {
        return $this->hasMany(MovieSession::class);
    }
}