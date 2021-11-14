<?php

use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Cashier::create([
            'username' => 'cashier1', 'password' => bcrypt('123456'), 'city_id' => 1, 'first_name' => 'Cashier 1',
            'last_name' => 'Cashier'
        ]);
    }
}
