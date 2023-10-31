<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('comment')->toggleable(),
                Tables\Columns\TextColumn::make('name')->toggleable(),
                Tables\Columns\TextColumn::make('google_file_id')->limit(30)->toggleable(),
                Tables\Columns\ToggleColumn::make('is_sent')->disabled()->label('Отправлено?')->toggleable(),
                Tables\Columns\TextColumn::make('url')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('content_url')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('type')->label('Соц. сеть'),
                Tables\Columns\TextColumn::make('sent_at')->dateTime('d.m.Y H:i')->label('Отправлено в'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('created_at', 'desc');
    }
}
