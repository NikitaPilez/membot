<?php

namespace App\Services;

use App\Helpers\Download\InstagramContentVideo;
use App\Helpers\Download\SimpleConverter;
use App\Helpers\Download\TikTokContentVideo;
use App\Helpers\Download\YoutubeContentVideo;
use App\Models\Video;
use Illuminate\Support\Facades\Log;

class DownloadVideoService
{
    public function download(string $type, string $url): void
    {
        $videoDownloader = match ($type) {
            'tiktok' => new TikTokContentVideo(),
            'youtube' => new YoutubeContentVideo(),
            'instagram' => new InstagramContentVideo(),
            default => new SimpleConverter(),
        };

        $contentUrlResponse = $videoDownloader->getContentUrl($url);

        if (!$contentUrlResponse->success) {
            Log::channel('content')->error('Ошибка при попытке получить исходный url', [
                'videoUrl' => $url,
                'message' => $contentUrlResponse->message,
            ]);

            return;
        }

        Log::channel('content')->info('Успешно получен исходный url', [
            'videoUrl' => $url,
            'contentUrl' => $contentUrlResponse->sourceUrl,
        ]);

        $getContentDto = $videoDownloader->getContent($contentUrlResponse->sourceUrl);

        if (!$getContentDto->success) {
            Log::channel()->error('Ошибка при получении контента по урлу', [
                'message' => $getContentDto->message,
                'url' => $contentUrlResponse->sourceUrl,
            ]);

            return;
        }

        $fileName = $type . date('Y-m-d H:i') . '.mp4';

        /** @var GoogleDriveService $googleDriveService */
        $googleDriveService = app(GoogleDriveService::class);
        $driveFile = $googleDriveService->createFile($getContentDto->content, $fileName);

        if (!$driveFile->getId()) {
            Log::channel('content')->error('Не найден id файла при загрузке файла на гугл диск', [
                'sourceUrl' => $url,
                'contentUrl' => $contentUrlResponse->sourceUrl,
            ]);

            return;
        }

        Log::channel('content')->info('Файл успешно загружен на гугл диск', [
            'fileId' => $driveFile->getId(),
            'sourceUrl' => $url,
            'contentUrl' => $contentUrlResponse->sourceUrl,
        ]);

        Video::query()->create([
            'google_file_id' => $driveFile->getId(),
            'name' => $fileName,
            'url' => $url,
            'content_url' => $contentUrlResponse->sourceUrl,
            'type' => $type,
        ]);
    }
}
