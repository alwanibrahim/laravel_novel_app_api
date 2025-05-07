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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;






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
             FileUpload::make('cover_image')
                ->label('Cover Image')
                ->image()
                ->directory('cover_images') // Akan disimpan di storage/app/public/cover_images
                ->disk('public') // Penting: harus "public" untuk bisa diakses
                ->visibility('public') // Supaya bisa diakses via browser
                ->imagePreviewHeight('150')
                ->maxSize(1024), // optional

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

               Toggle::make('is_featured')
                ->label('Featured?')
                ->inline(false)
                ->default(false)

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
    ->getStateUsing(fn ($record) => asset('storage/' . $record->cover_image))
    ->height(80),
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
