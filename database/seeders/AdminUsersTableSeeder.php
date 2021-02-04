<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('admin_users')->delete();

        \DB::table('admin_users')->insert([
            0 => [
                'id' => 1,
                'username' => 'admin',
                'password' => '$2y$10$58McWkSyEUvI5.lodI6Ju.F4ZSzkis2od2gMkzAkQzu45iH845Sw2',
                'name' => 'Administrator',
                'avatar' => null,
                'remember_token' => null,
                'created_at' => '2020-11-30 09:58:49',
                'updated_at' => '2021-01-28 16:13:18',
            ],
        ]);
    }
}
