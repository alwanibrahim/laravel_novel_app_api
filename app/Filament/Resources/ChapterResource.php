<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChapterResource\Pages;
use App\Models\Chapter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';
    protected static ?string $navigationGroup = 'Manajemen Konten';


    // Form untuk input data Chapter
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('novel_id')
                    ->relationship('novel', 'title')
                    ->required()
                    ->label('Judul Novel'),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label('Judul Chapter'),

                Forms\Components\Textarea::make('content')
                    ->required()
                    ->label('Isi Chapter')
                    ->rows(10),

                Forms\Components\TextInput::make('chapter_number')
                    ->required()
                    ->numeric()
                    ->label('Nomor Chapter'),

                Forms\Components\TextInput::make('word_count')
                    ->numeric()
                    ->label('Jumlah Kata'),
            ]);
    }


    // Tabel untuk menampilkan data Chapter
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('novel.title')->label('Judul Novel')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('title')->label('Judul Chapter')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('chapter_number')->label('Chapter Ke-')->sortable(),
                Tables\Columns\TextColumn::make('word_count')->label('Jumlah Kata')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime(),
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
    // Relasi jika ada, misalnya jika Chapter memiliki relasi dengan model lain
    public static function getRelations(): array
    {
        return [
            // Misalnya jika Chapter memiliki relasi, tambahkan di sini
        ];
    }

    // Menyediakan berbagai halaman untuk ChapterResource
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChapters::route('/'),
            'create' => Pages\CreateChapter::route('/create'),
            'edit' => Pages\EditChapter::route('/{record}/edit'),
        ];
    }
}
