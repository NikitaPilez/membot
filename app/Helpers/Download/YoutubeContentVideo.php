<?php

namespace App\Helpers\Download;

use App\DTO\GetContentUrlDTO;
use Exception;

class YoutubeContentVideo implements ContentVideoInterface
{

    public function getContentUrl(string $videoUrl): GetContentUrlDTO
    {
        try {
            preg_match('/"(?=https:\/\/rr).{1,444}(?=mp4).*?"/', file_get_contents($videoUrl), $matches);
            $sourceUrl = str_replace('\u0026', '&', str_replace('"', '', $matches[0]));
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

    public function getContent(string $sourceUrl)
    {
        return file_get_contents($sourceUrl);
    }
}
