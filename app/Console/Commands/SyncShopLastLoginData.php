<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2019/8/19
 * Time: 11:09
 * Desc：同步店铺最后访问时间记录
 */

namespace App\Console\Commands;

use App\Model\Weixin;
use Illuminate\Console\Command;

class SyncShopLastLoginData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:shopLastLoginData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync shop last login time  from cache to database at 2:00Am every day';

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
     * @return mixed
     */
    public function handle(Weixin $shop)
    {
        //
        $shop->syncLastAtToDataBase();
    }
}
