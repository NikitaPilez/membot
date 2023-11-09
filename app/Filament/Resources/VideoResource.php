<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'Управление видео';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Имя на гугл диске')->disabled(),
                Forms\Components\TextInput::make('comment')->label('Комментарий'),
                Forms\Components\TextInput::make('description')->label('Описание для видео'),
                Forms\Components\TextInput::make('google_file_id')->label('ID на гугл диске')->disabled(),
                Forms\Components\TextInput::make('url')->label('Урл видео')->disabled(),
                Forms\Components\TextInput::make('content_url')->label('Урл исходного видео')->disabled(),
                Forms\Components\TextInput::make('type')->label('Соц. сеть')->disabled(),
                Forms\Components\DateTimePicker::make('sent_at')->label('Когда отправлено?')->disabled(),
                Forms\Components\DateTimePicker::make('publication_date')->label('Время отправки')->native(false)->timezone('Europe/Minsk'),
                Forms\Components\FileUpload::make('preview_image_path')->image()->disk('public')->disabled(),
                Forms\Components\Toggle::make('is_sent')->label('Отправлено в телеграм?')->disabled(),
                Forms\Components\Checkbox::make('is_prod')->label('Прод видео?')->disabled(fn (Video $video) => $video->is_sent),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('comment')->toggleable()->searchable()->label('Комментарий'),
                Tables\Columns\TextColumn::make('publication_date')->dateTime('d.m.Y H:i', 'Europe/Minsk')->label('Время отправки')->sortable(),
                Tables\Columns\TextColumn::make('sent_at')->dateTime('d.m.Y H:i', 'Europe/Minsk')->label('Отправлено в')->sortable(),
                Tables\Columns\ToggleColumn::make('is_sent')->disabled()->label('Отправлено?')->toggleable(),
                Tables\Columns\CheckboxColumn::make('is_prod')->label('Прод?')->toggleable()->disabled(fn (Video $video) => $video->is_sent),
                Tables\Columns\ImageColumn::make('preview_image_path')->label('Превью изображение')->toggleable()->square()->size(75),
                Tables\Columns\TextColumn::make('description')->label('Описание к видео')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i', 'Europe/Minsk')->label('Создано в')->toggleable(),
                Tables\Columns\TextColumn::make('url')->limit(30)->toggleable()->label('Урл')->searchable(),
                Tables\Columns\TextColumn::make('name')->toggleable()->label('Имя в гугл диске'),
                Tables\Columns\TextColumn::make('google_file_id')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('content_url')->limit(30)->toggleable()->label('Исходный урл'),
                Tables\Columns\TextColumn::make('type')->label('Соц. сеть')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_sent')->label('Статус')
                    ->options([
                        '1' => 'Отправлено',
                        '0' => 'Не отправлено',
                    ]),
                Tables\Filters\Filter::make('publication_date')
                    ->indicateUsing(fn (array $data) => 'Дата отправки ' . $data['publication_date'])
                ->form([
                    Forms\Components\DatePicker::make('publication_date')
                        ->label('Дата отправки')
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if ($data['publication_date']) {
                        return $query->whereDate('publication_date', $data['publication_date']);
                    }
                    return $query;
                })
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->url(fn (Video $video) => route('send.telegram', ['video' => $video]))
                    ->icon('heroicon-m-inbox-arrow-down')
                    ->visible(fn (Video $video) => !$video->is_sent),
                Tables\Actions\EditAction::make(),

            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
            ])
            ->defaultSort('publication_date', 'desc');
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
