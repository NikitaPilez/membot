<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Stat;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class GetChannelStatsCommand extends Command
{
    protected $signature = 'channel:stats';

    public function handle(TelegramService $telegramService): void
    {
        $stats = $telegramService->getChannelStats();

        Stat::query()->create([
            'date' => date('d.m.Y'),
            'participants_count' => $stats['full']['participants_count']
        ]);
    }
}
