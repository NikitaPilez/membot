<?php

namespace App\Services;

use App\DTO\ChannelPostDTO;
use App\Helpers\CheckPotentialVideo\CheckPotentialVideoFromTelegram;
use App\Helpers\CheckPotentialVideo\CheckPotentialVideoFromYoutube;
use App\Helpers\CheckPotentialVideo\CheckPotentialVideoInterface;
use App\Models\Channel;
use App\Models\ChannelPost;
use Illuminate\Support\Facades\Log;

class CheckPotentialVideoService
{
    public function run(string $socialNetwork): void
    {
        $channels =  Channel::query()
            ->where('is_active', 1)
            ->where('type', $socialNetwork)
            ->get();

        foreach ($channels as $channel) {
            $checkPotentialVideoHandler = $this->getPotentialVideoHelper($channel);

            if ($checkPotentialVideoHandler) {
                /** @var ChannelPostDTO[] $parsedChannelPosts */
                $parsedChannelPosts = $checkPotentialVideoHandler->getChannelPosts($channel);
                $this->createNewChannelPosts($channel, $parsedChannelPosts);
            }
        }
    }

    public function getPotentialVideoHelper(Channel $channel): ?CheckPotentialVideoInterface
    {
        if ($channel->type === 'telegram') {
            return new CheckPotentialVideoFromTelegram();
        } else if ($channel->type === 'youtube') {
            return new CheckPotentialVideoFromYoutube();
        }

        return null;
    }

    public function createNewChannelPosts(Channel $channel, array $parsedChannelPosts): void
    {
        $existsPostIds =  ChannelPost::query()->where('channel_id', $channel->id)->pluck('post_id')->toArray();

        /** @var ChannelPostDTO $parsedChannelPost */
        foreach ($parsedChannelPosts as $parsedChannelPost) {
            if (!in_array($parsedChannelPost->id, $existsPostIds)) {
                /** @var ChannelPost $channelPost */
                $channelPost = ChannelPost::query()->create([
                    'channel_id' => $channel->id,
                    'post_id' => $parsedChannelPost->id,
                    'description' => $parsedChannelPost->description,
                    'image' => $parsedChannelPost->image,
                    'publication_at' => $parsedChannelPost->createdAt,
                ]);

                Log::channel('content')->info('Новый пост с канала ' . $channel->name, [
                    'post_id' => $channelPost->id,
                ]);
            }
        }
    }
}
