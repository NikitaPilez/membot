<?php

namespace App\Jobs;

use App\Services\CheckPotentialVideoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckPotentialVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $socialNetwork;

    /**
     * Create a new job instance.
     */
    public function __construct(string $socialNetwork)
    {
        $this->socialNetwork = $socialNetwork;
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(CheckPotentialVideoService $service): void
    {
        $service->run($this->socialNetwork);
    }
}
