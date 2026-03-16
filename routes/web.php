<?php

use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[HomeController::class,'landingPage']);


Route::match(['get','post'],'/login',[HomeController::class,'login'])->name('login');
Route::get('/logout',[HomeController::class,'logout'])->name('logout');

Route::middleware('super_admin:admin')->group(function () {
    Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard'])
        ->name('admin.dashboard');
});


Route::middleware('super_admin:depo,admin')->group(function () {
    Route::get('/depo/dashboard', [HomeController::class, 'depoDashboard'])
        ->name('depo.dashboard');
});

Route::middleware('super_admin:mpo,admin')->group(function () {
    Route::get('/mpo/dashboard', [HomeController::class, 'mpoDashboard'])
        ->name('mpo.dashboard');
});

Route::middleware('super_admin:chemist_house,admin')->group(function () {
    Route::get('/chemist-house/dashboard', [HomeController::class, 'chemistHouseDashboard'])
        ->name('chemist-house.dashboard');
});

Route::middleware('super_admin:asm,admin')->group(function () {
    Route::get('/asm/dashboard', [HomeController::class, 'asmDashboard'])
        ->name('asm.dashboard');
});

Route::middleware('super_admin:sm,admin')->group(function () {
    Route::get('/sm/dashboard', [HomeController::class, 'smDashboard'])
        ->name('sm.dashboard');
});

Route::middleware('super_admin:rsm,admin')->group(function () {
    Route::get('/rsm/dashboard', [HomeController::class, 'rsmDashboard'])
        ->name('rsm.dashboard');
});

Route::middleware('super_admin:nsm,admin')->group(function () {
    Route::get('/nsm/dashboard', [HomeController::class, 'nsmDashboard'])
        ->name('nsm.dashboard');
});

Route::middleware('super_admin:director,admin')->group(function () {
    Route::get('/director/dashboard', [HomeController::class, 'directorDashboard'])
        ->name('director.dashboard');
});





//Route::get('/admin/dashboard','HomeController@master')->name('dashboard')->middleware('super_admin');
//Route::get('/depo/dashboard','HomeController@depoDashboard')->name('depo.dashboard');





