<?php

namespace App\Helpers\Download;

use App\DTO\GetContentDTO;
use App\DTO\GetContentUrlDTO;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;

class TikTokContentVideo implements ContentVideoInterface
{

    public function getContent(string $sourceUrl)
    {
        $client = new Client();

        try {
            $response = $client->get($sourceUrl);
            $body = $response->getBody();
        } catch (GuzzleException $e) {
            return new GetContentDTO(
                success: false,
                message: $e->getMessage(),
            );
        }

        return new GetContentDTO(
            success: true,
            content: $body->getContents(),
        );
    }

    public function getContentUrl(string $videoUrl): GetContentUrlDTO
    {
        try {
            $result = Http::asMultipart()->post('https://ssstik.io/abc?url=dl', [
                'id' => $videoUrl,
                'locale' => 'en',
                'tt' => 'bk5XWDhl'
            ]);

            $regex = '/<a[^>]*href="([^"]*)"[^>]*>Without watermark<\/a>/';

            preg_match($regex, $result->body(), $matches);

            return new GetContentUrlDTO(
                success: true,
                sourceUrl: $matches[1],
            );
        } catch (Exception $exception) {
            return new GetContentUrlDTO(
                success: false,
                message: $exception->getMessage(),
            );
        }
    }
}
