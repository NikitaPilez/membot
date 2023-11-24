<?php

namespace App\Services;

use App\DTO\ChannelPostDTO;
use App\Models\Channel;
use App\Models\ChannelPost;
use Exception;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Dom\Node;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckPotentialVideoService
{
    public function run(): void
    {
        $channels =  Channel::query()->where('is_active', 1)->get();

        foreach ($channels as $channel) {
            $this->check($channel);
        }
    }

    public function check(Channel $channel): void
    {
        if ($channel->type === 'telegram') {
            $parsedChannelPosts = $this->getChannelPosts($channel);
            $this->createNewChannelPosts($channel, $parsedChannelPosts);
        }
    }

    public function createNewChannelPosts(Channel $channel, array $parsedChannelPosts): void
    {
        $existsPostIds = Cache::remember('exists-post-ids-' . $channel->id, 60, function () use ($channel) {
            return ChannelPost::query()->where('channel_id', $channel->id)->pluck('post_id')->toArray();
        });

        /** @var ChannelPostDTO $parsedChannelPost */
        foreach ($parsedChannelPosts as $parsedChannelPost) {
            if (!in_array($parsedChannelPost->id, $existsPostIds)) {
                /** @var ChannelPost $channelPost */
                $channelPost = ChannelPost::query()->create([
                    'channel_id' => $channel->id,
                    'post_id' => $parsedChannelPost->id,
                    'description' => $parsedChannelPost->description,
                    'publication_at' => $parsedChannelPost->createdAt,
                ]);

                Log::channel('content')->info('Новый пост с канала ' . $channel->name, [
                    'post_id' => $channelPost->id,
                ]);
            }
        }
    }

    /**
     * @return array<ChannelPostDTO>
     */
    public function getChannelPosts(Channel $channel): array
    {
        $channelAlias = $channel->getChannelAlias();

        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser([
            'customFlags' => ['--disable-blink-features', '--disable-blink-features=AutomationControlled'],
            'sendSyncDefaultTimeout' => 10000,
        ]);

        $page = $browser->createPage();
        $page->navigate('https://t.me/s/' . $channelAlias)->waitForNavigation();
        $dom = $page->dom();
        $elements = $dom->querySelectorAll('.tgme_widget_message_wrap');

        if (!$elements) {
            Log::channel('content')->error('Не найдены посты.', [
                'alias' => $channelAlias,
            ]);
        }

        /** @var ChannelPostDTO[] $channelPosts */
        $channelPosts = [];

        /** @var Node $element */
        foreach ($elements as $element) {
            $id = $element->querySelector('.tgme_widget_message')?->getAttribute('data-post');
            $createdAt = $element->querySelector('.tgme_widget_message_meta time')?->getAttribute('datetime');
            $description = $element->querySelector('.tgme_widget_message_text')?->getText();

            if ($id && $createdAt) {
                $channelPosts[] = new ChannelPostDTO(
                    id: Str::replace($channelAlias . '/', '', $id),
                    createdAt: $createdAt,
                    description: $description,
                );
            } else {
                Log::channel('content')->error('Не достаточно информации о посте.', [
                    'alias' => $channelAlias,
                    'id' => $id,
                    'created_at' => $createdAt,
                    'description' => $description,
                ]);
            }
        }

        $browser->close();

        return $channelPosts;
    }
}
