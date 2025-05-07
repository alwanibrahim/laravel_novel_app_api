<?php

namespace App\Filament\Resources\AuthorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;

class NovelsRelationManager extends RelationManager
{
    protected static string $relationship = 'novels';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul'),

                Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->label('Deskripsi'),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Kategori'),

                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Tag'),

                FileUpload::make('cover_image')
                    ->image()
                    ->directory('novels/covers')
                    ->visibility('public')
                    ->label('Cover'),

                Select::make('status')
                    ->options([
                        'ongoing' => 'Berlangsung',
                        'completed' => 'Selesai',
                        'hiatus' => 'Hiatus',
                    ])
                    ->default('ongoing')
                    ->required()
                    ->label('Status'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Cover'),

                TextColumn::make('title')
                    ->searchable()
                    ->limit(30)
                    ->label('Judul'),

                TextColumn::make('category.name')
                    ->searchable()
                    ->label('Kategori'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'ongoing' => 'warning',
                        'hiatus' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),

                TextColumn::make('chapters_count')
                    ->counts('chapters')
                    ->label('Jumlah Chapter'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Kategori'),

                SelectFilter::make('status')
                    ->options([
                        'ongoing' => 'Berlangsung',
                        'completed' => 'Selesai',
                        'hiatus' => 'Hiatus',
                    ])
                    ->label('Status'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn($record) => route('filament.admin.resources.novels.edit', $record)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
