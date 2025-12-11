<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => 'テスト　1',
            'email' => '123@456.com',
            'password' => 'password',
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'テスト　2',
            'email' => '456@123.com',
            'password' => 'password',
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '管理者',
            'email' => 'kanri@aaa.com',
            'password' => 'kanripass',
        ];
        DB::table('users')->insert($param);
    }
}
