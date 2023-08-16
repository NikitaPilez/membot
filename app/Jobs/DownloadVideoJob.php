<?php

namespace App\Jobs;

use App\Helpers\Download\InstagramContent;
use App\Helpers\Download\SimpleConverter;
use App\Helpers\Download\TikTokContentVideo;
use App\Helpers\Download\YoutubeContentVideo;
use App\Models\Video;
use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

    public function handle(): void
    {
        $videoDownloader = match ($this->type) {
            'tiktok' => new TikTokContentVideo(),
            'youtube' => new YoutubeContentVideo(),
            'instagram' => new InstagramContent(),
            default => new SimpleConverter(),
        };

        $contentUrlResponse = $videoDownloader->getContentUrl($this->url);

        if (!$contentUrlResponse->success) {
            Log::channel('content')->error('Ошибка при попытке получить исходный url', [
                'videoUrl' => $this->url,
                'message' => $contentUrlResponse->message,
            ]);

            return;
        }

        Log::channel('content')->info('Успешно получен исходный url', [
            'videoUrl' => $this->url,
            'contentUrl' => $contentUrlResponse->sourceUrl,
        ]);

        $content = $videoDownloader->getContent($contentUrlResponse->sourceUrl);

        $fileName = $this->type . date('Y-m-d H:i') . '.mp4';

        /** @var GoogleDriveService $googleDriveService */
        $googleDriveService = app(GoogleDriveService::class);
        $driveFile = $googleDriveService->createFile($content, $fileName);

        if (!$driveFile->getId()) {
            Log::channel('content')->error('Не найден id файла при загрузке файла на гугл диск', [
                'sourceUrl' => $this->url,
                'contentUrl' => $contentUrlResponse->sourceUrl,
            ]);
        } else {
            Log::channel('content')->info('Файл успешно загружен на гугл диск', [
                'fileId' => $driveFile->getId(),
                'sourceUrl' => $this->url,
                'contentUrl' => $contentUrlResponse->sourceUrl,
            ]);
        }

        Video::query()->create([
            'google_file_id' => $driveFile->getId(),
            'name' => $fileName,
            'url' => $this->url,
            'content_url' => $contentUrlResponse->sourceUrl,
            'type' => $this->type,
        ]);
    }
}
