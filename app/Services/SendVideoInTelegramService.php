<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\FFMpegHelper;
use App\Models\Video;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendVideoInTelegramService
{
    private GoogleDriveService $googleDriveService;
    private TelegramService $telegramService;

    public function __construct(GoogleDriveService $googleDriveService, TelegramService $telegramService)
    {
        $this->googleDriveService = $googleDriveService;
        $this->telegramService = $telegramService;
    }

    public function run(): void
    {
        $video = Video::where('google_file_id', '!=', null)
            ->where('is_sent', 0)
            ->where('publication_date', '<', now())
            ->orderBy('publication_date')
            ->first();

        if ($video) {
            Log::channel('content')->info('Найдено видео для отправки', [
                'video' => $video->id,
                'link' => $video->url,
            ]);

            $this->sendVideoInTelegram($video);
        }
    }

    public function sendVideoInTelegram(Video $video): void
    {
        try {
            $fileFromDrive = $this->googleDriveService->getFileById($video->google_file_id);

            $fileName = $this->googleDriveService->downloadFile($fileFromDrive);

//            if ($this->checkIsNeedCompressVideo($fileName)) {
//                FFMpegHelper::compressVideo($fileName);
//            }

            $result = $this->telegramService->sendVideo($video);

            if ($result === true) {
                $video->is_sent = 1;
                $video->sent_at = Carbon::now();
                $video->publication_date = Carbon::now();
                $video->save();

//                Storage::disk('public')->delete($video->name);
            }
        } catch (Exception $exception) {
            Log::channel('content')->info('Ошибка при получении информации о файле.', [
                'message' => $exception->getMessage(),
                'videoId' => $video->id,
            ]);
        }
    }

    public function checkIsNeedCompressVideo(string $fileName): bool
    {
        $contentSize = Storage::disk('public')->size($fileName);

        $contentSizeMB = $contentSize / (1024 * 1024);

        return $contentSizeMB > 20;
    }
}
