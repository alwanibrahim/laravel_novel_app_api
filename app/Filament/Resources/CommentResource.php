<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use App\Models\User;
use App\Models\Review;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;


class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';
    protected static ?string $navigationGroup = 'Manajemen Konten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Select::make('review_id')
                    ->label('Review Novel')
                    ->relationship('review', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->novel->title . ' - ' . \Str::limit($record->comment, 20))
                    ->searchable()
                    ->required(),

                TextInput::make('content')
                    ->label('Isi Komentar')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('likes_count')
                    ->label('Jumlah Like')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('user.name')->label('Pengguna')->searchable(),
                TextColumn::make('review.novel.title')->label('Review Novel')->searchable(),
                TextColumn::make('content')->label('Isi')->limit(50),
                TextColumn::make('likes_count')->label('Likes')->sortable(),
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
