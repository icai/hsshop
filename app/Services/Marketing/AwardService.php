<?php
namespace App\Services\Marketing;

use App\Model\MarketingActivityAward;
use App\Services\Service;

/**
 * 奖品发放
 */
class AwardService extends Service{
    /**
     * @var array
     */
    public $field = ['id', 'type', 'refer', 'created_at', 'deleted_at'];
    
    public function __construct()
    {
        $this->request = app('request');
    }
    
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        
        $this->initialize(new MarketingActivityAward(), $uniqueKey, $uniqueValue, $idKey);
        
        return $this;
    }
}