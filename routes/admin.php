<?php
use Illuminate\Support\Facades\Route;


Route::middleware(['super_admin:admin'])->group(function () {

Route::group(['namespace' => 'Admin'], function () {

});

});



Route::middleware(['super_admin:admin','block_purchase_admin'])->group(function () {

    // Company Route
    Route::group(['namespace' => 'CompanySetting'], function () {
        Route::get('/main-company', [CompanySettingController::class, 'index'])->name('main.company.index');
        Route::put('/main-company/update/{id}', [CompanySettingController::class, 'update'])->name('main.company.update');
    });

  // Account Route
    Route::group(['namespace'=>'Account'],function(){
        Route::get('/account',[MainAccountController::class,'index'])->name('admin.account.index');
        Route::match(['get','post'],'/account/create',[MainAccountController::class,'create'])->name('admin.account.create');
        Route::get('/account/edit/{id}',[MainAccountController::class,'edit'])->name('admin.account.edit');
        Route::put('/account/update/{id}',[MainAccountController::class,'update'])->name('admin.account.update');
        Route::delete('/account/delete/{id}',[MainAccountController::class,'destroy'])->name('admin.account.delete');
    });


    // Gl account
    Route::group(['namespace'=>'GlAccount'],function(){
        Route::get('/gl-account',[GlAccountController::class,'index'])->name('admin.gl-account.index');
        Route::get('/gl-account/edit/{id}',[GlAccountController::class,'edit'])->name('admin.gl-account.edit');
        Route::put('/gl-account/update/{id}',[GlAccountController::class,'update'])->name('admin.gl-account.update');
    });

    // Chart of Account
    Route::group(['namespace'=>'ChartOfAccount'],function(){
        Route::get('/chart-of-account',[ChartOfAccountController::class,'index'])->name('admin.chart-of-account.index');
        Route::match(['get','post'],'/chart-of-account/create',[ChartOfAccountController::class,'create'])->name('admin.chart-of-account.create');
        Route::get('/chart-of-account/edit/{id}',[ChartOfAccountController::class,'edit'])->name('admin.chart-of-account.edit');
        Route::put('/chart-of-account/update/{id}',[ChartOfAccountController::class,'update'])->name('admin.chart-of-account.update');
        Route::delete('/chart-of-account/delete/{id}',[ChartOfAccountController::class,'destroy'])->name('admin.chart-of-account.delete');
    });


    // Party Route
    Route::group(['namespace'=>'Party'],function(){
        Route::get('/party',[PartyController::class,'index'])->name('admin.party.index');
        Route::match(['get','post'],'/party/create',[PartyController::class,'create'])->name('admin.party.create');
        Route::get('/party/edit/{id}',[PartyController::class,'edit'])->name('admin.party.edit');
        Route::put('/party/update/{id}',[PartyController::class,'update'])->name('admin.party.update');
        Route::delete('/party/delete/{id}',[PartyController::class,'destroy'])->name('admin.party.delete');
    });

});






