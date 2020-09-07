<?php
namespace App\Services\Award;

use App\Services\ScoreServices;

class ScoreAward extends AwardAbstract{
    
    /**
     * 发送奖品
     * @param int $num
     * @return bool
     */
    public function send($num)
    {
        (new ScoreServices($this->getMemberId()))->incr($num);
        return true;
    }
}