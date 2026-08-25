<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (Request $request) => redirect('/'.SetLocale::resolve($request)));

Route::prefix('{locale}')
    ->group(function () {
        require __DIR__.'/app/app.php';
        require __DIR__.'/public.php';
    });

/* Reached whenever no route matched at all : either the URL is missing the "{locale}"
 * prefix, or it's a wrong prefix or the intented page does not exist which therefore
 * returns a 404. */
Route::fallback(function (Request $request) {
    $firstSegment = explode('/', trim($request->path(), '/'))[0] ?? '';

    if (in_array($firstSegment, config('app.available_locales'), true)) {
        abort(404);
    }

    return redirect('/'.SetLocale::resolve($request).'/'.$request->path());
});
