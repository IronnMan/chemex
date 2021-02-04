<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSettingsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('admin_settings')->delete();

        \DB::table('admin_settings')->insert([
            0 => [
                'slug' => 'dcat-admin:operation-log',
                'value' => '{"except":[],"allowed_methods":[],"secret_fields":[]}',
                'created_at' => '2020-11-04 15:19:51',
                'updated_at' => '2020-11-04 15:19:51',
            ],
            1 => [
                'slug' => 'field_select_create',
                'value' => '1',
                'created_at' => '2021-01-28 16:47:28',
                'updated_at' => '2021-01-28 16:47:28',
            ],
            2 => [
                'slug' => 'footer_remove',
                'value' => '1',
                'created_at' => '2021-01-28 16:47:25',
                'updated_at' => '2021-01-28 16:47:25',
            ],
            3 => [
                'slug' => 'header_blocks',
                'value' => '1',
                'created_at' => '2021-01-28 16:47:25',
                'updated_at' => '2021-01-28 16:47:25',
            ],
            4 => [
                'slug' => 'sidebar_indentation',
                'value' => '1',
                'created_at' => '2021-01-28 16:47:25',
                'updated_at' => '2021-01-28 16:47:25',
            ],
            5 => [
                'slug' => 'sidebar_style',
                'value' => 'default',
                'created_at' => '2021-01-28 16:47:25',
                'updated_at' => '2021-01-28 16:47:25',
            ],
            6 => [
                'slug' => 'site_debug',
                'value' => '1',
                'created_at' => '2021-01-28 16:47:03',
                'updated_at' => '2021-01-28 16:47:17',
            ],
            7 => [
                'slug' => 'site_lang',
                'value' => 'zh_CN',
                'created_at' => '2021-01-28 16:47:03',
                'updated_at' => '2021-01-28 16:47:03',
            ],
            8 => [
                'slug' => 'site_logo',
                'value' => '',
                'created_at' => '2020-12-20 13:57:11',
                'updated_at' => '2020-12-20 13:57:11',
            ],
            9 => [
                'slug' => 'site_logo_mini',
                'value' => '',
                'created_at' => '2020-12-20 13:57:11',
                'updated_at' => '2020-12-20 13:57:11',
            ],
            10 => [
                'slug' => 'site_logo_text',
                'value' => '咖啡壶',
                'created_at' => '2020-12-20 13:57:11',
                'updated_at' => '2021-01-28 20:34:45',
            ],
            11 => [
                'slug' => 'site_title',
                'value' => '咖啡壶',
                'created_at' => '2020-12-20 13:57:11',
                'updated_at' => '2021-01-28 20:34:45',
            ],
            12 => [
                'slug' => 'site_url',
                'value' => '',
                'created_at' => '2021-01-28 16:47:03',
                'updated_at' => '2021-01-28 16:47:03',
            ],
            13 => [
                'slug' => 'theme_color',
                'value' => 'blue-light',
                'created_at' => '2021-01-28 16:47:25',
                'updated_at' => '2021-01-28 16:47:25',
            ],
            14 => [
                'slug' => 'switch_to_select_create',
                'value' => '1',
                'created_at' => '2021-01-28 16:47:25',
                'updated_at' => '2021-01-28 16:47:25',
            ],
        ]);
    }
}
