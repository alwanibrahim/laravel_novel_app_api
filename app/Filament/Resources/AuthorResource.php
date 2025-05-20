<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Penulis'),

                   FileUpload::make('profile_picture')
                ->label('Photo Profile')
                ->image()
                ->directory('profile_picture') // Akan disimpan di storage/app/public/cover_images
                ->disk('public') // Penting: harus "public" untuk bisa diakses
                ->visibility('public') // Supaya bisa diakses via browser
                ->imagePreviewHeight('150')
                ->maxSize(1024), // optional

                Textarea::make('bio')
                    ->columnSpanFull()
                    ->label('Biografi'),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                  ImageColumn::make('profile_picture')
    ->label('Profile Picture')
    ->getStateUsing(fn ($record) => asset('storage/' . $record->profile_picture))
    ->height(80),


                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
             TextColumn::make('bio')
    ->label('Bio')
    ->limit(50) // Batasi tampilan sampai 50 karakter
    ->tooltip(fn ($record) => $record->bio) // Tampilkan bio lengkap saat hover
    ->searchable()
    ->sortable(),

                TextColumn::make('novels_count')
                    ->counts('novels')
                    ->label('Jumlah Novel'),





                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tanggal Bergabung'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalDescription('Yakin ingin menghapus penulis ini? Tindakan ini mungkin akan mempengaruhi novel yang terkait.')
                    ->requiresConfirmation(),
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
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'view' => Pages\ViewAuthor::route('/{record}'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'bio', 'country'];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('novels');
    }
}
