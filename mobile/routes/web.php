<?php

use App\Livewire\Auth\Login;
use App\Livewire\Buildings\Form as BuildingForm;
use App\Livewire\Buildings\Index as BuildingIndex;
use App\Livewire\Buildings\Show as BuildingShow;
use App\Livewire\Home;
use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

Route::get('/', function (AuthService $auth) {
    return redirect()->route($auth->hasToken() ? 'home' : 'login');
})->name('start');

Route::get('/login', Login::class)->name('login');

Route::middleware('mobile.auth')->group(function () {
    Route::get('/home', Home::class)->name('home');

    Route::middleware('mobile.management')->group(function () {
        Route::get('/buildings', BuildingIndex::class)->name('buildings.index');
        Route::get('/buildings/create', BuildingForm::class)->name('buildings.create');
        Route::get('/buildings/{building}/edit', BuildingForm::class)->whereNumber('building')->name('buildings.edit');
        Route::get('/buildings/{building}', BuildingShow::class)->whereNumber('building')->name('buildings.show');
    });
});
