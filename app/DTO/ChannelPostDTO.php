<?php

namespace App\DTO;

class ChannelPostDTO
{
    public function __construct(public int $id, public string $createdAt, public ?string $description = null, public ?string $image = null)
    {
    }
}
