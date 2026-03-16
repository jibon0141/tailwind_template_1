<?php

use App\Http\Controllers\Backend\Account\MainAccountController;
use App\Http\Controllers\Backend\Admin\AdminPurchaseController;
use App\Http\Controllers\Backend\Admin\PurchaseAdminController;
use App\Http\Controllers\Backend\Brand\BrandController;
use App\Http\Controllers\Backend\ChartOfAccount\ChartOfAccountController;
use App\Http\Controllers\Backend\Chemist\ChemistHouseController;
use App\Http\Controllers\Backend\Company\CompanyController;
use App\Http\Controllers\Backend\CompanySetting\CompanySettingController;
use App\Http\Controllers\Backend\CreditVoucher\CreditVoucherController;
use App\Http\Controllers\Backend\Depo\DepoController;
use App\Http\Controllers\Backend\District\DistrictController;
use App\Http\Controllers\Backend\Division\DivisionController;
use App\Http\Controllers\Backend\DosageForm\DosageFormController;
use App\Http\Controllers\Backend\Due\DepoDueCollectionController;
use App\Http\Controllers\Backend\Due\SupplierPaymentController;
use App\Http\Controllers\Backend\Employee\EmployeeController;
use App\Http\Controllers\Backend\GenericName\GenericNameController;
use App\Http\Controllers\Backend\HeadOfficeDistribute\HeadOfficeDistributeController;
use App\Http\Controllers\Backend\HeadOfficeDistribute\TempDistributeController;
use App\Http\Controllers\Backend\HeadOfficePurchase\HeadOfficePurchaseController;
use App\Http\Controllers\Backend\Investor\InvestorController;
use App\Http\Controllers\Backend\Investor\InvestorInvestController;
use App\Http\Controllers\Backend\Investor\InvestorWithdrawController;
use App\Http\Controllers\Backend\JobApplication\ApplicationController;
use App\Http\Controllers\Backend\MarketingTeam\AsmController;
use App\Http\Controllers\Backend\MarketingTeam\DirectorController;
use App\Http\Controllers\Backend\MarketingTeam\MpoController;
use App\Http\Controllers\Backend\MarketingTeam\NsmController;
use App\Http\Controllers\Backend\MarketingTeam\RsmController;
use App\Http\Controllers\Backend\MarketingTeam\SmController;
use App\Http\Controllers\Backend\Medicine\MedicineController;
use App\Http\Controllers\Backend\Medicine\MedicineListController;
use App\Http\Controllers\Backend\MedicineCategory\MedicineCategoryController;
use App\Http\Controllers\Backend\Party\PartyController;
use App\Http\Controllers\Backend\Report\CashFlowController;
use App\Http\Controllers\Backend\Report\ChemistHouseLedgerController;
use App\Http\Controllers\Backend\Report\DepoReportController;
use App\Http\Controllers\Backend\Report\InvestorLedgerController;
use App\Http\Controllers\Backend\Report\SupplierLedgerController;
use App\Http\Controllers\Backend\Requisition\RequisitionController;
use App\Http\Controllers\Backend\Strength\StrengthController;
use App\Http\Controllers\Backend\VatSetting\VatSettingController;
use App\Http\Controllers\Backend\Supplier\SupplierController;
use App\Http\Controllers\Backend\GlAccount\GlAccountController;
use App\Http\Controllers\Backend\DebitVoucher\DebitVoucherController;
use Illuminate\Support\Facades\Route;


Route::middleware(['super_admin:admin'])->group(function () {

Route::group(['namespace' => 'Admin'], function () {
    Route::get('/medicine-purchase',[AdminPurchaseController::class,'index'])->name('admin.medicine.purchase.index');
    Route::match(['get','post'],'/medicine-purchase/create',[AdminPurchaseController::class,'create'])->name('admin.medicine.purchase.create');
    Route::get('/medicine-purchase/show/{id}',[AdminPurchaseController::class,'show'])->name('admin.medicine.purchase.show');
    Route::put('/medicine-purchase/update/{id}',[AdminPurchaseController::class,'update'])->name('admin.medicine.purchase.update');
    Route::delete('/medicine-purchase/delete/{id}',[AdminPurchaseController::class,'destroy'])->name('admin.medicine.purchase.delete');
    Route::get('/medicine-purchase/print/{id}',[AdminPurchaseController::class,'print'])->name('admin.medicine.purchase.print');


    Route::get('/medicine-getPurchaseData',[AdminPurchaseController::class,'getPurchaseData'])->name('admin.medicine.purchase.getPurchaseData');
    Route::get('/medicine-search-medicine',[AdminPurchaseController::class,'searchMedicine'])->name('admin.medicine.purchase.searchMedicine');
    Route::get('/medicine-getSuppliers',[AdminPurchaseController::class,'getSuppliers'])->name('admin.medicine.purchase.getSuppliers');
});

});



