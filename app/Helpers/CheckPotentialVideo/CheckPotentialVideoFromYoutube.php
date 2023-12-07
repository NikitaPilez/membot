<?php

namespace App\Helpers\CheckPotentialVideo;

use App\DTO\ChannelPostDTO;
use App\Models\Channel;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Dom\Node;
use Illuminate\Support\Facades\Log;

class CheckPotentialVideoFromYoutube implements CheckPotentialVideoInterface
{

    public function getChannelPosts(Channel $channel): array
    {
        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser([
            'customFlags' => ['--disable-blink-features', '--disable-blink-features=AutomationControlled'],
            'sendSyncDefaultTimeout' => 10000,
        ]);

        $page = $browser->createPage();
        $page->navigate($channel->parse_new_video_link)->waitForNavigation();

        sleep(1);

        $dom = $page->dom();
        $elements = $dom->querySelectorAll('.ytd-rich-item-renderer');

        if (!$elements) {
            Log::channel('content')->error('Не найдены посты.', [
                'channel_id' => $channel->id,
            ]);
        }

        /** @var ChannelPostDTO[] $channelPosts */
        $channelPosts = [];

        /** @var Node $element */
        foreach ($elements as $element) {
            $description = $element->querySelector('#details span')?->getText();
            $image = $element->querySelector('#thumbnail img')?->getAttribute('src');
            $shortUrl = $element->querySelector('#thumbnail')?->getAttribute('href');
            $hash = hash_hmac('sha256', $shortUrl, '1');
            $id = (int) fmod(hexdec($hash), 10000) + 1;

            if ($description && $id) {
                $channelPosts[] = new ChannelPostDTO(
                    id: $id,
                    createdAt: now(),
                    description: $description,
                    image: $image,
                );
            } else {
                Log::channel('content')->error('Не достаточно информации о посте.', [
                    'channel_id' => $channel->id,
                    'channel_post_id' => $id,
                    'channel_post_description' => $description,
                    'channel_post_image' => $image,
                ]);
            }
        }

        $browser->close();

        return $channelPosts;
    }
}
