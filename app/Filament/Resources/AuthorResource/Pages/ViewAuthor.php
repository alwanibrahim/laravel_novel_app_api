<?php

namespace App\Filament\Resources\AuthorResource\Pages;

use App\Filament\Resources\AuthorResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;

class ViewAuthor extends ViewRecord
{
    protected static string $resource = AuthorResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Penulis')
                    ->schema([
                        ImageEntry::make('photo')
                            ->circular()
                            ->label('Foto'),

                        TextEntry::make('name')
                            ->label('Nama Penulis'),

                        



                        TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                'featured' => 'warning',
                                default => 'gray',
                            })
                            ->label('Status'),
                    ])
                    ->columns(2),

                Section::make('Biografi')
                    ->schema([
                        TextEntry::make('bio')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Section::make('Statistik')
                    ->schema([
                        TextEntry::make('novels_count')
                            ->state(function ($record) {
                                return $record->novels()->count();
                            })
                            ->label('Jumlah Novel'),

                        TextEntry::make('chapters_count')
                            ->state(function ($record) {
                                $novelIds = $record->novels()->pluck('id');
                                return \App\Models\Chapter::whereIn('novel_id', $novelIds)->count();
                            })
                            ->label('Total Chapter'),

                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Tanggal Bergabung'),

                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Terakhir Diperbarui'),
                    ])
                    ->columns(2),
            ]);
    }
}
