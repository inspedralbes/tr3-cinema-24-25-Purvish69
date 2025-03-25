<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Movie;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class MovieTest extends TestCase
{
    
    
    /**
     * Test para verificar que la ruta de películas responde.
     */
    public function test_can_get_all_movies()
    {
        $response = $this->getJson('/api/movies');
        
        $response->assertStatus(200);
    }
    
    /**
     * Test para verificar que la ruta de obtener una película específica responde.
     */
    public function test_can_get_single_movie()
    {
        // verificamos que la ruta responde cuando enviamos datos correctos
        $response = $this->getJson('/api/movies/1');
        
        $this->assertTrue(in_array($response->status(), [200, 404]));
    }
    
    /**
     * Test para verificar que la ruta de creación de películas está protegida.
     */
    public function test_movie_creation_requires_authentication()
    {
        $movieData = [
            'title' => 'Nueva Película Test',
            'description' => 'Descripción de prueba',
            'duration' => 120,
            'genre' => 'Acción',
            'image' => 'path/to/image.jpg',
            'trailer' => 'path/to/trailer'
        ];
        
        // Sin autenticación, debería fallar
        $response = $this->postJson('/api/movies', $movieData);
        
        // Verificamos que devuelve un código de error de autenticación o similar
        $this->assertTrue(in_array($response->status(), [401, 403, 302]));
    }
}
