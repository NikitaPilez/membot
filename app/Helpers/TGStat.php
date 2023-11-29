<?php

namespace App\Helpers;

use App\DTO\ChannelPostTGStatDTO;
use Carbon\Carbon;
use HeadlessChromium\Dom\Node;

class TGStat
{

    public static function getHumanViews(string $string): int
    {
        preg_match('/([\d.]+)k?/', $string, $matches);

        $number = $matches[1];

        if ($number) {
            return (int) $number != $number ? (int) ($number * 1000) : (int) $number;
        }

        return 0;
    }

    public static function getChannelPostFromNode(Node $element): ChannelPostTGStatDTO
    {
        $shares = $element->querySelector('[data-original-title="Пересылок всего"]')?->getText();
        $views = $element->querySelector('[data-original-title="Количество просмотров публикации"]')?->getText();

        $id = $element->querySelector('[data-original-title="Количество просмотров публикации"]')?->getAttribute('href');
        preg_match('/\/(\d+)\/stat/', $id, $matches);
        $formattedId = (int) $matches[1];

        $createdAt = $element->querySelector('.post-header small')?->getText();
        $formattedCreatedAt = Carbon::parse($createdAt)->format('Y-m-d H:i:s');

        $description = $element->querySelector('.post-text')?->getText();

        return new ChannelPostTGStatDTO(
            id: $formattedId,
            views: self::getHumanViews($views),
            shares: self::getHumanViews($shares),
            createdAt: $formattedCreatedAt,
            description: $description,
        );
    }
}
