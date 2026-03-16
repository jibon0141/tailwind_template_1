<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockPurchaseAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Please login first');
        }

        $user = Auth::user();

        if ($user->user_type === 'admin' && $user->role === 'purchase_admin') {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
