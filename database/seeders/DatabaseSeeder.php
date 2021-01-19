<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->truncate();
        Company::query()->truncate();
        User::factory()->count(rand(5,10))->has(Company::factory()->count(rand(2,6)), 'companies')->create();
    }
}
