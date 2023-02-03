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

        $googleDriveService->downloadFiles($files);

        $video = Video::query()->where('is_sent', 0)->inRandomOrder()->first();

        if($video) {
            SendVideoTelegramJob::dispatch($video);
        }
    }
}
