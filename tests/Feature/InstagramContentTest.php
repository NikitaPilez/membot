<?php

namespace Tests\Feature;

use App\Helpers\Download\InstagramContentVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InstagramContentTest extends TestCase
{
    public function testGetContentUrlFromInstagramSuccess(): void
    {
        $instagramContent = new InstagramContentVideo();

        $result = $instagramContent->getContentUrl('https://www.instagram.com/p/CopX1mhp99P/');

        $this->assertTrue($result->success);
    }
}
