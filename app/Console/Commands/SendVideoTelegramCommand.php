<?php

namespace App\Console\Commands;

use App\Jobs\SendVideoTelegramJob;
use App\Models\Video;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;

class SendVideoTelegramCommand extends Command
{
    protected $signature = 'send:video-telegram';

    protected $description = 'Отправляет видео в телеграм канал';

    public function handle(GoogleDriveService $googleDriveService)
    {
        $files = $googleDriveService->getFiles(config('services.google.mem_video_folder_id'));

        $videoIds = Video::pluck('file_id')->toArray();

        foreach ($files as $file) {
            if (!in_array($file->getId(), $videoIds)) {
                $video = $googleDriveService->downloadFile($file);
                SendVideoTelegramJob::dispatch($video);
            }
        }
    }
}
