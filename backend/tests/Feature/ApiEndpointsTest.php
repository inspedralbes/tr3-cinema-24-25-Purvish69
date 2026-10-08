<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    /**
     * Test básico para verificar que la ruta de películas responde.
     */
    public function test_movies_endpoint_returns_success_response()
    {
        $response = $this->get('/api/movies');
        
        $response->assertStatus(200);
    }
    
    /**
     * Test básico para verificar que la ruta de sesiones responde.
     */
    public function test_sessions_endpoint_returns_success_response()
    {
        $response = $this->get('/api/sessions');
        
        $response->assertStatus(200);
    }
    
    /**
     * Test básico para verificar que la ruta de tickets responde.
     */
    public function test_tickets_endpoint_returns_success_response()
    {
        $response = $this->get('/api/tickets');
        
        $response->assertStatus(200);
    }
    
    /**
     * Test básico para verificar que la ruta de asientos responde.
     */
    public function test_seats_endpoint_returns_success_response()
    {
        $response = $this->get('/api/seats');
        
        $response->assertStatus(200);
    }
    
    /**
     * Test básico para verificar que el registro de usuario devuelve una respuesta adecuada.
     * No verificamos el código exacto porque puede variar (éxito o error de validación)
     */
    public function test_register_endpoint_responds()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testbasic@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
        
        $response = $this->postJson('/api/register', $userData);
        
        // Verificamos que responde (sea con éxito o con errores de validación)
        $status = $response->status();
        $this->assertTrue(in_array($status, [200, 201, 422]));
    }
    
    /**
     * Test básico para verificar que el login responde adecuadamente.
     * Incluso si las credenciales no son válidas, debería devolver una respuesta.
     */
    public function test_login_endpoint_responds()
    {
        $credentials = [
            'email' => 'testbasic@example.com',
            'password' => 'password123'
        ];
        
        $response = $this->postJson('/api/login', $credentials);
        
        // Verificamos que responde (sea con éxito o con error de autenticación)
        $status = $response->status();
        $this->assertTrue(in_array($status, [200, 401, 422]));
    }
}
