<?php

namespace Database\Seeders;

use App\Services\MovieSessionService;
use Illuminate\Database\Seeder;

class MovieSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessionService = new MovieSessionService();
        $sessionService->generateSessions(null, true);
    }
}
