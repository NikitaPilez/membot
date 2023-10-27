<?php

namespace App\Helpers\Download;

use App\DTO\GetContentUrlDTO;
use Exception;
use Illuminate\Support\Facades\Http;

class InstagramContentVideo implements ContentVideoInterface
{

    public function getContent(string $sourceUrl)
    {
        return file_get_contents($sourceUrl);
    }

    public function getContentUrl(string $videoUrl): GetContentUrlDTO
    {
        try {
            $response = Http::get('sssinstagram.com');
            $cookies = $response->cookies();

            foreach ($cookies as $cookie) {
                if ($cookie->getName() == 'sssinstagram_session') {
                    $headers['Cookie'] = 'sssinstagram_session=' . $cookie->getValue();
                }

                if ($cookie->getName() == 'XSRF-TOKEN') {
                    $headers['X-Xsrf-Token'] = urldecode($cookie->getValue());
                }
            }

            $result = Http::withHeaders($headers)->post('https://sssinstagram.com/r', [
                'link' => $videoUrl,
                'token' => '',
            ]);

            $sourceUrl = $result['data']['items'][0]['urls'][0]['urlDownloadable'];
        } catch (Exception $exception) {
            return new GetContentUrlDTO(
                success: false,
                message: $exception->getMessage(),
            );
        }

        return new GetContentUrlDTO(
            success: true,
            sourceUrl: $sourceUrl,
        );
    }
}
