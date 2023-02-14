<?php

namespace App\Console\Commands;

use App\Models\Stat;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class GetChannelStatsCommand extends Command
{
    protected $signature = "channel:stats";

    public function handle(TelegramService $telegramService)
    {
        $stats = $telegramService->getChannelStats();

        Stat::create([
            "date" => date("d.m.Y"),
            "participants_count" => $stats["full"]["participants_count"]
        ]);
    }
}
