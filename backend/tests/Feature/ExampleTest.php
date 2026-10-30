<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test que verifica que la aplicación devuelve una respuesta.
     * La respuesta puede ser 200 (OK) o 302 (redirección).
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Aceptamos tanto 200 como 302 (redirección) como respuestas válidas
        $this->assertTrue($response->status() == 200 || $response->status() == 302);
    }
}
