<?php

use App\Http\Controllers\Backend\Admin\ProfileController;
use App\Http\Controllers\Backend\CompanySetting\CompanySettingController;
use App\Http\Controllers\Backend\User\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['super_admin:admin'])->group(function () {

Route::group(['namespace' => 'Admin'], function () {

});

});



Route::middleware(['super_admin:admin','block_purchase_admin'])->group(function () {

    // Profile Route
    Route::group(['namespace' => 'Admin'], function () {
        Route::get('/profile/password', [ProfileController::class, 'passwordEdit'])->name('admin.profile.password.edit');
        Route::post('/profile/password/update', [ProfileController::class, 'passwordUpdate'])->name('admin.profile.password.update');
    });

    // Company Route
    Route::group(['namespace' => 'CompanySetting'], function () {
        Route::get('/main-company', [CompanySettingController::class, 'index'])->name('main.company.index');
        Route::put('/main-company/update/{id}', [CompanySettingController::class, 'update'])->name('main.company.update');
    });

    Route::group(['namespace'=>'User'],function(){
        Route::get('/user',[UserController::class,'index'])->name('admin.user.index');
        Route::match(['get','post'],'/user/create',[UserController::class,'create'])->name('admin.user.create');
        Route::get('/user/get-referrer',[UserController::class,'getReferrer'])->name('admin.user.get-referrer');
        Route::get('/user/{id}',[UserController::class,'show'])->name('admin.user.show');
        Route::get('/user/edit/{id}',[UserController::class,'edit'])->name('admin.user.edit');
        Route::get('/user/access/{id}',[UserController::class,'userAccess'])->name('admin.user.access');
        Route::put('/user/update/{id}',[UserController::class,'update'])->name('admin.user.update');
        Route::delete('/user/delete/{id}',[UserController::class,'destroy'])->name('admin.user.delete');
        Route::post('/user/{id}/nominee',[UserController::class,'storeNominee'])->name('admin.user.nominee.store');
        Route::put('/user/{id}/nominee/{nominee}',[UserController::class,'updateNominee'])->name('admin.user.nominee.update');
        Route::delete('/user/{id}/nominee/{nominee}',[UserController::class,'destroyNominee'])->name('admin.user.nominee.destroy');
    });


});






