<?php

namespace App\Filament\Resources\ChannelPostResource\Pages;

use App\Filament\Resources\ChannelPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChannelPost extends EditRecord
{
    protected static string $resource = ChannelPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
