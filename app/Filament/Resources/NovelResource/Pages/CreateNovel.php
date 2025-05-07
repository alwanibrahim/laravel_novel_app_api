<?php

namespace App\Filament\Resources\NovelResource\Pages;

use App\Filament\Resources\NovelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNovel extends CreateRecord
{
    protected static string $resource = NovelResource::class;
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl(); // ini akan kembali ke list (index) page
    }
}
