<?php

namespace App\Filament\Resources\ChannelPostResource\Pages;

use App\Filament\Resources\ChannelPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChannelPosts extends ListRecords
{
    protected static string $resource = ChannelPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
