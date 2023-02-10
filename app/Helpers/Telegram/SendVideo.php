<?php

namespace App\Helpers\Telegram;

use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendVideo
{
    public static function execute(Video $video)
    {
        $params = [
            'chat_id' => config('services.telegram.chat_id'),
            'video' => env('APP_URL') . '/' . $video->name
        ];

        $url = "https://api.telegram.org/" . config('services.telegram.api_key') . "/sendVideo";
        $response = Http::post($url, $params);

        Log::info($response->json());
        return $response->json();
    }
}
