<?php

namespace Tests\Feature;

use App\Models\MovieSession;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\SeatHold;
use App\Models\User;
use Tests\TestCase;

class SeatHoldTest extends TestCase
{
    public function test_can_hold_seats_and_detect_conflict(): void
    {
        $session = MovieSession::first();
        $this->assertNotNull($session);

        $tokenA = 'token-user-a-' . uniqid();
        $tokenB = 'token-user-b-' . uniqid();

        // 1. User A holds seat A-1
        $response = $this->postJson('/api/holds', [
            'funcion_id' => $session->id,
            'asientos'   => ['A-1'],
            'hold_token' => $tokenA,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('seat_holds', [
            'funcion_id' => $session->id,
            'asiento'    => 'A-1',
            'hold_token' => $tokenA,
        ]);

        // 2. User B tries to hold A-1 -> 409 Conflict
        $responseConflict = $this->postJson('/api/holds', [
            'funcion_id' => $session->id,
            'asientos'   => ['A-1', 'A-2'],
            'hold_token' => $tokenB,
        ]);

        $responseConflict->assertStatus(409);
        $responseConflict->assertJsonFragment(['conflicts' => ['A-1']]);
        // Neither A-1 nor A-2 held by B
        $this->assertDatabaseMissing('seat_holds', [
            'funcion_id' => $session->id,
            'asiento'    => 'A-2',
            'hold_token' => $tokenB,
        ]);

        // 3. User B calls GET /funciones/{id}/asientos -> A-1 is in retenidos
        $responseAsientos = $this->getJson("/api/funciones/{$session->id}/asientos?hold_token={$tokenB}");
        $responseAsientos->assertStatus(200);
        $this->assertTrue(in_array('A-1', $responseAsientos->json('retenidos')));

        // 4. User A releases A-1
        $responseDelete = $this->deleteJson('/api/holds', [
            'funcion_id' => $session->id,
            'asientos'   => ['A-1'],
            'hold_token' => $tokenA,
        ]);
        $responseDelete->assertStatus(200);

        $this->assertDatabaseMissing('seat_holds', [
            'funcion_id' => $session->id,
            'asiento'    => 'A-1',
        ]);
    }

    public function test_holds_clean_command_removes_expired_holds(): void
    {
        $session = MovieSession::first();

        // Create an expired hold
        SeatHold::create([
            'funcion_id' => $session->id,
            'asiento'    => 'B-9',
            'hold_token' => 'expired-token',
            'expires_at' => now()->subMinutes(10),
        ]);

        $this->artisan('holds:clean')->assertExitCode(0);

        $this->assertDatabaseMissing('seat_holds', [
            'asiento'    => 'B-9',
            'hold_token' => 'expired-token',
        ]);
    }
}
