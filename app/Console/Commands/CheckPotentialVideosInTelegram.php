<?php

namespace App\Console\Commands;

use App\Enum\SocialNetwork;
use App\Jobs\CheckPotentialVideoJob;
use Illuminate\Console\Command;

class CheckPotentialVideosInTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-potential-videos-in-telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check potential video in telegram';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(): void
    {
        CheckPotentialVideoJob::dispatch(SocialNetwork::Telegram->value)->onQueue('content');
    }
}
