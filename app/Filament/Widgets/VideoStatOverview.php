<?php

namespace App\Filament\Widgets;

use App\Models\Video;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VideoStatOverview extends ChartWidget
{
    protected static ?string $heading = 'Видео';

    protected function getData(): array
    {
        $data = Video::query()
            ->where('publication_date', '>', now())
            ->where('publication_date', '<', now()->addDays(7)->endOfDay())
            ->groupBy(DB::raw('date'))
            ->orderBy(DB::raw('date'))
            ->get(array(
                DB::raw('Date(publication_date) as date'),
                DB::raw('COUNT(*) as "videos"')
            ))->toArray();

        $dates = [];

        foreach ($data as $item) {
            $dates[] = $item['date'];
        }

        $videosCount = [];

        foreach ($data as $item) {
            $videosCount[] = $item['videos'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Количество видео для контента',
                    'data' => $videosCount,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
