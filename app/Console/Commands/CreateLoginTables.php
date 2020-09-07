<?php

namespace App\Console\Commands;

use function EasyWeChat\Payment\get_client_ip;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:LoginTables {start_time  : the start time  } {end_time : the end time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
        $stime_stamp = strtotime($this->argument('start_time'));
        $etime_stamp = strtotime($this->argument('end_time'));
        // 计算日期段内有多少天
        $days = ($etime_stamp - $stime_stamp) / 86400 + 1;
        if ($days <= 0) {
            $this->warn('Input arguments error');
            return ;
        }
        $this->output->progressStart($days);
        for($i = 0; $i < $days; $i++) {
            //表名
            $table_name = 'login_' . date('Ymd', $stime_stamp + (86400 * $i));
            //数据表不存在则创建
            if(!Schema::connection('mysql_dc_login_log')->hasTable($table_name)){
                Schema::connection('mysql_dc_login_log')->create($table_name, function (Blueprint $table) {
                    $table->engine = 'Myisam';
                    $table->increments('id')->comment('主键id');
                    $table->integer('uid')->comment('用户id');
                    $table->ipAddress('ip')->comment('IP地址');
                    $table->tinyInteger('type')->comment('类型 1:商家ds_user.id 2:总后台ds_account.id 3:微信公众号ds_member.id 4:微信小程序ds_member.id 5:支付宝小程序ds_member.id');
                    $table->integer('created_time')->comment('创建时间');
                });
            }
        }
    }

}