<?php

namespace App\Services;

use App\DTO\ChannelPostTGStatDTO;
use App\Helpers\TGStat;
use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\ChannelPostStat;
use Carbon\Carbon;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Dom\Node;
use Illuminate\Support\Facades\Log;

class UpdateChannelPostStatService
{
    public function run(): void
    {
        $channels = Channel::query()->where('is_active', 1)->get();
        $hourAgo = now()->subHour();
        $dayAgo = now()->subDay();

        foreach ($channels as $channel) {
            $channelPostStats = $this->getChannelStat($channel);

            $channelPosts = ChannelPost::query()->where('channel_id', $channel->id)->get()->keyBy('post_id');
            foreach ($channelPostStats as $post) {
                /** @var ChannelPost $channelPost */
                $channelPost = $channelPosts->get($post->id);

                if (!$channelPost || $channelPost->created_at > $dayAgo) {
                    continue;
                }

                if ($this->isNeedUpdateStat($channelPost, $hourAgo)) {
                    $this->updateViewsStat($channelPost, $post);
                }
            }
        }
    }

    public function isNeedUpdateStat(ChannelPost $channelPost, Carbon $hourAgo): bool
    {
        $isStatLessHourAgoDoesntExist = $channelPost->stats()->where('created_at', '>', $hourAgo)->doesntExist();
        return $isStatLessHourAgoDoesntExist || $channelPost->stats()->doesntExist();
    }

    /**
     * @return array<ChannelPostTGStatDTO>
     */
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
                    views: TGStat::getHumanViews($views),
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

    public function updateViewsStat(ChannelPost $channelPost, ChannelPostTGStatDTO $channelPostTGStatDTO): void
    {
        ChannelPostStat::query()->create([
            'channel_post_id' => $channelPost->id,
            'views' => $channelPostTGStatDTO->views,
            'shares' => $channelPostTGStatDTO->shares,
        ]);
    }
}
