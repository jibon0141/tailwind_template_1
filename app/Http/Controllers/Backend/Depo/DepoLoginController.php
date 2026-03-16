<?php

namespace App\Http\Controllers\Backend\Depo;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepoLoginController extends Controller
{
    public function login(Request $request, User $user)
    {
        // Login depo user
        Auth::guard('web')->login($user);

        return redirect()->route('depo.dashboard');
    }

}
