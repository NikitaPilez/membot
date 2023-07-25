<?php

namespace App\Helpers\Download;

use App\DTO\GetContentUrlDTO;

interface ContentVideoInterface
{
    public function getContent(string $sourceUrl);

    public function getContentUrl(string $videoUrl): GetContentUrlDTO;
}
