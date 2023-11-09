<?php

namespace App\Services;

use App\Helpers\Download\ContentVideoInterface;
use App\Helpers\Utils;
use App\Models\Video;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class DownloadVideoService
{
    public function download(string $type, string $url, bool $isProd, ?string $description, ?string $comment): void
    {
        /** @var ContentVideoInterface $videoDownloader */
        $videoDownloader = Utils::getVideoContentHelper($type);

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

        if ($contentUrlResponse->previewImgUrl) {
            $previewImagePath = $this->downloadPreviewImage($contentUrlResponse->previewImgUrl);
        }

        $lastVideo = Video::where('is_sent', 0)->where('google_file_id', '!=', null)->where('publication_date', '>', now())->orderByDesc('publication_date')->first();

        Video::query()->create([
            'google_file_id' => $driveFile->getId(),
            'name' => $fileName,
            'url' => $url,
            'content_url' => $contentUrlResponse->sourceUrl,
            'type' => $type,
            'comment' => $comment,
            'preview_image_path' => $previewImagePath ?? null,
            'publication_date' => $lastVideo ? Carbon::parse($lastVideo->publication_date)->addHours(2) : now()->addHour()->addMinutes(rand(50, 80)),
            'is_prod' => $isProd,
            'description' => $description,
        ]);
    }

    public function downloadPreviewImage(string $previewImgUrl): ?string
    {
        try {
            $image = Image::make($previewImgUrl);
            $image->encode('webp', 75);
            $fileName =  date('Y-m-d H:i') . '.webp';
            $savePath = storage_path('app/public/' . $fileName);
            $image->save($savePath);
        } catch (Exception $exception) {
            Log::channel('content')->error('Ошибка при получении превью к видео', [
                'message' => $exception->getMessage(),
                'url' => $previewImgUrl,
            ]);
        }

        return $fileName ?? null;
    }
}
