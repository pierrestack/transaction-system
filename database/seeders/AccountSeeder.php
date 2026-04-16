<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac80', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 1, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac81', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 2, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac82', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 3, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac83', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 4, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac84', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 5, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac85', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 6, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac86', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 7, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac87', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 8, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac88', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 9, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['account_number' => 'd31767d8-544f-4e3f-bdee-fb12a718ac89', 'balance' => 0.00, 'type' => 'system', 'system_name' => 'fees', 'currency_id' => 10, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        foreach ($accounts as $account) {
            Account::insert($account);
        }

        Account::factory()
            ->count(20)
            ->create();
    }
}
