<?php

use App\Livewire\Auth\Login;
use App\Livewire\Home;
use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

Route::get('/', function (AuthService $auth) {
    return redirect()->route($auth->hasToken() ? 'home' : 'login');
})->name('start');

Route::get('/login', Login::class)->name('login');

Route::middleware('mobile.auth')->group(function () {
    Route::get('/home', Home::class)->name('home');
});
