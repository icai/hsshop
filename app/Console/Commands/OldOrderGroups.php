<?php

/**
 * 拼团订单老数据迁移
 * @create 何书哲 2018年7月9日
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Module\OrderModule;
use DB;

class OldOrderGroups extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'OldOrderGroups';

    /**
     * The console command description.
     * @var string
     */
    protected $description = '迁移订单老拼团数据';

    /**
     * 创建定时任务实例
     * @param null
     * @return void
     * @create 何书哲 2018年7月9日
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 执行定时任务
     * @param null
     * @return void
     * @create 何书哲 2018年7月9日
     */
    public function handle()
    {
        (new OrderModule())->orderGroupsStatistics();
    }
}