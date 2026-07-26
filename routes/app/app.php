<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/search', 'pages::app.search')->name('search');
    Route::livewire('/tools/{tool:slug}', 'pages::app.tools.show')->name('tools.show');
});
