<?php

namespace App\Jobs;

use App\Helpers\Download\InstagramContent;
use App\Helpers\Download\TikTokContentVideo;
use App\Helpers\Download\YoutubeContentVideo;
use App\Services\GoogleDriveService;
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

    public function __construct(string $url, string $type)
    {
        $this->url = $url;
        $this->type = $type;
    }

    public function handle()
    {
        $videoDownloader = match ($this->type) {
            "tiktok" => new TikTokContentVideo(),
            "youtube" => new YoutubeContentVideo(),
            "instagram" => new InstagramContent()
        };

        $content = $videoDownloader->getContent($this->url);

        app(GoogleDriveService::class)->createFile($content);
    }
}