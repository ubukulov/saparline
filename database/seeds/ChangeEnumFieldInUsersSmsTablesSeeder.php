<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeEnumFieldInUsersSmsTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("ALTER TABLE sms CHANGE COLUMN role role ENUM('passenger', 'driver', 'lodger')");
        DB::statement("ALTER TABLE users CHANGE COLUMN role role ENUM('passenger', 'driver', 'lodger')");
    }
}
