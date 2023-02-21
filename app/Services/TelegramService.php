<?php

namespace App\Services;

use App\Models\Video;
use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $memChatId;
    private string $apiUrl;
    private API $proto;

    public function __construct()
    {
        $telegramApiKey = config('services.telegram.api_key');
        $this->apiUrl = "https://api.telegram.org/" . $telegramApiKey;
        $this->memChatId = config('services.telegram.chat_id');
        $this->proto = new API("/tmp");
        $this->proto->start();
    }

    public function sendVideo(Video $video)
    {
        $params = [
            'chat_id' => $this->memChatId,
            'video' => env('APP_URL') . '/' . $video->name,
            'caption' => "[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)",
            "parse_mode" => "markdown"
        ];

        $url =  $this->apiUrl . "/sendVideo";
        $response = Http::post($url, $params);

        Log::info($response->json());
        return $response->json();
    }

    public function sendErrorLog()
    {
        $url =  $this->apiUrl . "/sendDocument";
        $managerIds = explode(",", config('services.telegram.manager_ids'));
        foreach ($managerIds as $managerId) {
            $response = Http::attach('document', fopen(storage_path("logs/laravel.log"), 'r'))->post($url, [
                'chat_id' => $managerId,
            ]);

            Log::info($response->json());
        }
    }

    public function getChannelStats()
    {
        return $this->proto->getFullInfo($this->memChatId);
    }
}
