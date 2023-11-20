<?php

namespace App\Services;

use App\DTO\ChannelPostDTO;
use App\Models\Channel;
use App\Models\ChannelPost;
use Exception;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Dom\Node;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckPotentialVideoService
{
    /**
     * @throws Exception
     */
    public function run(): void
    {
        $channels =  Channel::query()->where('is_active', 1)->get();

        foreach ($channels as $channel) {
            $this->check($channel);
        }
    }

    /**
     * @throws Exception
     */
    public function check(Channel $channel): void
    {
        if ($channel->type === 'telegram') {
            $channelPosts = $this->getChannelPosts($channel);

            $postIds = ChannelPost::query()->where('channel_id', $channel->id)->pluck('post_id')->toArray();

            foreach ($channelPosts as $channelPost) {
                if (!in_array($channelPost->id, $postIds)) {
                    ChannelPost::query()->create([
                        'channel_id' => $channel->id,
                        'post_id' => $channelPost->id,
                        'description' => $channelPost->description,
                        'publication_at' => $channelPost->createdAt,
                    ]);
                }
            }
        }
    }

    /**
     * @throws Exception
     *
     * @return array<ChannelPostDTO>
     */
    public function getChannelPosts(Channel $channel): array
    {
        $channelAlias = $this->getChannelAlias($channel->url);

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
            $views = $element->querySelector('.tgme_widget_message_views')?->getText();
            $id = $element->querySelector('.tgme_widget_message')?->getAttribute('data-post');
            $createdAt = $element->querySelector('.tgme_widget_message_meta time')?->getAttribute('datetime');
            $description = $element->querySelector('.tgme_widget_message_text')?->getText();

            if ($views && $id && $createdAt) {
                $channelPosts[] = new ChannelPostDTO(
                    id: Str::replace($channelAlias . '/', '', $id),
                    createdAt: $createdAt,
                    views: $this->getHumanViews($views),
                    description: $description,
                );
            } else {
                Log::channel('content')->error('Не достаточно информации о посте.', [
                    'alias' => $channelAlias,
                    'id' => $id,
                    'views' => $views,
                    'created_at' => $createdAt,
                    'description' => $description,
                ]);
            }
        }

        $browser->close();

        return $channelPosts;
    }

    public function getHumanViews(string $views): float|int
    {
        $position = strpos($views, 'K');

        if ($position !== false) {
            return (float) str_replace('K', '', $views) * 1000;
        } else {
            return (int) $views;
        }
    }

    /**
     * @throws Exception
     */
    public function getChannelAlias(string $channelUrl): string
    {
        $splitBySlashArray = explode('/', $channelUrl);

        if (!$splitBySlashArray) {
            throw new Exception('Ошибка при получении alias у канала ' . $channelUrl);
        }

        return end($splitBySlashArray);
    }
}
