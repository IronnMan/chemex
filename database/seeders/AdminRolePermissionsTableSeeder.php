<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminRolePermissionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('admin_role_permissions')->delete();

        \DB::table('admin_role_permissions')->insert([
            0 => [
                'role_id' => 2,
                'permission_id' => 51,
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'role_id' => 2,
                'permission_id' => 53,
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'role_id' => 2,
                'permission_id' => 55,
                'created_at' => null,
                'updated_at' => null,
            ],
            3 => [
                'role_id' => 2,
                'permission_id' => 57,
                'created_at' => null,
                'updated_at' => null,
            ],
            4 => [
                'role_id' => 2,
                'permission_id' => 59,
                'created_at' => null,
                'updated_at' => null,
            ],
            5 => [
                'role_id' => 2,
                'permission_id' => 61,
                'created_at' => null,
                'updated_at' => null,
            ],
            6 => [
                'role_id' => 2,
                'permission_id' => 63,
                'created_at' => null,
                'updated_at' => null,
            ],
            7 => [
                'role_id' => 2,
                'permission_id' => 65,
                'created_at' => null,
                'updated_at' => null,
            ],
            8 => [
                'role_id' => 2,
                'permission_id' => 67,
                'created_at' => null,
                'updated_at' => null,
            ],
            9 => [
                'role_id' => 2,
                'permission_id' => 69,
                'created_at' => null,
                'updated_at' => null,
            ],
            10 => [
                'role_id' => 2,
                'permission_id' => 74,
                'created_at' => '2021-01-28 20:36:29',
                'updated_at' => '2021-01-28 20:36:29',
            ],
            11 => [
                'role_id' => 2,
                'permission_id' => 77,
                'created_at' => '2021-01-28 20:36:29',
                'updated_at' => '2021-01-28 20:36:29',
            ],
        ]);
    }
}
