<?php

namespace App\DTO;

class GetContentUrlDTO
{
    public bool $success;
    public ?string $message;
    public ?string $sourceUrl;

    public function __construct(bool $success, ?string $message = null, ?string $sourceUrl = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->sourceUrl = $sourceUrl;
    }
}
