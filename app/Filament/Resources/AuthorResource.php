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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
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

                FileUpload::make('photo')
                    ->image()
                    ->directory('authors/photos')
                    ->visibility('public')
                    ->label('Foto Profil'),

                Textarea::make('bio')
                    ->columnSpanFull()
                    ->label('Biografi'),

                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir'),



                TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->label('Email'),

                TextInput::make('website')
                    ->url()
                    ->label('Website'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->circular()
                    ->label('Foto'),


                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                TextColumn::make('bio')
                    ->searchable()
                    ->sortable()
                    ->label('Bio'),

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
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'featured' => 'Direkomendasikan',
                    ])
                    ->label('Status'),

                TernaryFilter::make('has_novels')
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('novels'),
                        false: fn(Builder $query) => $query->whereDoesntHave('novels'),
                    )
                    ->label('Memiliki Novel'),
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
