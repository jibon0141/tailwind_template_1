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

        return view('admin.extends.dashboard');
    }


    public function login(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'email' => 'required|max:255|email',
                'password' => 'required',
            ]);

            $user = DB::table('users')->where('email', $request->email)->first();

            if (!$user) {
                return redirect()->back()->with('error', 'We could not find your email');
            }

            if ($user->status != 1) {
                return redirect()->back()->with('error', 'Your account has some issue. Please contact support');
            }

            if ($user->user_type != 'admin') {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            $credentials = $request->only('email', 'password');
            $remember = $request->has('remember');

            if (Auth::attempt($credentials, $remember)) {
                return redirect('/admin/dashboard')->with('success', 'Welcome To Admin Panel.');
            } else {
                return redirect()->back()->with('error', 'Login Failed');
            }
        }

        if (Auth::check() && Auth::user()->user_type == 'admin') {
            return redirect('/admin/dashboard')->with('success', 'Welcome To Admin Panel.');
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
