<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $memChatId;
    private string $apiUrl;

    public function __construct()
    {
        $telegramApiKey = config('services.telegram.api_key');
        $this->apiUrl = "https://api.telegram.org/" . $telegramApiKey;
        $this->memChatId = config('services.telegram.chat_id');
    }

    public function sendVideo(Video $video)
    {
        $params = [
            'chat_id' => $this->memChatId,
            'video' => config("app.url") . '/' . $video->name,
            'caption' => "[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)",
            "parse_mode" => "markdown"
        ];

        $url =  $this->apiUrl . "/sendVideo";
        $response = Http::post($url, $params);

        Log::info($response->json());
        return $response->json();
    }

    public function getChannelStats()
    {
        $proto = MTProtoSingleton::getProtoInstance();
        return $proto->getFullInfo($this->memChatId);
    }
}
