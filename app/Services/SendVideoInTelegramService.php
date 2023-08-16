<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendVideoTelegramJob;
use App\Models\Video;
use Exception;
use Illuminate\Support\Facades\Log;

class SendVideoInTelegramService
{
    private GoogleDriveService $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function sendVideoInTelegram(): void
    {
        $videosIds = Video::query()
            ->where('google_file_id', '!=', null)
            ->where('is_sent', 0)
            ->pluck('google_file_id')->toArray();

        if (!$videosIds) {
            Log::channel('content')->info('В данный момент нет видео для отправки.');

            return;
        }

        foreach ($videosIds as $videoId) {
            try {
                $fileFromDrive = $this->googleDriveService->getFileById($videoId);

                Log::channel('content')->info('Найдено видео для отправки.', [
                    'fileId' => $videoId,
                ]);

                $video = Video::query()->where('google_file_id', $videoId)->first();

                $this->googleDriveService->downloadFile($fileFromDrive);

                SendVideoTelegramJob::dispatch($video);

                return;
            } catch (Exception $exception) {
                Log::channel('content')->info('Ошибка при получении информации о файле.', [
                    'message' => $exception->getMessage(),
                    'fileId' => $videoId,
                ]);
            }
        }
    }
}
