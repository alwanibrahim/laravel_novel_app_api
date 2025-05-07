<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;


Route::get('/', function () {
    return view('welcome');
});

Filament::serving(function () {
    Filament::registerNavigationGroups([
        'Main',
    ]);
});
