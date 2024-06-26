<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelResource\Pages;
use App\Filament\Resources\ChannelResource\RelationManagers;
use App\Models\Channel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChannelResource extends Resource
{
    protected static ?string $model = Channel::class;

    protected static ?string $navigationLabel = 'Каналы';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Каналы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Социальная сеть')
                    ->required()
                    ->options([
                        'telegram' => 'Телеграм',
                        'youtube' => 'Ютуб',
                    ]),
                Forms\Components\TextInput::make('url')
                    ->label('Ссылка на канал')
                    ->url()
                    ->suffixIcon('heroicon-m-globe-alt')
                    ->required(),
                Forms\Components\TextInput::make('parse_new_video_link')
                    ->label('Ссылка, откуда брать контент')
                    ->url()
                    ->suffixIcon('heroicon-m-globe-alt'),
                Forms\Components\TextInput::make('youtube_id')
                    ->label('ID ютуб канала'),
                Forms\Components\TextInput::make('tgstat_link')
                    ->label('Ссылка на tgstat')
                    ->url()
                    ->suffixIcon('heroicon-m-globe-alt'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Активен?'),
                Forms\Components\Toggle::make('is_notify')
                    ->label('Отправлять уведомления?'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->toggleable()->searchable()->label('Имя'),
                Tables\Columns\SelectColumn::make('type')
                    ->label('Социальная сеть')
                    ->sortable()
                    ->toggleable()
                    ->rules(['required'])
                    ->options([
                    'telegram' => 'Телеграм',
                    'youtube' => 'Ютуб',
                ]),
                Tables\Columns\TextColumn::make('url')->label('Ссылка на канал')->toggleable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Активен?')->toggleable(),
                Tables\Columns\ToggleColumn::make('is_notify')->label('Отправлять уведомления?')->toggleable(),
                Tables\Columns\TextColumn::make('parse_new_video_link')->label('Ссылка, откуда брать контент')->toggleable(),
                Tables\Columns\TextColumn::make('youtube_id')->label('ID ютуб канала')->toggleable(),
                Tables\Columns\TextColumn::make('tgstat_link')->label('Ссылка на tgstat')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Социальная сеть')
                    ->options([
                        'telegram' => 'Телеграм',
                        'youtube' => 'Ютуб',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChannels::route('/'),
            'create' => Pages\CreateChannel::route('/create'),
            'edit' => Pages\EditChannel::route('/{record}/edit'),
        ];
    }
}
