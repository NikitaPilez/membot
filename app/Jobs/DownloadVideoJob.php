<?php

namespace App\Jobs;

use App\Services\DownloadVideoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $url;
    private string $type;
    private ?string $comment;

    public function __construct(string $url, string $type, ?string $comment)
    {
        $this->url = $url;
        $this->type = $type;
        $this->comment = $comment;
    }

    public function handle(DownloadVideoService $service): void
    {
        $service->download($this->type, $this->url, $this->comment);
    }
}
