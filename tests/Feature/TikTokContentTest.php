<?php

namespace Tests\Feature;

use App\Helpers\Download\TikTokContentVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TikTokContentTest extends TestCase
{
    public function testGetContentUrlFromTikTokSuccess(): void
    {
        $tiktokContent = new TikTokContentVideo();

        $result = $tiktokContent->getContentUrl('https://vm.tiktok.com/ZMjJusH2n/');

        $this->assertTrue($result->success);
    }
}
