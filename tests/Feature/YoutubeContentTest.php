<?php

namespace Tests\Feature;

use App\Helpers\Download\TikTokContentVideo;
use App\Helpers\Download\YoutubeContentVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class YoutubeContentTest extends TestCase
{
    public function testGetContentUrlFromYoutubeSuccess(): void
    {
        $youtubeContent = new YoutubeContentVideo();

        $result = $youtubeContent->getContentUrl('https://www.youtube.com/shorts/3MbOc0DFiw4?feature=share');

        $this->assertTrue($result->success);
    }
}
