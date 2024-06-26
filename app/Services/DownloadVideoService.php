<?php

namespace App\Services;

use App\Helpers\Download\ContentVideoInterface;
use App\Helpers\Utils;
use App\Models\Video;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DownloadVideoService
{
    public function download(string $type, string $url, bool $isProd, ?string $description, ?string $comment): void
    {
        Cache::put('video-status', 'processing');

        /** @var ContentVideoInterface $videoDownloader */
        $videoDownloader = Utils::getVideoContentHelper($type);

        $contentUrlResponse = $videoDownloader->getContentUrl($url);

        if (!$contentUrlResponse->success) {
            Log::channel('content')->error('Ошибка при попытке получить исходный url', [
                'videoUrl' => $url,
                'message' => $contentUrlResponse->message,
            ]);

            Cache::put('video-status', 'error');

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

            Cache::put('video-status', 'error');

            return;
        }

        $fileName = $type . date('d-m-Y_H:i:s') . '.mp4';

        /** @var GoogleDriveService $googleDriveService */
        $googleDriveService = app(GoogleDriveService::class);
        $driveFile = $googleDriveService->createFile($getContentDto->content, $fileName);

        if (!$driveFile->getId()) {
            Log::channel('content')->error('Не найден id файла при загрузке файла на гугл диск', [
                'sourceUrl' => $url,
                'contentUrl' => $contentUrlResponse->sourceUrl,
            ]);

            Cache::put('video-status', 'error');

            return;
        }

        Log::channel('content')->info('Файл успешно загружен на гугл диск', [
            'fileId' => $driveFile->getId(),
            'sourceUrl' => $url,
            'contentUrl' => $contentUrlResponse->sourceUrl,
        ]);

        $previewImageUrl = $this->getPreviewImageUrl($contentUrlResponse->sourceUrl, $contentUrlResponse->previewImgUrl);

        $lastVideosDateAndCount = Video::query()
            ->selectRaw('date(publication_date) as date, count(*) as count')
            ->where('publication_date', '>', now())
            ->where('is_prod', 1)
            ->whereNotNull('google_file_id')
            ->groupByRaw('date(publication_date)')
            ->orderByRaw('date(publication_date) desc')
            ->first()
        ;

        if ($lastVideosDateAndCount?->count > 3) {
            $nextPublicationDate = Carbon::parse($lastVideosDateAndCount->date)
                ->addDay()
                ->addHours(8)
                ->addMinutes(rand(1, 59))
            ;
        } else {
            $lastVideo = Video::query()
                ->where('is_prod', 1)
                ->whereNotNull('google_file_id')
                ->orderByDesc('publication_date')
                ->first()
            ;

            if (now()->diffInHours($lastVideo->publication_date, false) < -3) {
                $nowHours = (int)now()->format('H');

                $nextPublicationDate = $nowHours < 8
                    ? today()->addHours(8)->addMinutes(rand(1, 59))
                    : now()->addMinutes(2)
                ;
            } else {
                $nowHours = (int)$lastVideo->publication_date->addHours(3)->format('H');

                $nextPublicationDate = $nowHours < 8
                    ? today()->addHours(8)->addMinutes(rand(1, 59))
                    : $lastVideo->publication_date->addHours(3)->addMinutes(rand(1, 30))
                ;
            }
        }

        Video::query()->create([
            'google_file_id' => $driveFile->getId(),
            'name' => $fileName,
            'url' => $url,
            'content_url' => $contentUrlResponse->sourceUrl,
            'type' => $type,
            'comment' => $comment,
            'preview_image_path' => $previewImageUrl,
            'publication_date' => $nextPublicationDate,
            'is_prod' => $isProd,
            'description' => $description,
        ]);

        Cache::put('video-status', 'success');
    }

    public function compressPreviewImage(string $previewImgUrl): ?string
    {
        try {
            $image = Image::make($previewImgUrl);
            $image->encode('webp', 75);
            $fileName = date('Y-m-d H:i:s') . '.webp';
            $savePath = Storage::disk('public')->path($fileName);
            $image->save($savePath);
        } catch (Exception $exception) {
            Log::channel('content')->error('Ошибка при сжатии превью к видео', [
                'message' => $exception->getMessage(),
                'url' => $previewImgUrl,
            ]);
        }

        return $fileName ?? null;
    }

    public function getPreviewImageUrl(string $videoSourceUrl, ?string $previewImageUrl): ?string
    {
        if (!$previewImageUrl) {
            $previewImageUrl = $this->getPreviewImageFromVideo($videoSourceUrl);
        }

        return $previewImageUrl ? $this->compressPreviewImage($previewImageUrl) : null;
    }

    public function getPreviewImageFromVideo(string $videoSourceUrl): ?string
    {
        try {
            $fileName = 'preview' . time() . '.png';
            $thumbnail = Storage::disk('public')->path($fileName);
            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($videoSourceUrl);
            $frame = $video->frame(TimeCode::fromSeconds(1));
            $frame->save($thumbnail);
            return config('app.url') . '/storage/' . $fileName;
        } catch (Exception $exception) {
            Log::channel('content')->error('Ошибка при попытке получить превью из видео', [
                'message' => $exception->getMessage(),
                'sourceUrl' => $videoSourceUrl,
            ]);

            return null;
        }
    }
}
