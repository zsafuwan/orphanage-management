<?php

use App\Http\Controllers\CareRecordController;
use App\Http\Controllers\DevelopmentRevisionController;
use App\Http\Controllers\DonationRecordController;
use App\Http\Controllers\InventoryRecordController;
use App\Http\Controllers\StaffVolunteerRecordController;
use App\Http\Controllers\WelfareRequestRecordController;
use App\Http\Middleware\CareAccess;
use Illuminate\Support\Facades\Route;

Route::middleware(CareAccess::class)->group(function () {
    Route::get('/care-data', [CareRecordController::class, 'snapshot']);
    Route::get('/care-data/child/{child}/photo', [CareRecordController::class, 'photo'])->whereNumber('child');
    Route::post('/care-data/child/{child}/photo', [CareRecordController::class, 'updatePhoto'])->whereNumber('child');
    Route::post('/care-data/paper', [CareRecordController::class, 'paper']);
    Route::post('/care-data/intake', [CareRecordController::class, 'intake']);
    Route::post('/care-data/{resource}', [CareRecordController::class, 'save']);
});

Route::prefix('staff-volunteer-data')->group(function () {
    Route::get('/', [StaffVolunteerRecordController::class, 'snapshot']);
    Route::post('/personnel', [StaffVolunteerRecordController::class, 'personnel']);
    Route::post('/shift', [StaffVolunteerRecordController::class, 'shift']);
    Route::post('/attendance', [StaffVolunteerRecordController::class, 'attendance']);
    Route::post('/credential', [StaffVolunteerRecordController::class, 'credential']);
    Route::post('/event', [StaffVolunteerRecordController::class, 'event']);
    Route::post('/{resource}', [StaffVolunteerRecordController::class, 'save']);
});

Route::prefix('donation-data')->group(function () {
    Route::get('/', [DonationRecordController::class, 'snapshot']);
    Route::post('/donor', [DonationRecordController::class, 'registerDonor']);
    Route::post('/contribution', [DonationRecordController::class, 'recordContribution']);
    Route::post('/{resource}', [DonationRecordController::class, 'save']);
});

Route::prefix('inventory-data')->group(function () {
    Route::get('/', [InventoryRecordController::class, 'snapshot']);
    Route::post('/item', [InventoryRecordController::class, 'addItem']);
    Route::post('/adjust', [InventoryRecordController::class, 'adjustCount']);
    Route::post('/receipt', [InventoryRecordController::class, 'receiveStock']);
    Route::post('/issue', [InventoryRecordController::class, 'issueStock']);
    Route::post('/supplier', [InventoryRecordController::class, 'registerSupplier']);
    Route::post('/{resource}', [InventoryRecordController::class, 'save']);
});

Route::prefix('welfare-data')->group(function () {
    Route::get('/', [WelfareRequestRecordController::class, 'snapshot']);
    Route::post('/request', [WelfareRequestRecordController::class, 'submitRequest']);
    Route::post('/decision', [WelfareRequestRecordController::class, 'decideRequest']);
    Route::post('/receive', [WelfareRequestRecordController::class, 'receiveRequest']);
    Route::post('/user', [WelfareRequestRecordController::class, 'registerUser']);
    Route::post('/notifications/read', [WelfareRequestRecordController::class, 'markNotificationsRead']);
    Route::post('/{resource}', [WelfareRequestRecordController::class, 'save']);
});

if (app()->environment('local')) {
    Route::get('/__development/revision', DevelopmentRevisionController::class)
        ->name('development.revision');
}

Route::view('/', 'home')->name('home');
Route::view('/volunteer/register', 'volunteer-register')->name('volunteer.register');

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
