<?php

namespace App\Console\Commands;

use App\Jobs\UpdateChannelPostStatJob;
use Illuminate\Console\Command;

class UpdateChannelPostStatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-channel-post-stat-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update channel post stat';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        UpdateChannelPostStatJob::dispatch()->onQueue('default');
    }
}
