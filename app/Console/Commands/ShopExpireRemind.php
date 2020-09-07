<?php
/**
 * @desc 店铺离到期（45天，15天，3天）短信提醒
 *
 * @author 吴晓平 <2019.04.30>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\S\Weixin\ShopService;

class ShopExpireRemind extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ShopExpireRemind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '店铺快到期提醒';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new ShopService())->getBeExpireList();
    }
}
