<?php

namespace App\Helpers\Telegram;

class SendMessage
{
    public static function execute(string $text)
    {

        $params = [
            'chat_id' => config('services.telegram.chat_id'),
            'text' => $text
        ];

        $ch = curl_init('https://api.telegram.org/' . config('services.telegram.api_key') . '/sendMessage');
        $postRaw = json_encode($params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postRaw);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = json_decode(curl_exec($ch), true);

        return $response;
    }
}
