<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama'),

                TextInput::make('username')
                    ->required()
                    ->label('Username'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Email'),

                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state)) // Hanya hash password saat create
                    ->dehydrated(fn($state) => filled($state)) // Dehidrasi jika password terisi
                    ->required(fn(string $operation): bool => $operation === 'create') // Hanya diperlukan saat create
                    ->label('Password'),

                Toggle::make('is_verified')
                    ->label('Terverifikasi')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                TextColumn::make('username')
                    ->searchable()
                    ->sortable()
                    ->label('User name'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_verified')
                    ->boolean()
                    ->sortable()
                    ->label('Terverifikasi'),

            TextColumn::make('is_active')
                ->label('Status')
                ->badge()
                ->sortable()
                ->formatStateUsing(fn($state) => $state ? 'Aktif' : 'Tidak Aktif')
                ->colors([
                    'success' => fn($state) => $state == 1,
                    'danger' => fn($state) => $state == 0,
                ]),


            TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Login Terakhir'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tanggal Dibuat'),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tanggal Diperbarui'),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->query(fn(Builder $query): Builder => $query->where('is_verified', true))
                    ->label('Terverifikasi'),

                Tables\Filters\Filter::make('unverified')
                    ->query(fn(Builder $query): Builder => $query->where('is_verified', false))
                    ->label('Belum Terverifikasi'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'), // Pastikan ini ada
        ];
    }
}
