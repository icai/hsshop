<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class createBiTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:BiTables 
                            {start_time  : the start time  } {end_time : the end time}';

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
        //
        //
        $stime_stamp = strtotime($this->argument('start_time'));
        $etime_stamp = strtotime($this->argument('end_time'));
        // 计算日期段内有多少天
        $days = ($etime_stamp - $stime_stamp) / 86400 + 1;
        if ($days <= 0) {
            $this->warn('Input arguments error');
            return ;
        }
        $this->output->progressStart($days);
        for($i = 0; $i < $days; $i++){
            $table_name = 'pageview_'.date('Ymd', $stime_stamp + (86400 * $i));
            if(!Schema::connection('mysql_bi')->hasTable($table_name)){

                Schema::connection('mysql_bi')->create($table_name, function (Blueprint $table) {


                    $table->engine = 'Myisam';

                    $table->increments('id');
                    $table->integer('wid');
                    $table->integer('uid');
                    $table->tinyInteger('appid',false,1);
                    $table->tinyInteger('type');
                    $table->integer('p1');
                    $table->integer('p2');
                    $table->string('biKey',64);
                    $table->tinyInteger('source');
                    $table->bigInteger('createtime',false,1);


                    $table->index('wid');
                    $table->index('bikey');
                });
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
