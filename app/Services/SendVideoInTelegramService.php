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

    public function sendVideoInTelegram(Video $video): void
    {
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
}
