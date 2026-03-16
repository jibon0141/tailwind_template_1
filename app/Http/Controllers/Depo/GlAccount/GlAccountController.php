<?php

namespace App\Http\Controllers\Depo\GlAccount;


use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GlAccountController extends controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $accounts = GlAccount::all();

            return DataTables::of($accounts)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('depo.gl-account.edit', $row->id);

                    return '
                    <div class="flex gap-2">
                        <a href="'.$editUrl.'" class="px-2 py-1 bg-blue-500 text-white text-xs rounded">
                            <i class="fa fa-edit"></i>
                        </a>
                    </div>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('depo.extends.gl_account.index');
    }

    public function edit($id)
    {
        $account = GlAccount::findOrFail($id);
        return view('depo.extends.gl_account.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = GlAccount::findOrFail($id);
        
        $data = $request->validate([
            'account_name' => 'required|string|max:255',
        ]);

        $account->update($data);
        
        return redirect()->back()->with('success', 'GL Account updated successfully.');
    }

}
