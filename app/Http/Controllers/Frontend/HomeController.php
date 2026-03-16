<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Account;
use App\Models\ChartOfAccount;
use App\Models\ChemistHouse;
use App\Models\CompanySetting;
use App\Models\CreditVoucher;
use App\Models\CreditVoucherItem;
use App\Models\DebitVoucher;
use App\Models\DebitVoucherItem;
use App\Models\Depo;
use App\Models\Distribute;
use App\Models\DistributeItem;
use App\Models\Employee;
use App\Models\GlAccount;
use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\TempDistributeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function adminDashboard()
    {
        $employees=Employee::count();
        $depos=Depo::count();
        $suppliers=Supplier::count();
        $nsms=Employee::where('employee_type','nsm')->count();
        $rsms=Employee::where('employee_type','rsm')->count();
        $sms=Employee::where('employee_type','sm')->count();
        $asms=Employee::where('employee_type','asm')->count();
        $mpos=Employee::where('employee_type','mpo')->count();

       // Total Purchase Value
        $totalPurchases = round(
            Purchase::get()->sum(function($purchase) {
                $vatAmount = $purchase->total * ($purchase->vat / 100);
                return $purchase->total - $purchase->discount + $vatAmount;
            }), 2
        );

       // Total Purchase Stock Value Start
        $totalPurchaseStockValue = round(
            Medicine::with('purchaseItems')->get()->sum(function($medicine) {
                $totalQuantity = $medicine->purchaseItems->sum('quantity') + $medicine->purchaseItems->sum('free_quantity');
                return $totalQuantity * $medicine->purchase_price;
            }), 2
        );
        // Total Purchase Stock Value End

        // Total Distribute Stock Value Start
        // Permanent distributed stock
        $totalDistributeStockValue = DistributeItem::join('medicines', 'distribute_items.medicine_id', '=', 'medicines.id')
            ->selectRaw('SUM((distribute_items.quantity + distribute_items.free_quantity) * medicines.purchase_price) as total')
            ->value('total') ?? 0;

        // Temporary distributed stock (only order_status = 1)
        $tempDistributeStockValue = TempDistributeItem::join('medicines', 'temp_distribute_items.medicine_id', '=', 'medicines.id')
            ->join('temp_distributes', 'temp_distribute_items.temp_distribute_id', '=', 'temp_distributes.id')
            ->where('temp_distributes.order_status', 1)
            ->selectRaw('SUM((temp_distribute_items.quantity + temp_distribute_items.free_quantity) * medicines.purchase_price) as total')
            ->value('total') ?? 0;

        $totalDistributeStockValue = round($totalDistributeStockValue + $tempDistributeStockValue, 2);
        // Total Distribute Stock Value End



       // Total Purchase in Stock Value Start
        $totalPurchaseInStockValue = round($totalPurchaseStockValue-$totalDistributeStockValue, 2);
        // Total Purchase in Stock Value End

       // Total Sale in Stock Value Start
        $totalSaleStockValue = round(
            Medicine::with('purchaseItems')->get()->sum(function($medicine) {
                $totalQuantity = $medicine->purchaseItems->sum('quantity') + $medicine->purchaseItems->sum('free_quantity');
                return $totalQuantity * $medicine->sale_price;
            }), 2
        );

        $totalDistributeSaleStockValue = DistributeItem::join('medicines', 'distribute_items.medicine_id', '=', 'medicines.id')
            ->selectRaw('SUM((distribute_items.quantity + distribute_items.free_quantity) * medicines.sale_price) as total')
            ->value('total') ?? 0;


        $tempDistributeSaleStockValue = TempDistributeItem::join('medicines', 'temp_distribute_items.medicine_id', '=', 'medicines.id')
            ->join('temp_distributes', 'temp_distribute_items.temp_distribute_id', '=', 'temp_distributes.id')
            ->where('temp_distributes.order_status', 1)
            ->selectRaw('SUM((temp_distribute_items.quantity + temp_distribute_items.free_quantity) * medicines.sale_price) as total')
            ->value('total') ?? 0;


        $totalSaleInStockValue=round($totalSaleStockValue-($totalDistributeSaleStockValue+$tempDistributeSaleStockValue), 2);


        // Total Sale in Stock Value End




        // Total Purchase in Stock Value End

       // Total Sale
        $totalSale=Sale::sum('final_total');

       // Total distribute Value
        $totalDistributeValue = round(
            Distribute::where('order_status', 3)
            ->get()
                ->sum(function ($distribute) {

                    $total = $distribute->total ?? 0;
                    $discount = $distribute->discount ?? 0;
                    $vatPercent = $distribute->vat ?? 0;

                    $vatAmount = ($total - $discount) * ($vatPercent / 100);

                    return ($total - $discount + $vatAmount);
                }),
            2
        );




       // Total Distribute Value End

        // Today Purchase
        $startOfDay = Carbon::today();
        $endOfDay   = Carbon::today()->endOfDay();

        $todayPurchase = round(
            Purchase::whereDate('purchase_date', Carbon::today())
                ->orWhereDate('created_at', Carbon::today())
                ->get()
                ->sum(function ($purchase) {
                    $total = $purchase->total ?? 0;
                    $discount = $purchase->discount ?? 0;
                    $vatPercent = $purchase->vat ?? 0;
                    $vatAmount = ($total - $discount) * ($vatPercent / 100);
                    return ($total - $discount + $vatAmount);
                })
        );


        $today = Carbon::today()->toDateString();

        // Today Distribute
        $todayDistribute = round(
            Distribute::where('order_status', 3)
                ->whereDate('created_at', $today)
                ->get()
                ->sum(function ($distribute) {
                    $total = $distribute->total ?? 0;
                    $discount = $distribute->discount ?? 0;
                    $vatPercent = $distribute->vat ?? 0;
                    $vatAmount = ($total - $discount) * ($vatPercent / 100);
                    return ($total - $discount + $vatAmount);
                }),
            2
        );



        // Total Expense
        $userId = Auth::id();
        $totalExpense = DebitVoucher::where('user_id', $userId)
            ->sum('total_amount');


      // Today Expense
        $todayExpense = round(
            DebitVoucher::where('user_id', $userId)
                ->whereDate('payment_date', $today)
                ->sum('total_amount'),
            2
        );


      // Total Payable Due
        $payableDue=Supplier::where('balance', '>', 0)->sum('balance');

      // Total Receivable Due
        $receivableDue = Supplier::where('balance', '<', 0)->sum('balance');



        return view('admin.extends.dashboard',compact('employees','depos','suppliers','nsms','rsms','sms','asms','mpos','totalPurchases',
            'totalPurchaseStockValue','totalSale','totalDistributeStockValue','totalDistributeValue','todayPurchase','todayDistribute','totalExpense','todayExpense',
        'totalPurchaseInStockValue','totalSaleInStockValue','payableDue','receivableDue'));
    }

    public function depoDashboard(){


        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;


        $fetchDepo=Depo::where('user_id',$userId)->first();

        $totalSale = round(
            Sale::where('depo_id', $fetchDepo->id)->get()->sum(function ($sale) {
                $vatAmount = $sale->total * ($sale->vat / 100);
                return $sale->total - $sale->discount + $vatAmount;
            }),
            2
        );

        $totalSaleToday = round(
            Sale::where('depo_id', $fetchDepo->id)
                ->whereDate('sale_date', Carbon::today())
                ->get()
                ->sum(function ($sale) {
                    $vatAmount = $sale->total * ($sale->vat / 100);
                    return $sale->total - $sale->discount + $vatAmount;
                }),
            2
        );

        $totalChemistHouse=ChemistHouse::where('depo_id', $fetchDepo->id)->where('status',1)->count();

        $currentBalance=Account::where('user_id', $userId)->where('status',1)->get()->sum('balance');

        $totalPurchase = Distribute::with('items')
            ->where('depo_id', $fetchDepo->id)
            ->get()
            ->sum(function($distributes) {
                return $distributes->items->sum('quantity');
            });

        $totalPurchaseFree = Distribute::with('items')
            ->where('depo_id', $fetchDepo->id)
            ->get()
            ->sum(function($distributes) {
                return $distributes->items->sum('free_quantity');
            });

        $totalDebit = DebitVoucherItem::whereHas('voucher', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->sum('paid_amount');

        $totalCredit=CreditVoucherItem::whereHas('creditVoucher', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->sum('paid_amount');

        $totalPurchaseAmount=Distribute::where('depo_id', $fetchDepo->id)->get()->sum('final_total');

        $totalSaleStockValue = round(
            Medicine::with(['saleItems.sale' => function ($q) use ($fetchDepo) {
                $q->where('depo_id', $fetchDepo->id);
            }])->get()->sum(function ($medicine) {
                // Filter only saleItems that belong to the depo
                $saleItems = $medicine->saleItems->filter(function ($item) {
                    return $item->sale && $item->sale->depo_id == $item->sale->depo_id;
                });

                $totalQuantity = $saleItems->sum('quantity') + $saleItems->sum('free_quantity');
                return $totalQuantity * $medicine->sale_price;
            }),
            2
        );

        $todayPurchase = round(
            Distribute::where('depo_id', $fetchDepo->id)
                ->whereDate('created_at', Carbon::today()) // only today
                ->get()
                ->sum(function ($dist) {
                    $vatAmount = $dist->total * ($dist->vat / 100);
                    return $dist->total - $dist->discount + $vatAmount;
                }),
            2
        );


        return view('depo.extends.dashboard',compact('totalChemistHouse', 'currentBalance','totalPurchase','totalPurchaseAmount','totalDebit','totalCredit','totalPurchaseFree','totalSaleStockValue','totalSale','totalSaleToday','todayPurchase'));
    }

    public function mpoDashboard(){

        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        $employee=Employee::where('employee_type','mpo')
            ->where('user_id',$userId)
            ->first();

        if (!$employee) {
            abort(403, 'MPO not found');
        }


        $totalChemistHouse=ChemistHouse::where('depo_id',$employee->depo_id)->where('status',1)->count();
        $totalSales=Sale::where('user_id',$userId)->sum('total');
        $totalSaleQuantity = SaleItem::whereHas('sale', function ($q) use ($employee) {
            $q->where('mpo_id', $employee->id);
        })
            ->sum('quantity');

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // Monthly Sale Start

        $totalMonthlySale = SaleItem::whereHas('sale', function ($q) use ($employee, $startOfMonth, $endOfMonth) {
            $q->where('mpo_id', $employee->id)
                ->whereBetween('sale_date', [$startOfMonth, $endOfMonth]);
        })
            ->selectRaw('SUM(quantity * unit_cost) as total_sale_amount')
            ->value('total_sale_amount');

        // Monthly Sale End

        return view('employee.mpo.extends.dashboard',compact('totalChemistHouse','totalSales','totalSaleQuantity','totalMonthlySale'));
    }

    public function chemistHouseDashboard(){
        return view('chemist_house.extends.dashboard');
    }

    public function asmDashboard(){
        return view('employee.asm.extends.dashboard');
    }

    public function smDashboard(){
        return view('employee.sm.extends.dashboard');
    }

    public function rsmDashboard(){
        return view('employee.rsm.extends.dashboard');
    }

    public function nsmDashboard(){
        return view('employee.nsm.extends.dashboard');
    }

    public function directorDashboard(){
        return view('employee.director.extends.dashboard');
    }


    public function login(Request $request)
    {

        if($request->isMethod('post')) {

            $request->validate([
                'email' => 'required|max:255|email',
                'password' => 'required',
            ]);

            $select_user = DB::table('users')->where('email', $request->email)->first();
            $select_employee = DB::table('employees')->where('email', $request->email)->first();

            if (empty($select_user)) {

                return redirect()->back()->with('error', 'We could not find your email');
            }

            if ($select_user->status == 1) {
                $credentials = $request->only('email', 'password');

                $remember = $request->has('remember');

                if (Auth::attempt($credentials, $remember)) {
                    if ($select_user->user_type == 'admin') {
                        return redirect('/admin/dashboard');
                    } elseif ($select_user->user_type == 'depo') {
                        return redirect('/depo/dashboard');
                    }elseif ($select_user->user_type == 'chemist_house') {
                        return redirect('/chemist-house/dashboard');
                    }

                    //  Director Login
                    elseif($select_user->user_type=='director'){
                        if($select_employee->employee_type=='director'){
                            return redirect('/director/dashboard');
                        }
                        else {
                            return redirect()->back()->with('error', 'Login Failed');
                        }
                    }

                    //  Nsm Login
                    elseif($select_user->user_type=='nsm'){
                        if($select_employee->employee_type=='nsm'){
                            return redirect('/nsm/dashboard');
                        }
                        else {
                            return redirect()->back()->with('error', 'Login Failed');
                        }
                    }

                    //  Rsm Login
                    elseif($select_user->user_type=='rsm'){
                        if($select_employee->employee_type=='rsm'){
                            return redirect('/rsm/dashboard');
                        }
                        else {
                            return redirect()->back()->with('error', 'Login Failed');
                        }
                    }

                    //  Sm Login
                    elseif($select_user->user_type=='sm'){
                        if($select_employee->employee_type=='sm'){
                            return redirect('/sm/dashboard');
                        }
                        else {
                            return redirect()->back()->with('error', 'Login Failed');
                        }
                    }

                    //  Asm Login
                    elseif($select_user->user_type=='asm'){
                        if($select_employee->employee_type=='asm'){
                            return redirect('/asm/dashboard');
                        }
                        else {
                            return redirect()->back()->with('error', 'Login Failed');
                        }
                    }

                    //  Mpo Login
                    elseif($select_user->user_type=='mpo'){
                        if($select_employee->employee_type=='mpo'){
                            return redirect('/mpo/dashboard');
                        }
                        else {
                            return redirect()->back()->with('error', 'Login Failed');
                        }

                    }
                    else{
                        return redirect()->back()->with('error', 'Invalid User');

                    }
                } else {
                    return redirect()->back()->with('error', 'Login Failed');
                }
            } else {
                return redirect()->back()->with('error', 'Your account has some issue. Please contact support');
            }

        }

        if (Auth::check()) {
            $user_type=Auth::user()->user_type;
            if ( $user_type== 'admin') {
                return redirect('/admin/dashboard')->with('success',"Welcome To Admin Panel.");
            } elseif ($user_type == 'depo') {
                return redirect('/depo/dashboard')->with('success',"Welcome To Depo Panel.");
            }
            elseif ($user_type == 'chemist_house') {
                return redirect('/chemist-house/dashboard')->with('success',"Welcome To Chemist House Panel.");
            }
            elseif($user_type=='director'){
                return redirect('/director/dashboard')->with('success',"Welcome To Director Panel.");
            }
            elseif($user_type=='nsm'){
                return redirect('/nsm/dashboard')->with('success',"Welcome To Nsm Panel.");
            }
            elseif($user_type=='rsm'){
                return redirect('/rsm/dashboard')->with('success',"Welcome To Rsm Panel.");
            }
            elseif($user_type=='sm'){
                return redirect('/sm/dashboard')->with('success',"Welcome To Sm Panel.");
            }
            elseif($user_type=='asm'){
                return redirect('/asm/dashboard')->with('success',"Welcome To Asm Panel.");
            }
            elseif($user_type=='mpo'){
                return redirect('/mpo/dashboard')->with('success',"Welcome To Mpo Panel.");
            }

        }

        return view('login');
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->back()->with('success', 'Logged out successfully');
    }


    public function landingPage(){
        $company=CompanySetting::first();
        return view('landing',compact('company'));
    }



}
