<?php
namespace App\S\ShareEvent;
use App\Model\ActivityRegister;
use App\S\Member\MemberService;
use App\S\S;
use DB;

class ActivityRegisterService extends S
{
	public function __construct()
    {
        parent::__construct('ActivityRegister');
    }


    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function updateBymid($id,$data)
    {
        return $this->model->where('mid',$id)->update($data);
    }

    public function update($id,$data)
    {
        return $this->model->where('id',$id)->update($data);
    }

    public function getRanking()
    {
        $res = $this->model->where('name','<>','')->take(5)->orderBy('num','DESC')->get()->toArray();
        return $res;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180307
     * @desc 查看用户是否已经注册过来
     * @param $wid
     */
    public function getRegister($mid)
    {
        $res = $this->model->where('mid',$mid)->get()->toArray();
        if ($res){
            return current($res);
        }else{
            return [];
        }
    }

    public function increment($where,$field,$num)
    {
        $sql = 'UPDATE ds_activity_register SET '.$field.'='.$field.'+'.$num.' where 1=1 ';
        foreach ($where as $key=>$val){
            $sql .=' and '.$key.'='.$val;
        }
        $res =DB::select($sql);
        if ($res){
            return true;
        }else{
            return false;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180312
     * @desc 获取邀请人数
     */
    public function getNum($openid)
    {
        $memberData = (new MemberService())->getRowByOpenid($openid);
        if (!$memberData){
            return 0;
        }else{
            $res = $this->model->where('mid',$memberData['id'])->get()->toArray();
            if (!$res){
                return 0;
            }else{
                $res = current($res);
                return $res['num'];
            }
        }
    }


}
