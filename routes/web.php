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

Route::get('/', function () {
    return redirect('/login');
});


Route::match(['get','post'],'/login',[HomeController::class,'login'])->name('login');
Route::get('/logout',[HomeController::class,'logout'])->name('logout');

Route::middleware('super_admin:admin')->group(function () {
    Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard'])
        ->name('admin.dashboard');
});







//Route::get('/admin/dashboard','HomeController@master')->name('dashboard')->middleware('super_admin');
//Route::get('/depo/dashboard','HomeController@depoDashboard')->name('depo.dashboard');





