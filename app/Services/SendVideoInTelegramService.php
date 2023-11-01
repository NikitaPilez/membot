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

    public function sendVideoInTelegram(?Video $video): void
    {
        $video = $this->getVideoForSend($video);

        if (!$video) {
            Log::channel('content')->info('В данный момент нет видео для отправки.');

            return;
        }

        try {
            $fileFromDrive = $this->googleDriveService->getFileById($video->google_file_id);

            $this->googleDriveService->downloadFile($fileFromDrive);

            SendVideoTelegramJob::dispatch($video);
        } catch (Exception $exception) {
            Log::channel('content')->info('Ошибка при получении информации о файле.', [
                'message' => $exception->getMessage(),
                'videoId' => $video->id,
            ]);
        }
    }

    public function getVideoForSend(?Video $video): ?Video
    {
        if (!$video) {
            return Video::where('google_file_id', '!=', null)->where('is_sent', 0)->first();
        }

        return $video;
    }
}
