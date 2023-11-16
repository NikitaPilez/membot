<?php

namespace App\Helpers\Download;

use App\DTO\GetContentUrlDTO;

class SimpleContentVideo implements ContentVideoInterface
{

    public function getContent(string $sourceUrl)
    {
        return file_get_contents($sourceUrl);
    }

    public function getContentUrl(string $videoUrl): GetContentUrlDTO
    {
        return new GetContentUrlDTO(
            success: true,
            sourceUrl: $videoUrl,
        );
    }
}
