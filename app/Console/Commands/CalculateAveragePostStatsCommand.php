<?php

namespace App\Console\Commands;

use App\Jobs\CalculateAveragePostStatsJob;
use Illuminate\Console\Command;

class CalculateAveragePostStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-average-post-stats-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate average post stats command';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        CalculateAveragePostStatsJob::dispatch()->onQueue('stat');
    }
}
