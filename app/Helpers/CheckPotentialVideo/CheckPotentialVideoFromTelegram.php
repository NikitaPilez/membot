<?php

namespace App\Helpers\CheckPotentialVideo;

use App\DTO\ChannelPostDTO;
use App\Helpers\TGStat;
use App\Models\Channel;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Dom\Node;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckPotentialVideoFromTelegram implements CheckPotentialVideoInterface
{
    public function getChannelPosts(Channel $channel): array
    {
        if (!$channel->parse_new_video_link) {
            return [];
        }

        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser([
            'customFlags' => [
                '--disable-blink-features',
                '--disable-blink-features=AutomationControlled'
            ],
            'sendSyncDefaultTimeout' => 10000,
        ]);

        $page = $browser->createPage();

        $page->navigate($channel->parse_new_video_link)->waitForNavigation();

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
                $channelPosts[] = new ChannelPostDTO(
                    id: $channelPostTGStatDTO->id,
                    createdAt: $channelPostTGStatDTO->createdAt,
                    description: $channelPostTGStatDTO->description,
                    link: Str::finish($channel->url, '/') . $channelPostTGStatDTO->id,
                );
            } else {
                Log::channel('content')->error('Не достаточно информации о посте.', [
                    'channel_id' => $channel->id,
                    'channel_post' => $channelPostTGStatDTO,
                ]);
            }
        }

        $browser->close();

        return $channelPosts;
    }
}
