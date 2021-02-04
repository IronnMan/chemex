<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Fill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chemex:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '为 Chemex 导入预填充数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->call('db:seed', ['--class' => 'DeviceCategoriesTableSeeder']);
        $this->call('db:seed', ['--class' => 'VendorRecordsTableSeeder']);

        return 0;
    }
}
