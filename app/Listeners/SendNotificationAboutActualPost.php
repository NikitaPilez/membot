<?php

namespace App\Listeners;

use App\Events\CreatePostStatEvent;
use App\Models\ChannelPostStat;
use App\Services\CheckActualPostService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNotificationAboutActualPost
{
    private CheckActualPostService $checkActualPostService;

    /**
     * Create the event listener.
     */
    public function __construct(CheckActualPostService $checkActualPostService)
    {
        $this->checkActualPostService = $checkActualPostService;
    }

    /**
     * Handle the event.
     */
    public function handle(CreatePostStatEvent $event): void
    {
        $channelPostStat = $event->channelPostStat;
        $actualPostDto = $this->checkActualPostService->check($event->channelPostStat);

        if ($actualPostDto->isMostShared) {
//            Log::channel('content')->info('Актуальный пост по репостам', [
//                'channel_post' => $channelPostStat->post->description,
//                'channel_id' => $channelPostStat->post->channel_id,
//                'avg_shares' => $actualPostDto->avgShares,
//            ]);
        }

        if ($actualPostDto->isMostViewed) {
//            Log::channel('content')->info('Актуальный пост по просмотрам', [
//                'channel_post' => $channelPostStat->post->description,
//                'channel_id' => $channelPostStat->post->channel_id,
//                'avg_views' => $actualPostDto->avgViews,
//            ]);
        }
    }
}
