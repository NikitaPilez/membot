<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SendVideoTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(TelegramService $telegramService): void
    {
        $result = $telegramService->sendVideo($this->video);

        if ($result === true) {
            $this->video->is_sent = 1;
            $this->video->sent_at = Carbon::now();
            $this->video->save();

            File::delete(public_path($this->video->name));
        }
    }
}
