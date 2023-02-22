<?php

namespace App\Helpers\Download;

interface ContentVideoInterface
{
    public function getContent(string $sourceUrl);

    public function getContentUrl(string $videoUrl): string;
}
