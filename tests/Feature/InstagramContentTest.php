<?php

namespace Tests\Feature;

use App\Helpers\Download\InstagramContentVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InstagramContentTest extends TestCase
{
    /**
     * @dataProvider instagramUrlDataProvider
     */
    public function testGetContentUrlFromInstagramSuccess(string $url): void
    {
        $instagramContent = new InstagramContentVideo();

        $result = $instagramContent->getContentUrl($url);

        $this->assertTrue($result->success);
    }

    public static function instagramUrlDataProvider(): array
    {
        return [
            ['https://www.instagram.com/p/CopX1mhp99P/'],
            ['https://www.instagram.com/reel/CylXu9LrfD8/'],
            ['https://www.instagram.com/reel/CvARcizqWl_/'],
            ['https://www.instagram.com/reel/Cybdt-8IxB9/'],
        ];
    }
}
