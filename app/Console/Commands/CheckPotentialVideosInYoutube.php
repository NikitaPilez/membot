<?php

namespace App\Console\Commands;

use App\Enum\SocialNetwork;
use App\Jobs\CheckPotentialVideoJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPotentialVideosInYoutube extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-potential-videos-in-youtube';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check potential video in youtube';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        CheckPotentialVideoJob::dispatch(SocialNetwork::Youtube->value)->onQueue('content');
    }
}
