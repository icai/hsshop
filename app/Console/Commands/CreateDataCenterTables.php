<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataCenterTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:DCTables 
                            {start_time  : the start time  } {end_time : the end time}
                            ';

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
            $table_name = 'page_view_'.date('Ymd', $stime_stamp + (86400 * $i));
            if(!Schema::connection('mysql_dc_page_log')->hasTable($table_name)){

                Schema::connection('mysql_dc_page_log')->create($table_name, function (Blueprint $table) {


                    $table->engine = 'Myisam';

                    $table->increments('id');
                    $table->integer('wid');
                    $table->integer('uid');
                    $table->tinyInteger('app_id',false,1);
                    $table->tinyInteger('type');
                    $table->integer('p1');
                    $table->tinyInteger('p2');
                    $table->string('biKey',64);
                    $table->tinyInteger('source');
                    $table->integer('created_time',false,1);
                    $table->string('title',50)->nullable();

                    $table->index('wid');
                    $table->index('bikey');
                });
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

    }




}
