<?php

namespace App\Helpers\Download;

use App\DTO\GetContentDTO;
use App\DTO\GetContentUrlDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SimpleContentVideo implements ContentVideoInterface
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
        return new GetContentUrlDTO(
            success: true,
            sourceUrl: $videoUrl,
        );
    }
}
