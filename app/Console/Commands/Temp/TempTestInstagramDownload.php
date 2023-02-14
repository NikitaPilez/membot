<?php

namespace App\Console\Commands\Temp;

use App\Services\InstagramDownloadService;
use Illuminate\Console\Command;

class TempTestInstagramDownload extends Command
{
    protected $signature = 'temp:test-inst {--url=https://www.instagram.com/p/CopX1mhp99P/}';

    protected $description = 'Загружает видео из inst на гугл диск';

    public function handle(InstagramDownloadService $instagramDownloadService)
    {
        $instagramDownloadService->download($this->option('url'));

        $this->output->success('Instagram downloaded');
    }
}
