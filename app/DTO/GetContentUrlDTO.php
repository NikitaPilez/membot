<?php

declare(strict_types=1);

namespace App\DTO;

class GetContentUrlDTO
{
    public bool $success;
    public ?string $message;
    public ?string $sourceUrl;
    public ?string $previewImgUrl;

    public function __construct(bool $success, ?string $message = null, ?string $sourceUrl = null, ?string $previewImgUrl = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->sourceUrl = $sourceUrl;
        $this->previewImgUrl = $previewImgUrl;
    }
}
