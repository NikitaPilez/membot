<?php

namespace App\Helpers\Telegram;

use App\Models\Video;
use Illuminate\Support\Facades\Log;

class SendVideo
{
    public static function execute(Video $video)
    {
        $params = [
            'chat_id' => config('services.telegram.chat_id'),
            'video' => env('APP_URL') . '/' . $video->name
        ];

        $ch = curl_init('https://api.telegram.org/' . config('services.telegram.api_key') . '/sendVideo');
        $postRaw = json_encode($params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postRaw);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = json_decode(curl_exec($ch), true);
        Log::info($response);

        if ($response['ok'] == 'true') {
            $video->is_sent = 1;
            $video->save();
        }

        return $response;
    }
}
