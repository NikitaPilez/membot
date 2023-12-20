<?php

namespace App\Helpers\CheckPotentialVideo;

use App\DTO\ChannelPostDTO;
use App\Models\Channel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckPotentialVideoFromYoutube implements CheckPotentialVideoInterface
{

    public function getChannelPosts(Channel $channel): array
    {
        if (!$channel->youtube_id) {
            return [];
        }

        $channelPosts = [];

        $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
            'key' => config('services.youtube.api_key'),
            'videoDuration' => 'short',
            'type' => 'video',
            'order' => 'date',
            'maxResults' => 10,
            'channelId' => $channel->youtube_id,
            'part' => 'snippet',
        ]);

        if ($response->successful()) {
            $channelPosts = $this->transformResponseToPost($response->json());
        } else {
            Log::channel('content')->error('Не удалось получить контент по каналу.', [
                'channel_id' => $channel->id,
            ]);
        }

        return $channelPosts;
    }

    public function transformResponseToPost(array $response): array
    {
        $channelPosts = [];
        if ($items = $response['items'] ?? null) {
            foreach ($items as $item) {
                $description = $item['snippet']['title'] ?? null;
                $stringId = $item['id']['videoId'] ?? null;
                $createdAt = $item['snippet']['publishedAt'] ?? null;
                $image = $item['snippet']['thumbnails']['high']['url'] ?? null;
                $videoId = $item['id']['videoId'] ?? null;

                $hash = hash_hmac('sha256', $stringId, '1');
                $id = (int) fmod(hexdec($hash), 10000) + 1;

                $channelPosts[] = new ChannelPostDTO(
                    id: $id,
                    createdAt: Carbon::parse($createdAt),
                    description: $description,
                    image: $image,
                    link: 'https://www.youtube.com/shorts/' . $videoId,
                );
            }
        }

        return $channelPosts;
    }
}
