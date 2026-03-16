<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperAdminMiddleware
{

    public function handle(Request $request, Closure $next, ...$allowedTypes)
    {

        if (!Auth::check()) {
            return redirect('/')->with('error', 'Please login first');
        }

        $user = Auth::user();

        if ($user->status != 1) {
            Auth::logout();
            return redirect('/')->with('error', 'Account inactive');
        }

        if (empty($allowedTypes)) {
            abort(403, 'Access rule not defined');
        }

        if (!in_array($user->user_type, $allowedTypes)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
