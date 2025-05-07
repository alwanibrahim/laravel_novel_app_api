<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    // Hapus kata kunci static di sini
    public function getActions(): array
    {
        return [];  // Menonaktifkan aksi Edit
    }
}
