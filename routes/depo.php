<?php

use App\Http\Controllers\Depo\ChemistHouse\ChemistHouseController;
use App\Http\Controllers\Depo\ChemistHouse\DefaultChemistHouseController;
use App\Http\Controllers\Depo\CreditVoucher\CreditVoucherController;
use App\Http\Controllers\Depo\DebitVoucher\DebitVoucherController;
use App\Http\Controllers\Depo\Depo\DepoController;
use App\Http\Controllers\Depo\DepoDuePayment\DepoDuePaymentController;
use App\Http\Controllers\Depo\DepoMedicine\DepoMedicineController;
use App\Http\Controllers\Depo\DirectSale\DirectSaleController;
use App\Http\Controllers\Depo\GlAccount\GlAccountController;
use App\Http\Controllers\Depo\ChartOfAccount\ChartOfAccountController;
use App\Http\Controllers\Depo\Account\AccountController;
use App\Http\Controllers\Depo\Party\PartyController;
use App\Http\Controllers\Depo\Purchase\PurchaseController;
use App\Http\Controllers\Depo\Report\CashFlowController;
use App\Http\Controllers\Depo\Report\ChemistHouseLedgerController;
use App\Http\Controllers\Depo\Sale\SaleController;
use App\Http\Controllers\Depo\Stock\StockController;
use App\Http\Controllers\Depo\Supplier\SupplierController;
use App\Http\Controllers\Depo\ChemistHouseDuePayment\ChemistHouseDuePaymentController;
use Illuminate\Support\Facades\Route;


