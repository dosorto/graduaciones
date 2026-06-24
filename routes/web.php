<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventInvitationAssetController;
use App\Http\Controllers\EventInvitationDesignController;
use App\Http\Controllers\EventGuestController;
use App\Http\Controllers\EventInvitationController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ValidatorInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/i/{token}/image.png', [PublicInvitationController::class, 'image'])->name('invitations.public.image');
Route::get('/i/{token}', [PublicInvitationController::class, 'show'])->name('invitations.public.show');
Route::get('/invitation-default-background.png', [EventInvitationAssetController::class, 'showDefaultBackground'])->name('invitations.default-background');
Route::get('/events/{event}/invitation-background', [EventInvitationAssetController::class, 'showBackground'])->name('events.invitation-background');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'event-manager',
])->group(function () {
    Route::resource('events', EventController::class);
    Route::get('/events/{event}/invitation-design', [EventInvitationDesignController::class, 'edit'])->name('events.invitation-design.edit');
    Route::put('/events/{event}/invitation-design', [EventInvitationDesignController::class, 'update'])->name('events.invitation-design.update');
    Route::get('/events-template/guests', [EventGuestController::class, 'template'])->name('events.guests.template');
    Route::get('/events/{event}/guests/import', [EventGuestController::class, 'createImport'])->name('events.guests.import.create');
    Route::post('/events/{event}/guests/import/preview', [EventGuestController::class, 'previewImport'])->name('events.guests.import.preview');
    Route::post('/events/{event}/guests/import/execute', [EventGuestController::class, 'executeImport'])->name('events.guests.import.execute');
    Route::post('/events/{event}/guests', [EventGuestController::class, 'store'])->name('events.guests.store');
    Route::post('/events/{event}/guests/import', [EventGuestController::class, 'import'])->name('events.guests.import');
    Route::delete('/events/{event}/guests/{guest}', [EventGuestController::class, 'destroy'])->name('events.guests.destroy');
    Route::get('/events/{event}/guests/{guest}/invitations', [EventInvitationController::class, 'showGuest'])->name('events.guests.invitations.show');
    Route::post('/events/{event}/guests/{guest}/invitations', [EventInvitationController::class, 'add'])->name('events.guests.invitations.add');
    Route::post('/events/{event}/guests/{guest}/invitations/{invitation}/share', [EventInvitationController::class, 'share'])->name('events.guests.invitations.share');
    Route::delete('/events/{event}/guests/{guest}/invitations/{invitation}', [EventInvitationController::class, 'destroy'])->name('events.guests.invitations.destroy');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'validator',
])->group(function () {
    Route::get('/validator', [ValidatorInvitationController::class, 'index'])->name('validator.dashboard');
    Route::post('/validator/invitations/{invitation}/consume', [ValidatorInvitationController::class, 'consume'])->name('validator.invitations.consume');
    Route::post('/validator/invitations/{invitation}/enable-reentry', [ValidatorInvitationController::class, 'enableReentry'])->name('validator.invitations.enable-reentry');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'admin',
])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});
