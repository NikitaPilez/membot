<?php

namespace App\Console\Commands;

use App\Jobs\CheckPotentialVideoJob;
use Illuminate\Console\Command;

class CheckPotentialVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-potential-videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check potential video';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(): void
    {
        CheckPotentialVideoJob::dispatch();
    }
}
