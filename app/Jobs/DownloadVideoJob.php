<?php

namespace App\Jobs;

use App\Helpers\Download\InstagramContent;
use App\Helpers\Download\TikTokContentVideo;
use App\Helpers\Download\YoutubeContentVideo;
use App\Models\Video;
use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?string $url;
    private ?string $type;
    private ?string $contentUrl;

    public function __construct(?string $url = null, ?string $type = null, ?string $contentUrl = null)
    {
        $this->url = $url;
        $this->contentUrl = $contentUrl;
        $this->type = $type;
    }

    public function handle()
    {
        $videoDownloader = match ($this->type) {
            "tiktok" => new TikTokContentVideo(),
            "youtube" => new YoutubeContentVideo(),
            "instagram" => new InstagramContent(),
            default => null,
        };

        if ($videoDownloader) {
            $this->contentUrl = $videoDownloader->getContentUrl($this->url);
            $content = $videoDownloader->getContent($this->contentUrl);
        } else {
            $content = file_get_contents($this->contentUrl);
        }

        $fileName = ($this->type ? $this->type . " " : "") . date("Y-m-d H:i") . ".mp4";

        /** @var GoogleDriveService $googleDriveService */
        $googleDriveService = app(GoogleDriveService::class);
        $driveFile = $googleDriveService->createFile($content, $fileName);

        Video::create([
            "google_file_id" => $driveFile->getId(),
            "name" => $fileName,
            "url" => $this->url,
            "content_url" => $this->contentUrl,
            "type" => $this->type,
        ]);
    }
}
