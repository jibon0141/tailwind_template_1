<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function passwordEdit()
    {
        return view('admin.extends.profile.password');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password'     => 'required',
            'new_password'         => 'required|min:8|confirmed',
        ]);

        $admin = auth()->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        try {
            $admin->password = Hash::make($request->new_password);
            $admin->save();

            Log::info('Admin password updated successfully', ['admin_id' => $admin->id]);

            return redirect()->back()->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Password update failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Password update failed.');
        }
    }
}
