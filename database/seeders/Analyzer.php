<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Analyzer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('analyzers')->insert([
            'data_name' => 'users_number',
        ]);

        DB::table('analyzers')->insert([
            'data_name' => 'orders_number',
        ]);
    }
}
