<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NovelResource\Pages;
use App\Models\Novel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\DatePicker;





class NovelResource extends Resource
{
    protected static ?string $model = Novel::class;


    protected static ?string $navigationIcon = 'heroicon-o-bookmark';
    protected static ?string $navigationGroup = 'Manajemen Konten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Novel'),
                Textarea::make('description')
                    ->required()
                    ->label('Deskripsi Novel'),
            SpatieMediaLibraryFileUpload::make('cover_image'),

            Select::make('category_id')
                    ->label('Categry')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->relationship('category', 'name'),
            Select::make('author_id')
                    ->label('Penulis')
                    ->relationship('author', 'name')
                    ->required()
                    ->searchable()
                    ->relationship('author', 'name'),
            Select::make('tags')
    ->label('Tags')
    ->multiple()
    ->relationship('tags', 'name')
    ->searchable()
    ->preload()
    ->required(),
     DatePicker::make('publication_date')
            ->label('Tanggal Terbit')
            ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->searchable(),
                TextColumn::make('title')->label('Judul')->sortable()->searchable(),
                TextColumn::make('description')->label('Deskripsi')->limit(30),
            ImageColumn::make('cover_image')
    ->label('Cover')
    ->getStateUsing(fn ($record) => $record->getFirstMediaUrl('cover'))
    ->square(),

            TextColumn::make('category.name')->label('Kategori'),
                TextColumn::make('author.name')->label('Penulis'),
                TextColumn::make('publication_date')->label('Tanggal Terbit')->date(),
                TextColumn::make('page_count')->label('Halaman'),
                TextColumn::make('language')->label('Bahasa')->sortable(),
                BooleanColumn::make('is_featured')->label('Unggulan'),
                TextColumn::make('average_rating')->label('Rating'),
                TextColumn::make('view_count')->label('View'),
                TextColumn::make('created_at')->label('Dibuat')->dateTime(),
                TextColumn::make('updated_at')->label('Diperbarui')->dateTime(),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNovels::route('/'),
            'create' => Pages\CreateNovel::route('/create'),
            'edit' => Pages\EditNovel::route('/{record}/edit'),
        ];
    }
}
