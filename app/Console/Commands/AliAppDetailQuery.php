<?php

namespace App\Console\Commands;

use App\Module\AliApp\VersionManageModule;
use Illuminate\Console\Command;

class AliAppDetailQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AliAppDetailQuery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '小程序版本详情查询';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new VersionManageModule())->versionDetailQuery();
    }
}






