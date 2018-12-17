<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Sylvain Roh',
            'email' => 'syzin12@gmail.com',
            'password' => Hash::make('pass123$'),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@potostop.ch',
            'password' => Hash::make('pass123$'),
        ]);
    }
}
