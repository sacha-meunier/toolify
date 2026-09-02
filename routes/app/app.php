<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/onboarding', 'pages::app.onboarding.index')->name('onboarding');
});

Route::middleware(['auth', 'verified', 'onboarded'])->group(function () {
    Route::livewire('/search', 'pages::app.search.index')->name('search');
    Route::livewire('/discovery', 'pages::app.discovery.index')->name('discovery');
    Route::livewire('/inbox/{notification?}', 'pages::app.inbox.index')->name('inbox');
    Route::livewire('/tools/{tool:slug}', 'pages::app.tools.show')->name('tools.show');

    Route::livewire('/workspaces/create-or-join', 'pages::app.workspaces.create-or-join')->name('workspaces.create-or-join');

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
            Route::livewire('/preferences', 'pages::app.settings.account.preferences')->name('preferences');
        });

        Route::prefix('workspace')->name('workspace.')->group(function () {
            Route::livewire('/general', 'pages::app.settings.workspace.general')->name('general');
            Route::livewire('/members', 'pages::app.settings.workspace.members')->name('members');

            Route::prefix('teams')->name('teams.')->group(function () {
                Route::livewire('/', 'pages::app.settings.workspace.teams.index')->name('index');
                Route::livewire('/create', 'pages::app.settings.workspace.teams.create')->name('create');
            });
        });

        Route::prefix('teams/{team:slug}')->name('teams.')->group(function () {
            Route::livewire('/general', 'pages::app.settings.teams.general')->name('general');
            Route::livewire('/members', 'pages::app.settings.teams.members')->name('members');

            Route::prefix('listing')->name('listing.')->group(function () {
                Route::livewire('/', 'pages::app.settings.teams.listing.index')->name('index');
                Route::livewire('/identity', 'pages::app.settings.teams.listing.identity')->name('identity');
                Route::livewire('/details', 'pages::app.settings.teams.listing.details')->name('details');
                Route::livewire('/links', 'pages::app.settings.teams.listing.links')->name('links');
                Route::livewire('/gallery', 'pages::app.settings.teams.listing.gallery')->name('gallery');
                Route::livewire('/basics', 'pages::app.settings.teams.listing.basics')->name('basics');
                Route::livewire('/danger-zone', 'pages::app.settings.teams.listing.danger-zone')->name('danger-zone');
            });
        });
    });
});
