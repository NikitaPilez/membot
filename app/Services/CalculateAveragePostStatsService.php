<?php

namespace App\Services;

use App\DTO\GetAverageStatByChannelPost;
use App\Models\Channel;
use App\Models\ChannelPostStat;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateAveragePostStatsService
{
    public function run(): void
    {
        $channels = Channel::query()->where('is_active', 1)->has('posts')->get();

        foreach ($channels as $channel) {
            $this->saveChannelAverageStat($this->getAverageStatByChannel($channel), $channel->id);
        }
    }

    /**
     * @param Channel $channel
     * @return GetAverageStatByChannelPost[]
     */
    public function getAverageStatByChannel(Channel $channel): array
    {
        $averageStatByChannel = [];

        for ($i = 1; $i < 25; $i++) {
            $countMinutesMin = $i * 60;
            $countMinutesMax = $countMinutesMin + 10;

            /** @var Collection<int, ChannelPostStat> $channelPostStats */
            $channelPostStats = $this->getChannelStatByHour($channel, $countMinutesMin, $countMinutesMax);

            $averageStatByChannel[] = $this->getChannelAverageStatByHour($channelPostStats, $channel, $i);
        }

        return $averageStatByChannel;
    }

    public function getChannelStatByHour(Channel $channel, int $countMinutesMin, int $countMinutesMax)
    {
        return ChannelPostStat::join('channel_posts', 'channel_posts.id', '=', 'channel_post_stats.channel_post_id')
            ->whereRaw('TIMESTAMPDIFF(MINUTE, channel_posts.publication_at, channel_post_stats.created_at) >= ' . $countMinutesMin)
            ->whereRaw('TIMESTAMPDIFF(MINUTE, channel_posts.publication_at, channel_post_stats.created_at) < ' . $countMinutesMax)
            ->where('channel_posts.channel_id', $channel->id)
            ->get(['channel_id', 'channel_post_id', 'views', 'shares']);
    }

    public function getChannelAverageStatByHour(Collection $channelPostStats, Channel $channel, int $hourCount): GetAverageStatByChannelPost
    {
        $viewsCount = 0;
        $sharesCount = 0;
        $countChannelPostStats = $channelPostStats->count();

        foreach ($channelPostStats as $channelPostStat) {
            $viewsCount += $channelPostStat->views;
            $sharesCount += $channelPostStat->shares;
        }

        $avgShares = $countChannelPostStats ? (int) $sharesCount / $countChannelPostStats : 0;
        $viewsCount = $countChannelPostStats ? (int) $viewsCount / $countChannelPostStats : 0;

        return new GetAverageStatByChannelPost(
            channelId: $channel->id,
            hourCount: $hourCount,
            avgShares: $avgShares,
            avgViews: $viewsCount,
        );
    }

    /**
     * @param array<int, GetAverageStatByChannelPost> $averageStatByChannel
     * @return void
     */
    public function saveChannelAverageStat(array $averageStatByChannel, $channelId): void
    {
        $dataForInsert = [];

        foreach ($averageStatByChannel as $averageStatByHour) {
            $dataForInsert[] = [
                'channel_id' => $averageStatByHour->channelId,
                'hour_count' => $averageStatByHour->hourCount,
                'avg_share' => $averageStatByHour->avgShares,
                'avg_views' => $averageStatByHour->avgViews,
                'created_at' => now(),
            ];
        }

        try {
            DB::beginTransaction();
            DB::table('channel_average_stats')->where('channel_id', $channelId)->delete();
            DB::table('channel_average_stats')->insert($dataForInsert);
            DB::commit();
        } catch (Exception $exception) {
            Log::channel('content')->error('Ошибка при обновлении дневной статистики по каналам', [
                'channel_id' => $channelId,
                'message' => $exception->getMessage(),
            ]);

            DB::rollBack();
        }

    }
}