Route::middleware(['super_admin:admin','block_purchase_admin'])->group(function () {

    // Purchase Admin Create
    Route::group(['namespace' => 'CompanySetting'], function () {
        Route::get('/purchase-admin',[PurchaseAdminController::class,'index'])->name('purchase.admin.index');
        Route::match(['get','post'],'/purchase-admin/create',[PurchaseAdminController::class,'create'])->name('purchase.admin.create');
        Route::get('/purchase-admin/show/{id}',[PurchaseAdminController::class,'show'])->name('purchase.admin.show');
        Route::get('/purchase-admin/edit/{id}',[PurchaseAdminController::class,'edit'])->name('purchase.admin.edit');
        Route::put('/purchase-admin/update/{id}',[PurchaseAdminController::class,'update'])->name('purchase.admin.update');
    });

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

    // Debit Voucher Route
    Route::group(['namespace'=>'DebitVoucher'],function(){
        Route::get('/debit-voucher',[DebitVoucherController::class,'index'])->name('admin.debit-voucher.index');
        Route::match(['get','post'],'/debit-voucher/create',[DebitVoucherController::class,'create'])->name('admin.debit-voucher.create');
        Route::get('/debit-voucher/show/{id}',[DebitVoucherController::class,'show'])->name('admin.debit-voucher.show');
        Route::put('/debit-voucher/update/{id}',[DebitVoucherController::class,'update'])->name('admin.debit-voucher.update');
        Route::delete('/debit-voucher/delete/{id}',[DebitVoucherController::class,'destroy'])->name('admin.debit-voucher.delete');
        Route::get('/debit-voucher/print/{id}', [DebitVoucherController::class, 'print'])->name('admin.debit-voucher.print');

        // AJAX routes
        Route::get('/debit-voucher/get-parties', [DebitVoucherController::class, 'getParties'])->name('admin.debit-voucher.get-parties');
        Route::get('/debit-voucher/get-bank-accounts', [DebitVoucherController::class, 'getBankAccounts'])->name('admin.debit-voucher.get-bank-accounts');
        Route::get('/debit-voucher/get-expense-coa', [DebitVoucherController::class, 'getExpenseCoa'])->name('admin.debit-voucher.get-expense-coa');
    });

    // Credit Voucher
    Route::group(['namespace' => 'CreditVoucher'], function () {
        Route::get('/credit-voucher', [CreditVoucherController::class, 'index'])->name('admin.credit-voucher.index');
        Route::match(['get', 'post'], '/credit-voucher/create', [CreditVoucherController::class, 'create'])->name('admin.credit-voucher.create');
        Route::get('/credit-voucher/show/{id}', [CreditVoucherController::class, 'show'])->name('admin.credit-voucher.show');
        Route::put('/credit-voucher/update/{id}', [CreditVoucherController::class, 'update'])->name('admin.credit-voucher.update');
        Route::get('/credit-voucher/print/{id}', [CreditVoucherController::class, 'print'])->name('admin.credit-voucher.print');

        // AJAX routes
        Route::get('/credit-voucher/get-parties', [CreditVoucherController::class, 'getParties'])->name('admin.credit-voucher.get-parties');
        Route::get('/credit-voucher/get-bank-accounts', [CreditVoucherController::class, 'getBankAccounts'])->name('admin.credit-voucher.get-bank-accounts');
        Route::get('/credit-voucher/get-income-coa', [CreditVoucherController::class, 'getIncomeCoa'])->name('admin.credit-voucher.get-income-coa');
    });

// Depo Route
    Route::group(['namespace' => 'Depo'], function () {
        Route::get('/depo', [DepoController::class, 'index'])->name('depo.index');
        Route::match(['get', 'post'], '/depo/create', [DepoController::class, 'create'])->name('depo.create');
        Route::get('/depo/show/{id}', [DepoController::class, 'show'])->name('depo.show');
        Route::get('/depo/edit/{id}', [DepoController::class, 'edit'])->name('depo.edit');
        Route::put('/depo/update/{id}', [DepoController::class, 'update'])->name('depo.update');
        Route::delete('/depo/delete/{id}', [DepoController::class, 'destroy'])->name('depo.destroy');
    });

// GenericName Route
    Route::group(['namespace' => 'GenericName'], function () {
        Route::get('/generic', [GenericNameController::class, 'index'])->name('generic.index');
        Route::match(['get', 'post'], '/generic/create', [GenericNameController::class, 'create'])->name('generic.create');
        Route::get('/generic/show/{id}', [GenericNameController::class, 'show'])->name('generic.show');
        Route::get('/generic/edit/{id}', [GenericNameController::class, 'edit'])->name('generic.edit');
        Route::put('/generic/update/{id}', [GenericNameController::class, 'update'])->name('generic.update');
        Route::delete('/generic/delete/{id}', [GenericNameController::class, 'destroy'])->name('generic.delete');
    });

// Company Route
    Route::group(['namespace'=>'Company'],function(){
        Route::get('/company',[CompanyController::class,'index'])->name('company.index');
        Route::match(['get', 'post'], '/company/create', [CompanyController::class, 'create'])->name('company.create');
        Route::get('/company/edit/{id}', [CompanyController::class, 'edit'])->name('company.edit');
        Route::put('/company/update/{id}', [CompanyController::class, 'update'])->name('company.update');
        Route::delete('/company/delete/{id}', [CompanyController::class, 'destroy'])->name('company.delete');
    });

// Dosage Route
    Route::group(['namespace' => 'DosageForm'], function () {
        Route::get('/dosage', [DosageFormController::class, 'index'])->name('dosage.index');
        Route::match(['get', 'post'], '/dosage/create', [DosageFormController::class, 'create'])->name('dosage.create');
        Route::get('/dosage/show/{id}', [DosageFormController::class, 'show'])->name('dosage.show');
        Route::get('/dosage/edit/{id}', [DosageFormController::class, 'edit'])->name('dosage.edit');
        Route::put('/dosage/update/{id}', [DosageFormController::class, 'update'])->name('dosage.update');
        Route::delete('/dosage/delete/{id}', [DosageFormController::class, 'delete'])->name('dosage.delete');
    });

// Strength Route
    Route::group(['namespace' => 'Strength'], function () {
        Route::get('/strength', [StrengthController::class, 'index'])->name('strength.index');
        Route::match(['get', 'post'], '/strength/create', [StrengthController::class, 'create'])->name('strength.create');
        Route::get('/strength/show/{id}', [StrengthController::class, 'show'])->name('strength.show');
        Route::get('/strength/edit/{id}', [StrengthController::class, 'edit'])->name('strength.edit');
        Route::put('/strength/update/{id}', [StrengthController::class, 'update'])->name('strength.update');
        Route::delete('/strength/delete/{id}', [StrengthController::class, 'delete'])->name('strength.delete');
    });

// Category Route
    Route::group(['namespace' => 'MedicineCategory'], function () {
        Route::get('/category', [MedicineCategoryController::class, 'index'])->name('category.index');
        Route::match(['get', 'post'], '/category/create', [MedicineCategoryController::class, 'create'])->name('category.create');
        Route::get('/category/show/{id}', [MedicineCategoryController::class, 'show'])->name('category.show');
        Route::get('/category/edit/{id}', [MedicineCategoryController::class, 'edit'])->name('category.edit');
        Route::put('/category/update/{id}', [MedicineCategoryController::class, 'update'])->name('category.update');
        Route::delete('/category/delete/{id}', [MedicineCategoryController::class, 'delete'])->name('category.delete');
    });

// Brand Route
    Route::group(['namespace' => 'Brand'], function () {
        Route::get('/brand', [BrandController::class, 'index'])->name('brand.index');
        Route::match(['get', 'post'], '/brand/create', [BrandController::class, 'create'])->name('brand.create');
        Route::get('/brand/show/{id}', [BrandController::class, 'show'])->name('brand.show');
        Route::get('/brand/edit/{id}', [BrandController::class, 'edit'])->name('brand.edit');
        Route::put('/brand/update/{id}', [BrandController::class, 'update'])->name('brand.update');
        Route::delete('/brand/delete/{id}', [BrandController::class, 'destroy'])->name('brand.delete');
    });

// Supplier Route
    Route::group(['namespace'=>'Supplier'],function(){
        Route::get('/supplier',[SupplierController::class,'index'])->name('supplier.index');
        Route::match(['get','post'],'/supplier/create',[SupplierController::class,'create'])->name('supplier.create');
        Route::get('/supplier/show/{id}',[SupplierController::class,'show'])->name('supplier.show');
        Route::get('/supplier/edit/{id}',[SupplierController::class,'edit'])->name('supplier.edit');
        Route::get('/supplier/show/{id}',[SupplierController::class, 'show'])->name('supplier.show');
        Route::put('/supplier/update/{id}',[SupplierController::class,'update'])->name('supplier.update');
        Route::delete('/supplier/delete/{id}',[SupplierController::class,'destroy'])->name('supplier.delete');
    });

// Investor Route
    Route::group(['namespace'=>'Investor'],function(){
        Route::get('/investor',[InvestorController::class,'index'])->name('admin.investor.index');
        Route::match(['get','post'],'/investor/create',[InvestorController::class,'create'])->name('admin.investor.create');
        Route::get('/investor/show/{id}',[InvestorController::class,'show'])->name('admin.investor.show');
        Route::get('/Investor/edit/{id}',[InvestorController::class,'edit'])->name('admin.investor.edit');
        Route::put('/investor/update/{id}',[InvestorController::class,'update'])->name('admin.investor.update');

      // Investor Invest Route
       Route::get('/investor/invest',[InvestorInvestController::class,'index'])->name('admin.investor.invest.index');
       Route::match(['get','post'],'/investor/invest/create',[InvestorInvestController::class,'create'])->name('admin.investor.invest.create');
       Route::get('/investor/invest/show/{id}',[InvestorInvestController::class,'show'])->name('admin.investor.invest.show');
       Route::get('/investor/invest/print/{id}',[InvestorInvestController::class,'print'])->name('admin.investor.invest.print');
       Route::get('/get-investor-data',[InvestorInvestController::class,'getInvestorData'])->name('admin.get.investor.data');

       // Investor Withdraw Route
        Route::get('/investor/withdraw',[InvestorWithdrawController::class,'index'])->name('admin.investor.withdraw.index');
        Route::match(['get','post'],'/investor/withdraw/create',[InvestorWithdrawController::class,'create'])->name('admin.investor.withdraw.create');
        Route::get('/investor/withdraw/show/{id}',[InvestorWithdrawController::class,'show'])->name('admin.investor.withdraw.show');
        Route::get('/investor/withdraw/print/{id}',[InvestorWithdrawController::class,'print'])->name('admin.investor.withdraw.print');
        Route::get('/get-investor-data',[InvestorWithdrawController::class,'getInvestorData'])->name('admin.get.investor.data');
    });

// Medicine Route
    Route::group(['namespace' => 'Medicine'], function () {
        Route::get('/medicine', [MedicineController::class, 'index'])->name('medicine.index');
        Route::match(['get', 'post'], '/medicine/create', [MedicineController::class, 'create'])->name('medicine.create');
        Route::get('/medicine/show/{id}', [MedicineController::class, 'show'])->name('medicine.show');
        Route::get('/medicine/edit/{id}', [MedicineController::class, 'edit'])->name('medicine.edit');
        Route::put('/medicine/update/{id}', [MedicineController::class, 'update'])->name('medicine.update');
        Route::delete('/medicine/delete/{id}', [MedicineController::class, 'destroy'])->name('medicine.delete');
        Route::get('/get-strengths/{id}', [MedicineController::class, 'getStrength'])->name('get.strengths');

// Medicine Expire Route
        Route::get('/medicine-list', [MedicineListController::class, 'index'])->name('medicine.list');
    });

//  Depo Route
    Route::group(['namespace'=>'Depo'],function(){
        Route::get('/depo', [DepoController::class, 'index'])->name('depo.index');
        Route::match(['get','post'],'/depo/create', [DepoController::class, 'create'])->name('depo.create');
        Route::get('/depo/show/{id}', [DepoController::class, 'show'])->name('depo.show');
        Route::get('/depo/edit/{id}', [DepoController::class, 'edit'])->name('depo.edit');
        Route::put('/depo/update/{id}', [DepoController::class, 'update'])->name('depo.update');
        Route::get('/depo/access/{id}', [DepoController::class, 'depoAccess'])->name('depo.access');
        Route::delete('/depo/delete/{id}', [DepoController::class, 'destroy'])->name('depo.delete');
    });

//  Vat Route
    Route::group(['namespace'=>'VatSetting'],function(){
        Route::get('/vat',[VatSettingController::class,'index'])->name('vat.index');
        Route::match(['get','post'],'/vat/create',[VatSettingController::class,'create'])->name('vat.create');
        Route::get('/vat/show/{id}',[VatSettingController::class,'show'])->name('vat.show');
        Route::get('/vat/edit/{id}',[VatSettingController::class,'edit'])->name('vat.edit');
        Route::put('/vat/update/{id}',[VatSettingController::class,'update'])->name('vat.update');
        Route::delete('/vat/delete/{id}',[VatSettingController::class,'destroy'])->name('vat.delete');
    });

//  Chemist Route
    Route::group(['namespace'=>'Chemist'],function(){
        Route::get('/chemist-house',[ChemistHouseController::class,'index'])->name('chemist.house.index');
        Route::match(['get','post'],'/chemist-house/create',[ChemistHouseController::class,'create'])->name('chemist.house.create');
        Route::get('/chemist-house/edit/{id}',[ChemistHouseController::class,'edit'])->name('chemist.house.edit');
        Route::get('/chemist-house/show/{id}',[ChemistHouseController::class,'edit'])->name('chemist.house.show');
        Route::put('/chemist-house/update/{id}',[ChemistHouseController::class,'update'])->name('chemist.house.update');
        Route::delete('/chemist-house/delete/{id}',[ChemistHouseController::class,'destroy'])->name('chemist.house.delete');
        Route::post('/chemist-house/getMpo',[ChemistHouseController::class,'getMpo'])->name('chemist.house.getMpo');

        //  Default Chemist House

    });



//   Employee Route
    Route::group(['namespace'=>'Employee'],function(){
        Route::get('/employee',[EmployeeController::class,'index'])->name('employee.index');
        Route::match(['get','post'],'/employee/create',[EmployeeController::class,'create'])->name('employee.create');
        Route::get('/employee/show/{id}',[EmployeeController::class,'show'])->name('employee.show');
        Route::get('/employee/edit/{id}',[EmployeeController::class,'edit'])->name('employee.edit');
        Route::put('/employee/update/{id}',[EmployeeController::class,'update'])->name('employee.update');
        Route::delete('/employee/delete/{id}',[EmployeeController::class,'destroy'])->name('employee.delete');
        Route::get('/get-parent-employee', [EmployeeController::class, 'getParentEmployee'])->name('get.parent.employee');
    });

//    Division Route
    Route::group(['namespace'=>'Division'],function(){
        Route::get('/division',[DivisionController::class,'index'])->name('division.index');
        Route::match(['get','post'],'/division/create',[DivisionController::class,'create'])->name('division.create');
        Route::get('/division/show/{id}',[DivisionController::class,'show'])->name('division.show');
        Route::get('/division/edit/{id}',[DivisionController::class,'edit'])->name('division.edit');
        Route::put('/division/update/{id}',[DivisionController::class,'update'])->name('division.update');
        Route::delete('/division/delete/{id}',[DivisionController::class,'destroy'])->name('division.delete');
    });

//    District Route
    Route::group(['namespace'=>'District'],function(){
        Route::get('/district',[DistrictController::class,'index'])->name('district.index');
        Route::match(['get','post'],'/district/create',[DistrictController::class,'create'])->name('district.create');
        Route::get('/district/show/{id}',[DistrictController::class,'show'])->name('district.show');
        Route::get('/district/edit/{id}',[DistrictController::class,'edit'])->name('district.edit');
        Route::put('/district/update/{id}',[DistrictController::class,'update'])->name('district.update');
        Route::delete('/district/delete/{id}',[DistrictController::class,'destroy'])->name('district.delete');
        Route::get('/get-district/{divisionId}', [DistrictController::class, 'getDistrict'])->name('get.district');
    });

//   Director Route
    Route::group(['namespace'=>'MarketingTeam'],function(){
        Route::get('/director',[DirectorController::class,'index'])->name('director.index');
        Route::match(['get','post'],'/director/create',[DirectorController::class,'create'])->name('director.create');
        Route::get('/director/show/{id}',[DirectorController::class,'show'])->name('director.show');
        Route::get('/director/edit/{id}',[DirectorController::class,'edit'])->name('director.edit');
        Route::put('/director/update/{id}',[DirectorController::class,'update'])->name('director.update');
        Route::delete('/director/delete/{id}',[DirectorController::class,'destroy'])->name('director.delete');
        Route::get('/get-parent-employee', [DirectorController::class, 'getParentEmployee'])->name('get.parent.employee');
        Route::get('/director/access/{id}', [DirectorController::class, 'directorAccess'])->name('director.access');

    });

    //   NSM Route
    Route::group(['namespace'=>'MarketingTeam'],function(){
        Route::get('/nsm',[NsmController::class,'index'])->name('nsm.index');
        Route::match(['get','post'],'/nsm/create',[NsmController::class,'create'])->name('nsm.create');
        Route::get('/nsm/show/{id}',[NsmController::class,'show'])->name('nsm.show');
        Route::get('/nsm/edit/{id}',[NsmController::class,'edit'])->name('nsm.edit');
        Route::put('/nsm/update/{id}',[NsmController::class,'update'])->name('nsm.update');
        Route::delete('/nsm/delete/{id}',[NsmController::class,'destroy'])->name('nsm.delete');
        Route::get('/get-parent-employee', [NsmController::class, 'getParentEmployee'])->name('get.parent.employee');

        Route::get('/nsm/access/{id}', [NsmController::class, 'nsmAccess'])->name('nsm.access');
    });

    //   RSM Route
    Route::group(['namespace'=>'MarketingTeam'],function(){
        Route::get('/rsm',[RsmController::class,'index'])->name('rsm.index');
        Route::match(['get','post'],'/rsm/create',[RsmController::class,'create'])->name('rsm.create');
        Route::get('/rsm/show/{id}',[RsmController::class,'show'])->name('rsm.show');
        Route::get('/rsm/edit/{id}',[RsmController::class,'edit'])->name('rsm.edit');
        Route::put('/rsm/update/{id}',[RsmController::class,'update'])->name('rsm.update');
        Route::delete('/rsm/delete/{id}',[RsmController::class,'destroy'])->name('rsm.delete');
        Route::get('/get-parent-employee', [RsmController::class, 'getParentEmployee'])->name('get.parent.employee');

        Route::get('/rsm/access/{id}', [RsmController::class, 'rsmAccess'])->name('rsm.access');
    });


    //   SM Route
    Route::group(['namespace'=>'MarketingTeam'],function(){
        Route::get('/sm',[SmController::class,'index'])->name('sm.index');
        Route::match(['get','post'],'/sm/create',[SmController::class,'create'])->name('sm.create');
        Route::get('/sm/show/{id}',[SmController::class,'show'])->name('sm.show');
        Route::get('/sm/edit/{id}',[SmController::class,'edit'])->name('sm.edit');
        Route::put('/sm/update/{id}',[SmController::class,'update'])->name('sm.update');
        Route::delete('/sm/delete/{id}',[SmController::class,'destroy'])->name('sm.delete');
        Route::get('/get-parent-employee', [SmController::class, 'getParentEmployee'])->name('get.parent.employee');

        Route::get('/sm/access/{id}', [SmController::class, 'smAccess'])->name('sm.access');
    });

    //   ASM Route
    Route::group(['namespace'=>'MarketingTeam'],function(){
        Route::get('/asm',[ASmController::class,'index'])->name('asm.index');
        Route::match(['get','post'],'/asm/create',[ASmController::class,'create'])->name('asm.create');
        Route::get('/asm/show/{id}',[ASmController::class,'show'])->name('asm.show');
        Route::get('/asm/edit/{id}',[ASmController::class,'edit'])->name('asm.edit');
        Route::put('/asm/update/{id}',[ASmController::class,'update'])->name('asm.update');
        Route::delete('/asm/delete/{id}',[ASmController::class,'destroy'])->name('asm.delete');
        Route::get('/get-parent-employee', [ASmController::class, 'getParentEmployee'])->name('get.parent.employee');
    });

    //   MPO Route
    Route::group(['namespace'=>'MarketingTeam'],function(){
        Route::get('/mpo',[MpoController::class,'index'])->name('mpo.index');
        Route::match(['get','post'],'/mpo/create',[MpoController::class,'create'])->name('mpo.create');
        Route::get('/mpo/edit/{id}',[MpoController::class,'edit'])->name('mpo.edit');
        Route::put('/mpo/update/{id}',[MpoController::class, 'update'])->name('mpo.update');
        // Assign Depo Route

        Route::get('/mpo/assign-depo/{id}',[MpoController::class, 'assignDepo'])->name('mpo.assign.depo');
        Route::put('/mpo/add-depo/{id}', [MpoController::class, 'addDepo'])->name('mpo.add.depo');
        Route::get('/mpo/access/{id}', [MpoController::class, 'mpoAccess'])->name('mpo.access');
    });

    // Purchase Route
    Route::group(['namespace' => 'HeadOfficePurchase'], function () {
        Route::get('/purchase',[HeadOfficePurchaseController::class,'index'])->name('admin.purchase.index');
        Route::match(['get','post'],'/purchase/create',[HeadOfficePurchaseController::class,'create'])->name('admin.purchase.create');
        Route::get('/purchase/show/{id}',[HeadOfficePurchaseController::class,'show'])->name('admin.purchase.show');
        Route::put('/purchase/update/{id}',[HeadOfficePurchaseController::class,'update'])->name('admin.purchase.update');
        Route::delete('/purchase/delete/{id}',[HeadOfficePurchaseController::class,'destroy'])->name('admin.purchase.delete');
        Route::get('/purchase/print/{id}',[HeadOfficePurchaseController::class,'print'])->name('admin.purchase.print');
        Route::get('/sub-admin-medicine-purchase',[HeadOfficePurchaseController::class,'subAdminPurchase'])->name('admin.subAdmin.medicine.purchase');


        Route::get('/getPurchaseData',[HeadOfficePurchaseController::class,'getPurchaseData'])->name('admin.purchase.getPurchaseData');
        Route::get('/search-medicine',[HeadOfficePurchaseController::class,'searchMedicine'])->name('admin.purchase.searchMedicine');
        Route::get('/getSuppliers',[HeadOfficePurchaseController::class,'getSuppliers'])->name('admin.purchase.getSuppliers');
    });

    // Distribute Route
    Route::group(['namespace' => 'HeadOfficeDistribute'], function () {
        Route::get('/distribute', [HeadOfficeDistributeController::class, 'index'])->name('admin.distribute.index');
        Route::match(['get','post'],'/distribute/create',[HeadOfficeDistributeController::class,'create'])->name('admin.distribute.create');
        Route::get('/distribute/show/{id}',[HeadOfficeDistributeController::class,'show'])->name('admin.distribute.show');
        Route::get('/distribute/print/{id}',[HeadOfficeDistributeController::class,'print'])->name('admin.distribute.print');
        Route::get('/distribute/pos-print/{id}',[HeadOfficeDistributeController::class,'posPrint'])->name('admin.distribute.pos.print');
        Route::get('/getDistributeData',[HeadOfficeDistributeController::class,'getDistributeData'])->name('admin.distribute.getDistributeData');
        Route::get('/distribute-search-medicine',[HeadOfficeDistributeController::class,'searchMedicine'])->name('admin.distribute.searchMedicine');

    // Temp Distribute
        Route::get('/temp-distribute', [TempDistributeController::class, 'index'])->name('admin.temp-distribute.index');
        Route::match(['get','post'],'/temp-distribute/create',[TempDistributeController::class,'create'])->name('admin.temp-distribute.create');
        Route::get('/temp-distribute/show/{id}',[TempDistributeController::class,'show'])->name('admin.temp-distribute.show');
        Route::get('/temp-distribute/edit/{id}',[TempDistributeController::class,'edit'])->name('admin.temp-distribute.edit');
        Route::put('/temp-distribute/update/{id}',[TempDistributeController::class,'update'])->name('admin.temp-distribute.update');
        Route::get('/temp-distribute/print/{id}',[TempDistributeController::class,'print'])->name('admin.temp-distribute.print');
        Route::get('/temp-distribute/pos-print/{id}',[TempDistributeController::class,'posPrint'])->name('admin.temp-distribute.pos.print');


    });

    // Stock Route
    Route::group(['namespace'=>'Stock'],function(){
        Route::get('/stock','StockController@index')->name('admin.stock.index');
    });

    // Due Payment And Collection Route
    Route::group(['namespace'=>'Due'],function(){
        // Supplier Due Payment Route
        Route::get('/supplier-due-payment',[SupplierPaymentController::class,'index'])->name('admin.supplier-due-payment.index');
        Route::match(['get','post'],'/supplier-due-payment/create',[SupplierPaymentController::class,'create'])->name('admin.supplier-due-payment.create');
        Route::get('/supplier-due-payment/show/{id}',[SupplierPaymentController::class,'show'])->name('admin.supplier-due-payment.show');
        Route::get('/supplier-due-payment/print/{id}',[SupplierPaymentController::class,'print'])->name('admin.supplier-due-payment.print');
        Route::get('/getSupplierData',[SupplierPaymentController::class,'getSupplierData'])->name('admin.supplier.getSupplierData');

        // Depo Due Collection Route
        Route::get('/depo-due-collection', [DepoDueCollectionController::class, 'index'])->name('admin.depo-due-collection.index');
        Route::match(['get','post'],'/depo-due-collection/create',[DepoDueCollectionController::class, 'create'])->name('admin.depo-due-collection.create');
        Route::get('/depo-due-collection/show/{id}', [DepoDueCollectionController::class, 'show'])->name('admin.depo-due-collection.show');
        Route::get('/depo-due-collection/edit/{id}', [DepoDueCollectionController::class, 'edit'])->name('admin.depo-due-collection.edit');
        Route::put('/depo-due-collection/update/{id}', [DepoDueCollectionController::class, 'update'])->name('admin.depo-due-collection.update');
        Route::get('/depo-due-collection/print/{id}', [DepoDueCollectionController::class, 'print'])->name('admin.depo-due-collection.print');
        Route::get('/getDueDepoData', [DepoDueCollectionController::class, 'getDepoDueData'])->name('admin.depo.getDepoData');
    });


   // Requisition Route
    Route::group(['namespace'=>'Requisition'],function(){
        Route::get('/requisition',[RequisitionController::class,'index'])->name('admin.requisition.index');
        Route::match(['get','post'],'/requisition/create',[RequisitionController::class,'create'])->name('admin.requisition.create');
        Route::get('/requisition/show/{id}', [RequisitionController::class, 'show'])->name('admin.requisition.show');
        Route::get('/requisition/print/{id}', [RequisitionController::class, 'print'])->name('admin.requisition.print');
        Route::get('/requisition/getCompany', [RequisitionController::class, 'getCompanyAjax'])->name('admin.requisition.getCompanyAjax');
        Route::get('/requisition/getCategory', [RequisitionController::class, 'getCategoryAjax'])->name('admin.requisition.getCategoryAjax');
        Route::get('/requisition/getMedicine', [RequisitionController::class, 'getMedicine'])->name('admin.requisition.getMedicine');

    });

    // Application Form
    Route::group(['namespace'=>'JobApplication'],function(){
        Route::get('/job-application',[ApplicationController::class,'index'])->name('admin.job.application.index');
        Route::match(['get','post'],'/job-application/create',[ApplicationController::class,'create'])->name('admin.job.application.create');
        Route::get('/job-application/show/{id}',[ApplicationController::class,'show'])->name('admin.job.application.show');
        Route::get('/job-application/edit/{id}',[ApplicationController::class,'edit'])->name('admin.job.application.edit');
        Route::put('/job-application/update/{id}',[ApplicationController::class,'update'])->name('admin.job.application.update');
        Route::delete('/job-application/delete/{id}',[ApplicationController::class,'destroy'])->name('admin.job.application.delete');
    });



    // Report Route
    Route::group(['namespace'=>'Report'],function(){
        Route::get('/report/depo-report',[DepoReportController::class, 'index'])->name('report.depo.report');

        // Supplier Ledger
        Route::get('/report/suppliers', [SupplierLedgerController::class, 'getSuppliers'])->name('report.suppliers.ajax');
        Route::get('/supplier-ledger',[SupplierLedgerController::class, 'index'])->name('report.supplier.ledger');

        // Investor Ledger
        Route::get('/report/investors', [InvestorLedgerController::class, 'getInvestors'])->name('report.investors.ajax');
        Route::get('/investor-ledger',[InvestorLedgerController::class, 'index'])->name('report.investor.ledger');

       // Depo Profit Report
        Route::get('/report/depo-profit', [DepoReportController::class, 'profit'])->name('report.depo.profit');
        Route::get('/report/depo-profit/show/{id}', [DepoReportController::class, 'showProfit'])->name('report.depo.show.profit');
        Route::get('/report/depo-profit/print/{id}', [DepoReportController::class, 'printProfit'])->name('report.depo.print.profit');
        Route::get('/report/depo-monthly-profit',[DepoReportController::class, 'monthlyProfit'])->name('report.depo.monthly.profit');

        Route::get('/report/depos', [DepoReportController::class, 'getDepos'])->name('report.depos.ajax');
        Route::get('/depo-ledger',[DepoReportController::class, 'depoLedger'])->name('report.depo.ledger');

        // Chemist House Ledger Report
        Route::get('/report/chemist-houses', [ChemistHouseLedgerController::class, 'getChemistHouses'])->name('report.chemist.houses.ajax');
        Route::get('/report/chemist-house-ledger-report',[ChemistHouseLedgerController::class,'index'])->name('report.chemist-house-ledger-report.index');

        // Company Cash Flow Route
        Route::get('/report/company-accounts',[CashFlowController::class,'getCompanyAccounts'])->name('report.company.accounts.ajax');
        Route::get('/report/company-cash-flow-report',[CashFlowController::class,'companyCashFlow'])->name('report.company.cash-flow');

        // Depo Cash Flow Route
        Route::get('/report/depos',[CashFlowController::class,'getDepos'])->name('report.depos.ajax');
        Route::get('/report/depo-accounts',[CashFlowController::class,'getDepoAccounts'])->name('report.depo.accounts.ajax');
        Route::get('/report/depo-cash-flow-report',[CashFlowController::class,'depoCashFlow'])->name('report.depo.cash-flow');
    });



});






