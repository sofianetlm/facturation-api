<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['fr', 'ar'], true), 404);

    session(['locale' => $locale]);

    return back();
})->name('locale.switch');