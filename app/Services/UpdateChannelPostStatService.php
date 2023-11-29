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
        $channels = Channel::query()
            ->where('is_active', 1)
            ->whereNotNull('tgstat_link')
            ->get();

        $hourAgo = now()->subHour();
        $oneDayOneHourAgo = now()->subHours(25);

        foreach ($channels as $channel) {
            $channelPostStats = $this->getChannelStat($channel);

            $channelPosts = ChannelPost::query()
                ->where('channel_id', $channel->id)
                ->where('publication_at', '>', $oneDayOneHourAgo)
                ->get()
                ->keyBy('post_id');

            foreach ($channelPostStats as $post) {
                /** @var ChannelPost $channelPost */
                $channelPost = $channelPosts->get($post->id);

                if (!$channelPost) {
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
        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser([
            'customFlags' => ['--disable-blink-features', '--disable-blink-features=AutomationControlled'],
            'sendSyncDefaultTimeout' => 10000,
        ]);

        $page = $browser->createPage();
        $page->navigate($channel->tgstat_link)->waitForNavigation();
        $dom = $page->dom();
        $elements = $dom->querySelectorAll('[id^="post-"]');

        if (!$elements) {
            Log::channel('content')->error('Не найдены посты для статистики.', [
                'channel_id' => $channel->id,
            ]);
        }

        /** @var ChannelPostTGStatDTO[] $channelPostsStat */
        $channelPostsStat = [];

        /** @var Node $element */
        foreach ($elements as $element) {
            $channelPostTGStatDTO = TGStat::getChannelPostFromNode($element);

            if ($channelPostTGStatDTO->id) {
                $channelPostsStat[] = $channelPostTGStatDTO;
            } else {
                Log::channel('content')->error('Не достаточно информации о посте.', [
                    'channel_id' => $channel->id,
                    'channel_post_tg_stat_dto' => $channelPostTGStatDTO,
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
