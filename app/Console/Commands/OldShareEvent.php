<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class OldShareEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OldShareEvent';

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
        $sql = "INSERT INTO dc_share_event_record (`wid`, `share_event_id`, `source_id`, `actor_id`, `source`, `created_at`) VALUES ";
        $connect = DB::connection('mysql_dc_order_log');
        $re = DB::table('share_event_record')
            ->select(['*'])
            ->chunk(500,function ($item) use($connect,$sql) {
                foreach ($item as $value) {
                    $sql .= "($value->wid,$value->share_event_id,$value->source_id,$value->actor_id,$value->source,$value->created_at),";
                }
                $sql = substr($sql, 0,-1);
                $sql .= "ON DUPLICATE KEY UPDATE  wid = VALUES(wid),  share_event_id = VALUES(share_event_id),  source_id = VALUES(source_id),actor_id = VALUES(actor_id),source = VALUES(source),created_at = VALUES(created_at)";
                $connect->insert($sql);
            });
    }
}
