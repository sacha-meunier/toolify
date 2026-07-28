<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/search', 'pages::app.search')->name('search');
    Route::livewire('/discovery', 'pages::app.discovery')->name('discovery');
    Route::livewire('/tools/{tool:slug}', 'pages::app.tools.show')->name('tools.show');

    Route::livewire('/stack', 'pages::app.stacks.personal')->name('stacks.personal');
    Route::livewire('/stack/workspace', 'pages::app.stacks.workspace')->name('stacks.workspace');
    Route::livewire('/stack/teams/{team:slug}', 'pages::app.stacks.teams.show')->name('stacks.teams.show');
});
