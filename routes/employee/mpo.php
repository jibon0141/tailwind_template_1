<?php

use App\Http\Controllers\Employee\Mpo\ChemistHouse\ChemistHouseController;
use App\Http\Controllers\Employee\Mpo\DueCollection\ChemistHouseDueCollectionController;
use App\Http\Controllers\Employee\Mpo\Sale\SaleController;
use App\Http\Controllers\Employee\Mpo\Stock\StockController;
use Illuminate\Support\Facades\Route;



Route::middleware('super_admin:mpo,admin')->group(function () {

    Route::group(['namespace'=>'Sale'],function() {
        Route::get('/sale', [SaleController::class, 'index'])->name('mpo.sale.index');
        Route::match(['get', 'post'], '/sale/create', [SaleController::class, 'create'])->name('mpo.sale.create');
        Route::get('/sale/show/{id}', [SaleController::class, 'show'])->name('mpo.sale.show');
        Route::get('/sale/print/{id}', [SaleController::class, 'print'])->name('mpo.sale.print');
        Route::get('/sale/pos-print/{id}', [SaleController::class, 'posPrint'])->name('mpo.sale.pos.print');
        // Extra Route
        Route::get('/getSaleData', [SaleController::class, 'getSaleData'])->name('mpo.sale.getSaleData');
        Route::get('/search-medicine', [SaleController::class, 'searchMedicine'])->name('mpo.sale.searchMedicine');
    });


    Route::group(['namespace'=>'ChemistHouseDuePayment'],function(){
        Route::get('/chemist-house-due-payment',[ChemistHouseDueCollectionController::class,'index'])->name('mpo.chemist-house-due-payment.index');
        Route::get('/chemist-house-due-payment/show/{id}',[ChemistHouseDueCollectionController::class,'show'])->name('mpo.chemist-house-due-payment.show');
        Route::get('/chemist-house-due-payment/print/{id}',[ChemistHouseDueCollectionController::class,'print'])->name('mpo.chemist-house-due-payment.print');
    });

    Route::group(['namespace'=>'ChemistHouse'],function(){
        Route::get('/chemist-house',[ChemistHouseController::class,'index'])->name('mpo.chemist-house.index');
        Route::match(['get','post'], '/chemist-house/create', [ChemistHouseController::class, 'create'])->name('mpo.chemist-house.create');
        Route::get('/chemist-house/edit/{id}', [ChemistHouseController::class, 'edit'])->name('mpo.chemist-house.edit');
        Route::put('/chemist-house/update/{id}', [ChemistHouseController::class, 'update'])->name('mpo.chemist-house.update');
    });

    Route::group(['namespace'=>'Stock'],function(){
        Route::get('/stock',[StockController::class,'index'])->name('mpo.stock.index');
    });


});
