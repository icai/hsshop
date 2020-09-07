<?php

namespace App\Jobs;

use App\Module\MemberCardModule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BatchGrantMemberCard implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private     $mid;
    private     $wid;
    private     $card_id;
    private     $operation;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mid,$wid,$card_id,$operation = 0)
    {
        //
        $this->mid      = $mid;
        $this->wid      = $wid;
        $this->card_id  = $card_id;
        $this->operation = $operation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $cardModule = new MemberCardModule();
        if ($this->operation) {
            $re = $cardModule->deleteMemberCardByCardId($this->mid,$this->card_id);
            \Log::info('批量删除会员卡结果：'.$re);
        } else {
            $re = $cardModule->getMemberCard($this->card_id,$this->mid,$this->wid);
            \Log::info('批量发卡结果：');
            \Log::info($re);
        }

    }
}
