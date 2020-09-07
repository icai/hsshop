<?php
namespace App\S\ShareEvent;
use App\S\S;

class MeetingNexusService extends S
{
	public function __construct()
    {
        parent::__construct('MeetingNexus');
    }


    public function add($data)
    {
        return $this->model->insertGetId($data);
    }


    public function getRowByOpenId($openid)
    {
        $result = $this->model->where('openid',$openid)->get()->toArray();
        if ($result){
            return current($result);
        }else{
            return [];
        }
    }





}
