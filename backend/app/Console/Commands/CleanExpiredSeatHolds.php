<?php

namespace App\Console\Commands;

use App\Models\SeatHold;
use Illuminate\Console\Command;

class CleanExpiredSeatHolds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holds:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra los bloqueos temporales de asientos caducados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = SeatHold::where('expires_at', '<=', now())->delete();

        $this->info("Holds caducados eliminados: {$deleted}");

        return Command::SUCCESS;
    }
}
