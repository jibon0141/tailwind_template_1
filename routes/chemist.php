<?php

use App\Http\Controllers\ChemistHouse\ChemistOrder\ChemistOrderController;
use App\Http\Controllers\ChemistHouse\ChemistOrder\OrderProcessController;
use Illuminate\Support\Facades\Route;



Route::middleware('super_admin:chemist_house')->group(function () {

    Route::group(['namespace' => 'ChemistOrder'], function () {
        Route::get('/chemist-order', [ChemistOrderController::class, 'index'])->name('chemist.order.index');
        Route::match(['get', 'post'], '/chemist-order/create', [ChemistOrderController::class, 'create'])->name('chemist.order.create');
        Route::get('/chemist-order/show/{id}', [ChemistOrderController::class, 'show'])->name('chemist.order.show');
        Route::get('/chemist-order/print/{id}', [ChemistOrderController::class, 'print'])->name('chemist.order.print');

        Route::get('/getSaleData', [ChemistOrderController::class, 'getSaleData'])->name('chemist.order.getSaleData');
        Route::get('/search-medicine', [ChemistOrderController::class, 'searchMedicine'])->name('chemist.order.searchMedicine');

        Route::get('/medicine-list', [OrderProcessController::class, 'medicineList'])->name('chemist.house.medicine.list');
        Route::get('/search-company', [OrderProcessController::class, 'searchCompanyAjax'])->name('chemist.search.company.ajax');
        Route::get('/search-medicine-ajax', [OrderProcessController::class, 'searchMedicineAjax'])->name('chemist.medicine.searchMedicineAjax');
        Route::get('/search-med-ajax', [OrderProcessController::class, 'searchMedicineAjx'])->name('chemist.medicine.list.ajax');

        // Cart
        Route::post('/chemist-order/store-cart', [OrderProcessController::class, 'storeCart'])->name('chemist.order.storeCart');
        // web.php
        Route::get('/chemist-cart/preload', [OrderProcessController::class, 'preloadCart'])->name('chemist.cart.preload');

    });




});
