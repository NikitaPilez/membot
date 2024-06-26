<?php

namespace App\Services;

use App\DTO\ChannelPostTGStatDTO;
use App\Events\CreatePostStatEvent;
use App\Helpers\TGStat;
use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\ChannelPostStat;
use Carbon\Carbon;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Dom\Node;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UpdateChannelPostStatService
{
    public function run(): void
    {
        $channels = Channel::query()
            ->where('is_active', 1)
            ->whereNotNull('tgstat_link')
            ->has('posts')
            ->get();

        foreach ($channels as $channel) {
            $channelPostStatsFromTG = $this->getChannelStatFromTgStat($channel);

            $postIdsForUpdating = array_column($channelPostStatsFromTG, 'id');

            /** @var Collection<int, ChannelPost> $postsNeedingStatUpdate */
            $postsNeedingStatUpdate = $this->getPostsNeedingStatUpdate($channel, $postIdsForUpdating);

            foreach ($channelPostStatsFromTG as $channelPostStat) {
                $channelPost = $postsNeedingStatUpdate->get($channelPostStat->id);

                if (!$channelPost) {
                    continue;
                }

                $this->updateViewsStat($channelPost, $channelPostStat);
            }
        }
    }

    /**
     * @param Channel $channel
     * @return Collection<int, ChannelPost>
     */
    public function getPostsNeedingStatUpdate(Channel $channel, ?array $postIdsForUpdating): Collection
    {
        return ChannelPost::query()
            ->where('channel_id', $channel->id)
            ->whereBetween('publication_at', [
                now()->subDay()->subMinutes(5),
                now()->subHour(),
            ])
            ->where(function (Builder $query) {
                $query->whereDoesntHave('stats', function ($subQuery) {
                    $subQuery->where('created_at', '>', now()->subHour());
                })->orWhereDoesntHave('stats');
            })
            ->whereIn('post_id', $postIdsForUpdating)
            ->get()
            ->keyBy('post_id');
    }

    /**
     * @return array<ChannelPostTGStatDTO>
     */
    public function getChannelStatFromTgStat(Channel $channel): array
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
        $hourAgo = now()->subHour();

        /** @var ChannelPostStat $statLessHourAgo */
        $statLessHourAgo = $channelPost->stats()->where('created_at', '>', $hourAgo)->first();

        if ($statLessHourAgo) {
            $createdAt = $statLessHourAgo->created_at->addHour();
        } else {
            $publicationDate = Carbon::parse($channelPost->publication_at);
            $hoursDifference = now()->diffInHours($publicationDate);
            $createdAt = $publicationDate->addHours($hoursDifference);
        }

        $channelPostStat = ChannelPostStat::query()->create([
            'channel_post_id' => $channelPost->id,
            'views' => $channelPostTGStatDTO->views,
            'shares' => $channelPostTGStatDTO->shares,
            'created_at' => $createdAt,
        ]);

        CreatePostStatEvent::dispatch($channelPostStat);
    }
}