Route::middleware('super_admin:depo,admin')->group(function () {

    // Medicine Route
    Route::group(['namespace' => 'DepoMedicine'], function () {
        Route::get('/depo-medicine', [DepoMedicineController::class, 'index'])->name('depo.medicine.index');
        Route::match(['get', 'post'], '/depo-medicine/create', [DepoMedicineController::class, 'create'])->name('depo.medicine.create');
        Route::get('/depo-medicine/show/{id}', [DepoMedicineController::class, 'show'])->name('depo.medicine.show');
        Route::get('/depo-medicine/edit/{id}', [DepoMedicineController::class, 'edit'])->name('depo.medicine.edit');
        Route::put('/depo-medicine/update/{id}', [DepoMedicineController::class, 'update'])->name('depo.medicine.update');
        Route::delete('/depo-medicine/delete/{id}', [DepoMedicineController::class, 'destroy'])->name('depo.medicine.delete');
        Route::get('/depo-get-strengths/{id}', [DepoMedicineController::class, 'getStrength'])->name('depo.get.strengths');
    });

    // Supplier Route
    Route::group(['namespace' =>  'Supplier'], function() {
        Route::get('/depo-supplier',[SupplierController::class,'index'])->name('depo.supplier.index');
        Route::match(['get','post'],'/depo-supplier/create',[SupplierController::class,'create'])->name('depo.supplier.create');
        Route::get('/depo-supplier/show/{id}',[SupplierController::class,'show'])->name('depo.supplier.show');
        Route::get('/depo-supplier/edit/{id}',[SupplierController::class,'edit'])->name('depo.supplier.edit');
        Route::put('/depo-supplier/update/{id}',[SupplierController::class,'update'])->name('depo.supplier.update');
        Route::delete('/depo-supplier/delete/{id}',[SupplierController::class,'destroy'])->name('depo.supplier.delete');
    });

    // Gl account
    Route::group(['namespace'=>'GlAccount'],function(){
        Route::get('/gl-account',[GlAccountController::class,'index'])->name('depo.gl-account.index');
        Route::get('/gl-account/edit/{id}',[GlAccountController::class,'edit'])->name('depo.gl-account.edit');
        Route::put('/gl-account/update/{id}',[GlAccountController::class,'update'])->name('depo.gl-account.update');
    });

    // Chart of Account
    Route::group(['namespace'=>'ChartOfAccount'],function(){
        Route::get('/chart-of-account',[ChartOfAccountController::class,'index'])->name('depo.chart-of-account.index');
        Route::match(['get','post'],'/chart-of-account/create',[ChartOfAccountController::class,'create'])->name('depo.chart-of-account.create');
        Route::get('/chart-of-account/edit/{id}',[ChartOfAccountController::class,'edit'])->name('depo.chart-of-account.edit');
        Route::put('/chart-of-account/update/{id}',[ChartOfAccountController::class,'update'])->name('depo.chart-of-account.update');
        Route::delete('/chart-of-account/delete/{id}',[ChartOfAccountController::class,'destroy'])->name('depo.chart-of-account.delete');
    });

    // Account Route
    Route::group(['namespace'=>'Account'],function(){
        Route::get('/account',[AccountController::class,'index'])->name('depo.account.index');
        Route::match(['get','post'],'/account/create',[AccountController::class,'create'])->name('depo.account.create');
        Route::get('/account/edit/{id}',[AccountController::class,'edit'])->name('depo.account.edit');
        Route::put('/account/update/{id}',[AccountController::class,'update'])->name('depo.account.update');
        Route::delete('/account/delete/{id}',[AccountController::class,'destroy'])->name('depo.account.delete');
    });

    // Party Route
    Route::group(['namespace'=>'Party'],function(){
        Route::get('/party',[PartyController::class,'index'])->name('depo.party.index');
        Route::match(['get','post'],'/party/create',[PartyController::class,'create'])->name('depo.party.create');
        Route::get('/party/edit/{id}',[PartyController::class,'edit'])->name('depo.party.edit');
        Route::put('/party/update/{id}',[PartyController::class,'update'])->name('depo.party.update');
        Route::delete('/party/delete/{id}',[PartyController::class,'destroy'])->name('depo.party.delete');
    });

    // Debit Voucher Route
    Route::group(['namespace'=>'DebitVoucher'],function(){
        Route::get('/debit-voucher',[DebitVoucherController::class,'index'])->name('depo.debit-voucher.index');
        Route::match(['get','post'],'/debit-voucher/create',[DebitVoucherController::class,'create'])->name('depo.debit-voucher.create');
        Route::get('/debit-voucher/show/{id}',[DebitVoucherController::class,'show'])->name('depo.debit-voucher.show');
        Route::put('/debit-voucher/update/{id}',[DebitVoucherController::class,'update'])->name('depo.debit-voucher.update');
        Route::delete('/debit-voucher/delete/{id}',[DebitVoucherController::class,'destroy'])->name('depo.debit-voucher.delete');
        Route::get('/debit-voucher/print/{id}', [DebitVoucherController::class, 'print'])->name('depo.debit-voucher.print');

        // AJAX routes
        Route::get('/debit-voucher/get-parties', [DebitVoucherController::class, 'getParties'])->name('depo.debit-voucher.get-parties');
        Route::get('/debit-voucher/get-bank-accounts', [DebitVoucherController::class, 'getBankAccounts'])->name('depo.debit-voucher.get-bank-accounts');
        Route::get('/debit-voucher/get-expense-coa', [DebitVoucherController::class, 'getExpenseCoa'])->name('depo.debit-voucher.get-expense-coa');
    });

    Route::group(['namespace' => 'CreditVoucher'], function () {
        Route::get('/credit-voucher', [CreditVoucherController::class, 'index'])->name('depo.credit-voucher.index');
        Route::match(['get', 'post'], '/credit-voucher/create', [CreditVoucherController::class, 'create'])->name('depo.credit-voucher.create');
        Route::get('/credit-voucher/show/{id}', [CreditVoucherController::class, 'show'])->name('depo.credit-voucher.show');
        Route::put('/credit-voucher/update/{id}', [CreditVoucherController::class, 'update'])->name('depo.credit-voucher.update');
        Route::get('/credit-voucher/print/{id}', [CreditVoucherController::class, 'print'])->name('depo.credit-voucher.print');

        // AJAX routes
        Route::get('/credit-voucher/get-parties', [CreditVoucherController::class, 'getParties'])->name('depo.credit-voucher.get-parties');
        Route::get('/credit-voucher/get-bank-accounts', [CreditVoucherController::class, 'getBankAccounts'])->name('depo.credit-voucher.get-bank-accounts');
        Route::get('/credit-voucher/get-income-coa', [CreditVoucherController::class, 'getIncomeCoa'])->name('depo.credit-voucher.get-income-coa');
    });

    // Purchase Route
    Route::group(['namespace' => 'Purchase'], function () {
        Route::get('/purchase',[PurchaseController::class,'index'])->name('depo.purchase.index');
        Route::get('/pending-purchase',[PurchaseController::class,'pendingPurchase'])->name('depo.purchase.pending');
        Route::get('/pending-purchase/show/{id}',[PurchaseController::class,'pendingPurchaseShow'])->name('depo.pending-purchase.show');
        Route::get('/pending-purchase/print/{id}',[PurchaseController::class,'pendingPurchasePrint'])->name('depo.pending-purchase.print');
        Route::get('/pending-purchase/pos-print/{id}',[PurchaseController::class,'pendingPurchasePosPrint'])->name('depo.pending-purchase.pos.print');
        Route::get('/pending-purchase/edit/{id}',[PurchaseController::class,'pendingPurchaseEdit'])->name('depo.pending-purchase.edit');
        Route::put('/pending-purchase/update/{id}',[PurchaseController::class,'purchaseVerification'])->name('depo.pending-purchase.update');


        Route::match(['get','post'],'/purchase/create',[PurchaseController::class,'create'])->name('depo.purchase.create');
        Route::get('/purchase/show/{id}',[PurchaseController::class,'show'])->name('depo.purchase.show');
        Route::put('/purchase/update/{id}',[PurchaseController::class,'update'])->name('depo.purchase.update');
        Route::delete('/purchase/delete/{id}',[PurchaseController::class,'destroy'])->name('depo.purchase.delete');
        Route::get('/purchase/print/{id}',[PurchaseController::class,'print'])->name('depo.purchase.print');
        Route::get('/purchase/pos-print/{id}',[PurchaseController::class,'posPrint'])->name('depo.purchase.pos.print');
//        Route::get('/getPurchaseData',[PurchaseController::class, 'getPurchaseData'])->name('depo.purchase.getPurchaseData');
        Route::get('/getPurchaseData',[PurchaseController::class,'getPurchaseData'])->name('depo.purchase.getPurchaseData');
        Route::get('/search-medicine',[PurchaseController::class,'searchMedicine'])->name('depo.purchase.searchMedicine');
    });


    Route::group(['namespace' => 'Stock'], function () {
        Route::get('/stock',[StockController::class,'index'])->name('depo.stock.index');
    });


    // Sale Route
    Route::group(['namespace'=>'Sale'],function(){
        Route::get('/sale',[SaleController::class,'index'])->name('depo.sale.index');
        Route::match(['get','post'],'/sale/create',[SaleController::class,'create'])->name('depo.sale.create');
        Route::get('/sale/show/{id}',[SaleController::class,'show'])->name('depo.sale.show');
        Route::get('/sale/manage-edit/{id}',[SaleController::class,'manageEdit'])->name('depo.sale.manage.edit');
        Route::put('/sale/manage-update/{id}',[SaleController::class,'manageUpdate'])->name('depo.sale.manage.update');
        // Extra Route
        Route::get('/getSaleData',[SaleController::class,'getSaleData'])->name('depo.sale.getSaleData');
        Route::get('/sale/print/{id}',[SaleController::class,'print'])->name('depo.sale.print');
        Route::get('/sale/pos-print/{id}',[SaleController::class,'posPrint'])->name('depo.sale.pos.print');
//        Route::get('/search-medicine',[SaleController::class,'searchMedicine'])->name('depo.sale.searchMedicine');
    });

// Direct Sale Route
    Route::group(['namespace' => 'DirectSale'],function(){
        Route::get('/direct-sale',[DirectSaleController::class,'index'])->name('depo.direct-sale.index');
        Route::match(['get','post'],'/direct-sale/create',[DirectSaleController::class,'create'])->name('depo.direct-sale.create');
        Route::get('/direct-sale/show/{id}',[DirectSaleController::class,'show'])->name('depo.direct-sale.show');
        Route::get('/direct-sale/print/{id}', [DirectSaleController::class, 'print'])->name('depo.direct-sale.print');
        Route::get('/direct-sale-pos/print/{id}', [DirectSaleController::class, 'posPrint'])->name('depo.direct-sale.pos.print');


        // Extra Route
        Route::get('/getSaleData', [DirectSaleController::class, 'getSaleData'])->name('depo.sale.getSaleData');
        Route::get('/search-medicine', [DirectSaleController::class, 'searchMedicine'])->name('depo.sale.searchMedicine');
        Route::get('/get-depo-accounts',[DirectSaleController::class, 'getDepoAccounts'])->name('depo.direct-sale.getDepoAccounts');
    });

// Chemist House
    Route::group(['namespace'=>'ChemistHouse'],function(){
        Route::get('/chemist-house',[ChemistHouseController::class,'index'])->name('depo.chemist-house.index');
        Route::match(['get','post'],'/chemist-house/create',[ChemistHouseController::class,'create'])->name('depo.chemist-house.create');
        Route::get('/chemist-house/edit/{id}',[ChemistHouseController::class,'edit'])->name('depo.chemist-house.edit');
        Route::put('/chemist-house/update/{id}',[ChemistHouseController::class,'update'])->name('depo.chemist-house.update');

        //  Default Chemist House
        Route::get('/default-chemist-house',[DefaultChemistHouseController::class,'index'])->name('depo.default-chemist-house.index');
        Route::match(['get','post'],'/default-chemist-house/create',[DefaultChemistHouseController::class,'create'])->name('depo.default-chemist-house.create');
        Route::get('/default-chemist-house/edit/{id}',[DefaultChemistHouseController::class,'edit'])->name('depo.default-chemist-house.edit');
        Route::put('/default-chemist-house/update/{id}',[DefaultChemistHouseController::class,'update'])->name('depo.default-chemist-house.update');
    });

    // Chemist House Due Payment Route
    Route::group(['namespace'=>'ChemistHouseDuePayment'],function(){
        Route::get('/chemist-house-due-payment',[ChemistHouseDuePaymentController::class,'index'])->name('depo.chemist-house-due-payment.index');
        Route::match(['get','post'],'/chemist-house-due-payment/create',[ChemistHouseDuePaymentController::class,'create'])->name('depo.chemist-house-due-payment.create');
        Route::get('/chemist-house-due-payment/show/{id}',[ChemistHouseDuePaymentController::class,'show'])->name('depo.chemist-house-due-payment.show');
        Route::get('/chemist-house-due-payment/get-data',[ChemistHouseDuePaymentController::class,'getChemistHouseData'])->name('depo.chemist-house-due-payment.get-data');
    });

// Depo Route
    Route::group(['namespace'=>'Depo'],function(){
        Route::get('/',[DepoController::class,'index'])->name('depo.list.index');
        Route::get('/show/{id}',[DepoController::class,'show'])->name('depo.view');
    });

// Depo Due Payment Route
   Route::group(['namespace'=>'DepoDuePayment'],function(){
       Route::get('/depo-due-payment', [DepoDuePaymentController::class, 'index'])->name('depo.depo-due-payment.index');
       Route::match(['get','post'],'/depo-due-payment/create',[DepoDuePaymentController::class, 'create'])->name('depo.depo-due-payment.create');
       Route::get('/depo-due-payment/show/{id}', [DepoDuePaymentController::class, 'show'])->name('depo.depo-due-payment.show');
   });

// Report Route
    Route::group(['namespace'=>'Report'],function(){
        Route::get('/report/chemist-houses', [ChemistHouseLedgerController::class, 'getChemistHouses'])->name('report.chemist-houses.ajax');
        Route::get('/report/chemist-house-ledger-report',[ChemistHouseLedgerController::class,'index'])->name('depo.chemist-house-ledger-report.index');
        Route::get('/report/accounts',[CashFlowController::class,'getDepoAccounts'])->name('report.accounts.ajax');
        Route::get('/report/cash-flow',[CashFlowController::class,'cashFlow'])->name('report.cash-flow');
    });

});
