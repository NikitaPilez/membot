<?php

namespace App\Console\Commands\Temp;

use App\Services\TiktokDownloadService;
use Illuminate\Console\Command;

class TempTestTiktokDownloadCommand extends Command
{
    protected $signature = 'temp:test-tiktok {--url=https://www.tiktok.com/@witchfeelings222/video/7199742166451621122}';

    protected $description = 'Загружает видео из тиктока на гугл диск';

    public function handle(TiktokDownloadService $tiktokDownloadService)
    {
        $tiktokDownloadService->download($this->option('url'));

        $this->output->success('Tiktok downloaded');
    }
}
