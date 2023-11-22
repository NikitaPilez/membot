<?php

namespace App\Services;

use App\DTO\ChannelPostTGStatDTO;
use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\ChannelPostStat;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Dom\Node;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateChannelPostStatService
{
    public function run(): void
    {
        $channels = Channel::query()
            ->where('is_active', 1)
            ->get();

        foreach ($channels as $channel) {
            $channelPostStats = $this->getChannelStat($channel);

            $channelPosts = ChannelPost::query()->where('channel_id', $channel->id)->get()->keyBy('post_id');
            foreach ($channelPostStats as $post) {
                if ($channelPost = $channelPosts->get($post->id)) {
                    $this->updateViewsStat($channelPost, $post);
                }

            }
        }
    }

    public function getChannelStat(Channel $channel): array
    {
        $channelAlias = $channel->getChannelAlias();

        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser([
            'customFlags' => ['--disable-blink-features', '--disable-blink-features=AutomationControlled'],
            'sendSyncDefaultTimeout' => 10000,
        ]);

        $page = $browser->createPage();
        $page->navigate('https://tgstat.ru/ru/channel/@' . $channelAlias)->waitForNavigation();
        $dom = $page->dom();
        $elements = $dom->querySelectorAll('[id^="post-"]');

        if (!$elements) {
            Log::channel('content')->error('Не найдены посты.', [
                'alias' => $channelAlias,
            ]);
        }

        /** @var ChannelPostTGStatDTO[] $channelPostsStat */
        $channelPostsStat = [];

        /** @var Node $element */
        foreach ($elements as $element) {
            $shares = $element->querySelector('[data-original-title="Пересылок всего"]')?->getText();
            $id = $element->querySelector('[data-original-title="Количество просмотров публикации"]')?->getAttribute('href');
            $views = $element->querySelector('[data-original-title="Количество просмотров публикации"]')?->getText();
            preg_match('/\/(\d+)\/stat/', $id, $matches);
            $id = (int) $matches[1];

            if ($views && $shares && $id) {
                $channelPostsStat[] = new ChannelPostTGStatDTO(
                    id: $id,
                    views: $this->getHumanViews($views),
                    shares: $shares,
                );
            } else {
                Log::channel('content')->error('Не достаточно информации о посте.', [
                    'alias' => $channelAlias,
                    'id' => $id,
                    'views' => $views,
                    'shares' => $shares,
                ]);
            }
        }

        $browser->close();

        return $channelPostsStat;
    }

    public function getHumanViews(string $views): float|int
    {
        $position = strpos($views, 'k');

        if ($position !== false) {
            return (float) str_replace('k', '', $views) * 1000;
        } else {
            return (int) $views;
        }
    }

    public function updateViewsStat(ChannelPost $channelPost, ChannelPostTGStatDTO $channelPostTGStatDTO): void
    {
        $channelPostStat = $channelPost->stat;

        if (!$channelPost->stat) {
            $channelPostStat = new ChannelPostStat();
            $channelPostStat->channel_post_id = $channelPost->id;
        }

        $channelPostStat->views = $channelPostTGStatDTO->views;
        $channelPostStat->shares = $channelPostTGStatDTO->shares;

        $channelPostStat->save();
    }
}
