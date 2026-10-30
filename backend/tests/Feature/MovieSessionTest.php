<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\MovieSession;
use App\Services\MovieSessionService;
use Carbon\Carbon;
use Tests\TestCase;

class MovieSessionTest extends TestCase
{
    public function test_movie_session_service_generates_current_sessions(): void
    {
        $today = Carbon::today(config('app.timezone', 'Europe/Madrid'));

        $service = new MovieSessionService();
        $count = $service->generateSessions(7, true);

        $this->assertGreaterThan(0, $count);
        $this->assertDatabaseHas('movieSessions', [
            'fecha' => $today->format('Y-m-d'),
        ]);

        $firstSession = MovieSession::where('fecha', '>=', $today->format('Y-m-d'))->first();
        $this->assertNotNull($firstSession);
        $this->assertEquals(120, $firstSession->seats()->count());
    }

    public function test_sessions_generate_command(): void
    {
        $this->artisan('sessions:generate --days=7 --fresh')
            ->assertExitCode(0);

        $today = Carbon::today(config('app.timezone', 'Europe/Madrid'))->format('Y-m-d');
        $this->assertTrue(
            MovieSession::where('fecha', '>=', $today)->exists()
        );
    }
}
