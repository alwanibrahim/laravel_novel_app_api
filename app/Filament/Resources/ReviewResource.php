<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'Manajemen Konten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('Pengguna'),

                Select::make('novel_id')
                    ->relationship('novel', 'title')
                    ->required()
                    ->label('Novel'),

                TextInput::make('rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required()
                    ->label('Rating'),

                Textarea::make('comment')
                    ->label('Komentar')
                    ->rows(4),

                TextInput::make('likes_count')
                    ->numeric()
                    ->default(0)
                    ->label('Jumlah Like'),

                Forms\Components\Toggle::make('is_spoiler')
                    ->label('Spoiler?')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('user.name')->label('Pengguna')->searchable(),
                TextColumn::make('novel.title')->label('Novel')->searchable(),
                TextColumn::make('rating')->label('Rating')->sortable(),
                TextColumn::make('comment')->label('Review')->limit(50),
                TextColumn::make('likes_count')->label('Likes'),
                BooleanColumn::make('is_spoiler')->label('Spoiler'),
                TextColumn::make('created_at')->label('Dibuat')->dateTime(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
