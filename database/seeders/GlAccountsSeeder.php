<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('gl_accounts')->insert([
            ['account_name' => 'Assets'],
            ['account_name' => 'Liabilities'],
            ['account_name' => 'Income'],
            ['account_name' => 'Expense'],
        ]);
    }
}
