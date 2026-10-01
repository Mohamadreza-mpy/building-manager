<?php

use App\Livewire\Announcements\Form as AnnouncementForm;
use App\Livewire\Announcements\Index as AnnouncementIndex;
use App\Livewire\Apartments\Form as ApartmentForm;
use App\Livewire\Apartments\Index as ApartmentIndex;
use App\Livewire\Apartments\Show as ApartmentShow;
use App\Livewire\Auth\Login;
use App\Livewire\Buildings\Form as BuildingForm;
use App\Livewire\Buildings\Index as BuildingIndex;
use App\Livewire\Buildings\Show as BuildingShow;
use App\Livewire\Charges\Form as ChargeForm;
use App\Livewire\Charges\Index as ChargeIndex;
use App\Livewire\Charges\Show as ChargeShow;
use App\Livewire\Expenses\Form as ExpenseForm;
use App\Livewire\Expenses\Index as ExpenseIndex;
use App\Livewire\Home;
use App\Livewire\Notifications\Index as NotificationIndex;
use App\Livewire\Owners\Form as OwnerForm;
use App\Livewire\Owners\Index as OwnerIndex;
use App\Livewire\Requests\Form as RequestForm;
use App\Livewire\Requests\Index as RequestIndex;
use App\Livewire\Requests\Respond as RequestRespond;
use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

Route::get('/', function (AuthService $auth) {
    return redirect()->route($auth->hasToken() ? 'home' : 'login');
})->name('start');

Route::get('/login', Login::class)->name('login');

Route::middleware('mobile.auth')->group(function () {
    Route::get('/home', Home::class)->name('home');
    Route::get('/charges/{charge}', ChargeShow::class)->whereNumber('charge')->name('charges.show');
    Route::get('/buildings/{building}/expenses', ExpenseIndex::class)->whereNumber('building')->name('expenses.index');
    Route::get('/buildings/{building}/announcements', AnnouncementIndex::class)->whereNumber('building')->name('announcements.index');
    Route::get('/requests', RequestIndex::class)->name('requests.index');
    Route::get('/notifications', NotificationIndex::class)->name('notifications.index');

    Route::middleware('mobile.resident')->group(function () {
        Route::get('/charges', ChargeIndex::class)->name('charges.mine');
        Route::get('/requests/create', RequestForm::class)->name('requests.create');
    });

    Route::middleware('mobile.management')->group(function () {
        Route::get('/owners', OwnerIndex::class)->name('owners.index');
        Route::get('/owners/create', OwnerForm::class)->name('owners.create');
        Route::get('/buildings', BuildingIndex::class)->name('buildings.index');
        Route::get('/buildings/create', BuildingForm::class)->name('buildings.create');
        Route::get('/buildings/{building}/edit', BuildingForm::class)->whereNumber('building')->name('buildings.edit');
        Route::get('/buildings/{building}', BuildingShow::class)->whereNumber('building')->name('buildings.show');
        Route::get('/buildings/{building}/apartments', ApartmentIndex::class)->whereNumber('building')->name('apartments.index');
        Route::get('/buildings/{building}/apartments/create', ApartmentForm::class)->whereNumber('building')->name('apartments.create');
        Route::get('/apartments/{apartment}/edit', ApartmentForm::class)->whereNumber('apartment')->name('apartments.edit');
        Route::get('/apartments/{apartment}', ApartmentShow::class)->whereNumber('apartment')->name('apartments.show');
        Route::get('/buildings/{building}/charges', ChargeIndex::class)->whereNumber('building')->name('charges.index');
        Route::get('/buildings/{building}/charges/create', ChargeForm::class)->whereNumber('building')->name('charges.create');
        Route::get('/buildings/{building}/expenses/create', ExpenseForm::class)->whereNumber('building')->name('expenses.create');
        Route::get('/buildings/{building}/announcements/create', AnnouncementForm::class)->whereNumber('building')->name('announcements.create');
        Route::get('/requests/{residentRequest}/respond', RequestRespond::class)->whereNumber('residentRequest')->name('requests.respond');
    });
});
