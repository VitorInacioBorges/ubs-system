<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/register/{id?}', function (?string $id = null) {
    return view('register', [
        'search' => request('search'),
        'id' => $id,
    ]);
})->name('register');

Route::post('/login', function (Request $request) {
    $data = $request->all();

    return dd($data);
})->name('web');
