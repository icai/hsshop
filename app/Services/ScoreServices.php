<?php
namespace App\Services;

/**
 * 积分服务
 */
class ScoreServices extends Service{
    /**
     * @var int 用户ID
     */
    private $memberId;
    
    public function __construct($memberId)
    {
        $this->memberId = $memberId;
    }
    
    /**
     * 增加积分
     * @param int $num
     */
    public function incr($num)
    {
        //@todo
    }
    
    /**
     * 扣除积分
     * @param int $num
     */
    public function decr($num)
    {
        //@todo
    }
    
    /**
     * 获取积分
     */
    public function get()
    {
        //@todo
    }
    
    /**
     * 判断用户积分是否足够
     * @param int $num
     * @return bool
     */
    public function hasEnoughScore($num){
        $score = $this->get();
        return $score >= $num;
    }
    
    /**
     * @return int
     */
    public function getMemberId(): int
    {
        return $this->memberId;
    }
    
    /**
     * @param int $memberId
     * @return ScoreServices
     */
    public function setMemberId(int $memberId): ScoreServices
    {
        $this->memberId = $memberId;
        return $this;
    }
}