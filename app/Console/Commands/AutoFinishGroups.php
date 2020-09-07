<?php

namespace App\Console\Commands;

use App\Module\GroupsRuleModule;
use Illuminate\Console\Command;

class AutoFinishGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoFinishGroups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '团活动自动到期';

    /**
     * Execute the console command.
     *
     * todo  
     * @return mixed
     */
    public function handle()
    {
        //获取所有正在进行中的团
        (new  GroupsRuleModule())->autoGroups();
    }
}






