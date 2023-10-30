<?php

declare(strict_types=1);

namespace App\DTO;

class GetContentDTO
{
    public bool $success;
    public ?string $message;
    public ?string $content;

    public function __construct(bool $success, ?string $message = null, ?string $content = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->content = $content;
    }
}
