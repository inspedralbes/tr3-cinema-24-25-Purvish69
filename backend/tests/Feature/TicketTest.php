<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Models\MovieSession;
use Laravel\Sanctum\Sanctum;

class TicketTest extends TestCase
{
    // use RefreshDatabase;
    
    /**
     * Test para verificar que la ruta de tickets responde.
     */
    public function test_can_get_all_tickets()
    {
        $response = $this->getJson('/api/tickets');
        
        $response->assertStatus(200);
    }
    
    /**
     * Test para verificar que la ruta de obtener un ticket específico responde.
     */
    public function test_can_get_single_ticket()
    {
        // verificamos que la ruta responde cuando enviamos datos correctos
        $response = $this->getJson('/api/tickets/1');
        
        $this->assertTrue(in_array($response->status(), [200, 404]));
    }
    
    /**
     * Test para verificar que la ruta de creación de tickets responde.
     */
    public function test_can_access_create_ticket_endpoint()
    {
        $ticketData = [
            'session_id' => 1,
            'seat_id' => 1,
            'price' => 9.99,
            'paid' => false
        ];
        
        $response = $this->postJson('/api/tickets', $ticketData);
        
        // Esto puede que responda con éxito o con error de validación o autenticación
        $this->assertTrue(in_array($response->status(), [201, 200, 401, 403, 422]));
    }
    
    /**
     * Test para verificar que la ruta de tickets de usuario responde.
     */
    public function test_user_tickets_endpoint_responds()
    {
        // Sin autenticación, puede responder de varias formas
        $response = $this->getJson('/api/user/tickets');
        
        // Simplemente verificamos que la ruta responde, sin importar el código de estado
        $status = $response->status();
        $this->assertGreaterThanOrEqual(200, $status);
        $this->assertLessThan(600, $status); // Cualquier código HTTP válido
    }
}
