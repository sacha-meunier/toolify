<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('discovery');
    }

    return view('pages::public.home');
})->name('home');

Route::view('/homepage', 'pages::public.home')->name('public.homepage');
Route::view('/discover', 'pages::public.discover')->name('public.discover');
Route::view('/features', 'pages::public.features')->name('public.features');
Route::view('/contact', 'pages::public.contact')->name('public.contact');

Route::livewire('/invitations/{invitation}', 'pages::public.invitations.onboarding')
    ->name('invitations.onboarding')
    ->middleware('signed');
