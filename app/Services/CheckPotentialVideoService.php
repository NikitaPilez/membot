<?php

namespace App\Services;

use App\DTO\ChannelPostDTO;
use App\Helpers\TGStat;
use App\Models\Channel;
use App\Models\ChannelPost;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Dom\Node;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckPotentialVideoService
{
    public function run(): void
    {
        $channels =  Channel::query()
            ->where('is_active', 1)
            ->whereNotNull('tgstat_link')
            ->get();

        foreach ($channels as $channel) {
            $this->check($channel);
        }
    }

    public function check(Channel $channel): void
    {
        $parsedChannelPosts = $this->getChannelPosts($channel);
        $this->createNewChannelPosts($channel, $parsedChannelPosts);
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
            Log::channel('content')->error('Не найдены посты.', [
                'channel_id' => $channel->id,
            ]);
        }

        /** @var ChannelPostDTO[] $channelPosts */
        $channelPosts = [];

        /** @var Node $element */
        foreach ($elements as $element) {
            $channelPostTGStatDTO = TGStat::getChannelPostFromNode($element);

            if ($channelPostTGStatDTO->id && $channelPostTGStatDTO->createdAt) {
                $channelPosts[] = $channelPostTGStatDTO;
            } else {
                Log::channel('content')->error('Не достаточно информации о посте.', [
                    'channel_id' => $channel->id,
                    'channel_post_tg_stat_dto' => $channelPostTGStatDTO,
                ]);
            }
        }

        $browser->close();

        return $channelPosts;
    }
}
