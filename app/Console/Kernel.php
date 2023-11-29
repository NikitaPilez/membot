<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\CalculateAveragePostStatsCommand;
use App\Console\Commands\CheckPotentialVideos;
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
        $schedule->command(CheckPotentialVideos::class)->everyFiveMinutes();
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
