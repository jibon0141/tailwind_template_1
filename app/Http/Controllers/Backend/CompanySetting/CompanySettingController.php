<?php
namespace App\Http\Controllers\Backend\CompanySetting;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\ManageImage;

class CompanySettingController extends Controller
{

    use ManageImage;

    public function index()
    {
        $company = CompanySetting::first();
        return view('admin.extends.company.update', compact('company'));
    }


    public function update(Request $request, $id)
    {
        try {

            $request->validate([
                'company_name' => 'required|string|max:255',
                'address'      => 'required|string',
                'phone'        => 'required|string|max:50',
                'email'        => 'required|email',
                'logo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'favicon'      => 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:1024',
                'website_url'  => 'nullable|url',
            ]);

            $company = CompanySetting::where('id',$id)->first();

            if (!$company) {
                Log::error('Company not found ');
                return redirect()->back()->with('error', 'Company not found.');
            }

            // Logo Link-Unlink Part

            $logoName = $company->logo;
            if($request->hasFile('logo')){
                if (!empty($company->logo)) {
                    $this->destroyImage($company->logo, 'image/company_logo');
                }
                $logoName = $this->storeImage($request->file('logo'), 'image/company_logo');
            }

            // favicon unlink fix

            $faviconName = $company->favicon;
            if ($request->hasFile('favicon')) {
                if (!empty($company->favicon)) {
                    $this->destroyImage($company->favicon, 'image/company_favicon');
                }
                $faviconName = $this->storeImage($request->file('favicon'), 'image/company_favicon');
            }


            $data = [
                'company_name' => $request->company_name,
                'address'      => $request->address,
                'phone'        => $request->phone,
                'email'        => $request->email,
                'logo'         => $logoName,
                'favicon'      => $faviconName,
                'website_url'  => $request->website_url,
                'updated_at'   => date('Y-m-d H:i:s'),
            ];

            CompanySetting::where('id', $id)->update($data);
            Log::info('Company updated successfully');
            return redirect()->back()->with('success', 'Company updated successfully.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Company Update Failed.');
        }
    }

}
