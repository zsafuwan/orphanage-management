<?php

use Illuminate\Support\Facades\Route;

if (app()->environment('local')) {
    Route::get('/__development/revision', \App\Http\Controllers\DevelopmentRevisionController::class)
        ->name('development.revision');
}

Route::view('/', 'home')->name('home');

Route::view('/dashboard', 'portal.dashboard')->name('dashboard');
Route::view('/dashboard/admin', 'portal.dashboard')->name('dashboard.admin');
Route::view('/dashboard/staff', 'portal.staff-child-welfare')->name('dashboard.staff');
Route::view('/staff/child-welfare', 'portal.staff-child-welfare')->name('staff.child-welfare');
Route::view('/child-welfare', 'portal.child-welfare')->name('child-welfare');
Route::view('/staff/inventory', 'portal.staff-inventory')->name('staff.inventory');
Route::view('/inventory/staff', 'portal.staff-inventory')->name('inventory.staff');
Route::view('/welfare-requests', 'portal.welfare-requests')->name('welfare-requests');
Route::view('/staff/welfare-requests', 'portal.staff-welfare-requests')->name('staff.welfare-requests');
Route::view('/welfare-requests/staff', 'portal.staff-welfare-requests')->name('welfare-requests.staff');
Route::view('/staff-volunteers', 'portal.staff-volunteers')->name('staff-volunteers');
Route::view('/staff/staff-volunteers', 'portal.staff-volunteers')->name('staff.staff-volunteers');
Route::view('/staff/volunteers', 'portal.staff-volunteers')->name('staff.volunteers');
Route::view('/staff-volunteers/staff', 'portal.staff-volunteers')->name('staff-volunteers.staff');
Route::view('/donations', 'portal.donations')->name('donations');
Route::view('/staff/donations', 'portal.staff-donations')->name('staff.donations');
Route::view('/donations/staff', 'portal.staff-donations')->name('donations.staff');
Route::view('/register', 'portal.register')->name('register');
Route::get('/profile', function () {
    if (request('view') === 'director' || request('role') === 'director') {
        return view('portal.director.profile');
    }
    if (request('view') === 'staff' || request('role') === 'staff') {
        return view('portal.staff-profile');
    }
    return view('portal.profile');
})->name('profile');
Route::view('/staff/profile', 'portal.staff-profile')->name('staff.profile');
Route::view('/profile/staff', 'portal.staff-profile')->name('profile.staff');

// Orphanage Director Portal Routes
Route::prefix('director')->name('director.')->group(function () {
    Route::view('/', 'portal.director.dashboard')->name('dashboard');
    Route::view('/dashboard', 'portal.director.dashboard')->name('dashboard.index');
    Route::view('/inventory-requests', 'portal.director.inventory-requests')->name('inventory-requests');
    Route::view('/medical-records', 'portal.director.medical-records')->name('medical-records');
    Route::view('/children-education', 'portal.director.children-education')->name('children-education');
    Route::view('/staff-volunteers', 'portal.director.staff-volunteers')->name('staff-volunteers');
    Route::view('/donations', 'portal.director.donations')->name('donations');
    Route::view('/profile', 'portal.director.profile')->name('profile');
    Route::view('/register-staff', 'portal.director.register-staff')->name('register-staff');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Authenticated endpoints for future backend integration
});

require __DIR__.'/settings.php';
