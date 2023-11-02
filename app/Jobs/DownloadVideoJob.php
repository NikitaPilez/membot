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
    public bool $isProd;
    public ?string $description;

    private ?string $comment;

    public function __construct(string $url, string $type, bool $isProd, ?string $description, ?string $comment)
    {
        $this->url = $url;
        $this->type = $type;
        $this->isProd = $isProd;
        $this->comment = $comment;
        $this->description = $description;
    }

    public function handle(DownloadVideoService $service): void
    {
        $service->download($this->type, $this->url, $this->isProd, $this->description, $this->comment);
    }
}
