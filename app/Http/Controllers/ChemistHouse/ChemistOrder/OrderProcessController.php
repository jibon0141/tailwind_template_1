<?php

namespace App\Http\Controllers\ChemistHouse\ChemistOrder;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Medicine;
use Illuminate\Http\Request;

class OrderProcessController extends Controller
{

    public function medicineList(Request $request){
        return view('chemist_house.extends.order.medicine_list');
    }

    public function searchMedicineAjax(Request $request)
    {
        /* ===== Company Search (Select2) ===== */
        if ($request->filled('q')) {

            $term = $request->q;

            $companies = Company::where('company_name', 'LIKE', "%{$term}%")->get();

            return response()->json([
                'results' => $companies->map(fn ($c) => [
                    'id'   => $c->id,
                    'text' => $c->company_name,
                ])
            ]);
        }


        /* ===== Medicine Load ===== */

        $query = Medicine::query();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }


        if ($request->filled('search')) {
            $query->where('medicine_name', 'LIKE', '%' . $request->search . '%');
        }

        // If nothing selected → return empty
        if (!$request->filled('company_id') && !$request->filled('search')) {
            return response()->json([]);
        }

        $medicines = $query->get();


        return response()->json([
            'company_name' => optional($medicines->first()?->company)->company_name ?? '',
            'medicines' => $medicines->map(fn ($med) => [
                'id'             => $med->id,
                'name'           => $med->medicine_name,
                'purchase_price' => $med->sale_price,
                'mrp'            => $med->mrp,
            ])
        ]);
    }

    public function searchCompanyAjax(Request $request)
    {
        $search = $request->q;

        $companies = Company::where('company_name', 'LIKE', "%{$search}%")
            ->get();

        $results = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'text' => $company->company_name
            ];
        });

        return response()->json(['results' => $results]);
    }


    public function searchMedicineAjx(Request $request)
    {
        $search = $request->q;

        $medicines = Medicine::where('medicine_name', 'LIKE', "%{$search}%")
            ->get();

        $results = $medicines->map(function ($medicine) {
            return [
                'id'   => $medicine->id,
                'text' => $medicine->medicine_name,
            ];
        });

        return response()->json(['results' => $results]);
    }


    public function storeCart(Request $request)
    {
        session(['cart_medicines' => $request->cart]);
        return response()->json(['success' => true]);
    }

    // ChemistOrderController.php
    public function preloadCart()
    {
        $cart = session('cart_medicines', []); // array of medicine IDs and qty
        $medicines = [];

        foreach ($cart as $item) {
            $med = Medicine::find($item['id']);
            if ($med) {
                $medicines[] = [
                    'id' => $med->id,
                    'name' => $med->medicine_name,
                    'mrp' => $med->mrp,
                    'purchase_price' => $med->purchase_price,
                    'qty' => $item['qty'] ?? 1
                ];
            }
        }

        return response()->json($medicines);
    }

}
