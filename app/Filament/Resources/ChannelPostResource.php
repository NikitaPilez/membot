<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelPostResource\Pages;
use App\Filament\Resources\ChannelPostResource\RelationManagers;
use App\Models\ChannelPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class ChannelPostResource extends Resource
{
    protected static ?string $model = ChannelPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Посты каналов';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Каналы';


    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('channel_id')
                    ->relationship(name: 'channel', titleAttribute: 'name')->disabled(),
                Forms\Components\TextInput::make('post_id')
                    ->label('ID поста')
                    ->disabled(),
                Forms\Components\RichEditor::make('description')
                    ->label('Описание'),
                Forms\Components\DateTimePicker::make('publication_at')
                    ->label('Время публикации?')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channel.name')
                    ->toggleable()
                    ->label('Имя канала'),
                Tables\Columns\TextColumn::make('post_id')
                    ->toggleable()
                    ->label('ID поста'),
                Tables\Columns\TextColumn::make('publication_at')
                    ->toggleable()
                    ->label('Время опубликования')
                    ->dateTime('d.m.Y H:i'),
                Tables\Columns\TextColumn::make('stat.views_after_hour')
                    ->toggleable()
                    ->label('Просмотры за час')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stat.views_after_sixth_hour')
                    ->toggleable()
                    ->label('Просмотры за 6 часов')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stat.views_after_twelve_hour')
                    ->toggleable()
                    ->label('Просмотры за 12 часов')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stat.views_after_day')
                    ->toggleable()
                    ->label('Просмотры за сутки')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->toggleable()
                    ->label('Описание')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')->label('Канал')
                    ->relationship('channel', 'name'),
                Tables\Filters\Filter::make('publication_at')
                    ->indicateUsing(function (array $data): ?string {

                        if (! $data['publication_at']) {
                            return null;
                        }

                        return 'Дата публикации ' . Carbon::parse($data['publication_at'])->format('d.m.Y');
                    })
                    ->form([
                        Forms\Components\DatePicker::make('publication_at')
                            ->label('Дата публикации')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['publication_at']) {
                            return $query->whereDate('publication_at', $data['publication_at']);
                        }
                        return $query;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('stat')
                    ->label('Статистика')
                    ->modalContent(fn (ChannelPost $channelPost) => view('filament.stat.channel_post_stat', [
                        'channelPost' => $channelPost
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('publication_at', 'desc');
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
            'index' => Pages\ListChannelPosts::route('/'),
            'create' => Pages\CreateChannelPost::route('/create'),
            'edit' => Pages\EditChannelPost::route('/{record}/edit'),
        ];
    }
}
