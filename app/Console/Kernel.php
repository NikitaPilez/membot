<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\CalculateAveragePostStatsCommand;
use App\Console\Commands\CheckPotentialVideosInTelegram;
use App\Console\Commands\CheckPotentialVideosInYoutube;
use App\Console\Commands\SendVideoInTelegramCommand;
use App\Console\Commands\UpdateChannelPostStatCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SendVideoInTelegramCommand::class)->everyMinute();
        $schedule->command(CheckPotentialVideosInTelegram::class)->everyFifteenMinutes();
        $schedule->command(CheckPotentialVideosInYoutube::class)->hourly()->when(function () {
            return now()->hour >= 7 && now()->hour <= 19;
        });
//        $schedule->command(UpdateChannelPostStatCommand::class)->everyMinute();
//        $schedule->command(CalculateAveragePostStatsCommand::class)->hourly();

        $schedule->command('telescope:prune --hours=48')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
