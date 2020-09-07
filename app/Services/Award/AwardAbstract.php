<?php
namespace App\Services\Award;

use App\Model\MarketingActivityAward;

abstract class AwardAbstract{
    /**
     * @var MarketingActivityAward 奖品信息
     */
    private $award;
    
    /**
     * @var int 用户ID
     */
    protected $memberId;
    
    /**
     * @param MarketingActivityAward $award
     */
    public function __construct(MarketingActivityAward $award)
    {
        $this->award = $award;
    }
    
    /**
     * 发送奖品
     * @param int $num 发放数量
     * @return bool
     */
    abstract public function send($num);
    
    /**
     * @return int
     */
    public function getMemberId(): int
    {
        return $this->memberId ? $this->memberId : session('mid', 0);
    }
    
    /**
     * @param int $memberId
     * @return AwardAbstract
     */
    public function setMemberId(int $memberId): AwardAbstract
    {
        $this->memberId = $memberId;
        return $this;
    }
}