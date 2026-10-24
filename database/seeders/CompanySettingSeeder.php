<?php

namespace Database\Seeders;
use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{

    public function run(): void
    {
        CompanySetting::create([
            'company_name' => 'Demo Company',
            'email'        => 'info@democompany.com',
            'phone'        => '+880 1700-000000',
            'address'      => 'Dhaka, Bangladesh',
            'logo'         => null,
            'favicon'      => null,
            'website_url'  => 'https://democompany.com',
        ]);
    }
}
