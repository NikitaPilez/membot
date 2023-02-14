<?php

namespace App\Console\Commands\Temp;

use App\Services\InstagramDownloadService;
use App\Services\YoutubeDownloadService;
use Illuminate\Console\Command;

class TempTestYoutubeShortsDownload extends Command
{
    protected $signature = 'temp:test-shorts {--url=https://www.youtube.com/shorts/rrAlbHno10g}';

    protected $description = 'Загружает видео из youtube shorts на гугл диск';

    public function handle(YoutubeDownloadService $youtubeDownloadService)
    {
        $youtubeDownloadService->shortsDownload($this->option('url'));

        $this->output->success('Shorts downloaded');
    }
}
