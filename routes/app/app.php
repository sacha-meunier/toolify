<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/search', 'pages::app.search.index')->name('search');
    Route::livewire('/discovery', 'pages::app.discovery.index')->name('discovery');
    Route::livewire('/tools/{tool:slug}', 'pages::app.tools.show')->name('tools.show');

    Route::livewire('/stack', 'pages::app.stacks.personal')->name('stacks.personal');
    Route::livewire('/stack/workspace', 'pages::app.stacks.workspace')->name('stacks.workspace');
    Route::livewire('/stack/teams/{team:slug}', 'pages::app.stacks.teams.show')->name('stacks.teams.show');

    Route::livewire('/surveys', 'pages::app.surveys.index')->name('surveys.personal')->defaults('scope', 'personal');
    Route::livewire('/surveys/workspace', 'pages::app.surveys.index')->name('surveys.workspace')->defaults('scope', 'workspace');
    Route::livewire('/surveys/teams/{team:slug}', 'pages::app.surveys.index')->name('surveys.teams.show')->defaults('scope', 'team');
    Route::livewire('/surveys/{survey:name}', 'pages::app.surveys.show')->name('surveys.show');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::prefix('account')->name('account.')->group(function () {
            Route::livewire('/profile', 'pages::app.settings.account.profile')->name('profile');
            Route::livewire('/security', 'pages::app.settings.account.security')->name('security');
        });

        Route::prefix('workspace')->name('workspace.')->group(function () {
            Route::livewire('/general', 'pages::app.settings.workspace.general')->name('general');
            Route::livewire('/members', 'pages::app.settings.workspace.members')->name('members');
        });

        Route::prefix('teams/{team:slug}')->name('teams.')->group(function () {
            Route::livewire('/general', 'pages::app.settings.teams.general')->name('general');
            Route::livewire('/members', 'pages::app.settings.teams.members')->name('members');

            Route::prefix('listing')->name('listing.')->group(function () {
                Route::livewire('/', 'pages::app.settings.teams.listing.index')->name('index');
                Route::livewire('/identity', 'pages::app.settings.teams.listing.identity')->name('identity');
                Route::livewire('/details', 'pages::app.settings.teams.listing.details')->name('details');
        });
    });
});
