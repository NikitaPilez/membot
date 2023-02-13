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
        $settings = new Settings();
        $appInfo = new AppInfo();
        $appInfo->setApiHash(config('services.telegram.api_hash'));
        $appInfo->setApiId(config('services.telegram.api_id'));
        $settings->setAppInfo($appInfo);
        $this->proto = new API("session.madeline", $settings);
        $this->proto->botLogin(substr($telegramApiKey, 3));
        $this->proto->start();
    }

    public function sendVideo(Video $video)
    {
        $params = [
            'chat_id' => $this->memChatId,
            'video' => env('APP_URL') . '/' . $video->name
        ];

        $url =  $this->apiUrl . "/sendVideo";
        $response = Http::post($url, $params);

        Log::info($response->json());
        return $response->json();
    }

    public function getChannelStats()
    {
        return $this->proto->getFullInfo($this->memChatId);
    }
}
