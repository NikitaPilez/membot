<?php

namespace App\Jobs;

use App\Helpers\Telegram\SendVideo;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class SendVideoTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle()
    {
        $response = SendVideo::execute($this->video);

        if ($response["ok"]) {
            $this->video->is_sent = 1;
            $this->video->save();

            File::delete(public_path($this->video->name));
        }
    }
}
