<?php 
namespace App\S;
use App\S\S;
use App\Lib\Redis\BalanceRule as BalanceRuleRedis;

/**
 * 客户/会员
 */
class BalanceRuleService extends S 
{
    protected $initRule = [
        '0' =>  ['title'    =>  '充值0.01', 'money'   => 0.01],
        '1' =>  ['title'    =>  '充值5', 'money'   => 5],
        '2' =>  ['title'    =>  '充值100', 'money'   => 100],
        '3' =>  ['title'    =>  '充值500', 'money'   => 500],
    ];
    public function __construct()
    {
        parent::__construct('BalanceRule');
    }

    public function getWidRule($wid)
    {
        $where['wid'] = $wid;
        list($list) = $this->getListWithPage($where, 'money', 'ASC', 20);
        if (empty($list['data'])) {
            for ($i=0; $i < 4; $i++) { 
                $this->addRule($wid, $this->initRule[$i]['title'],$this->initRule[$i]['money'] * 100);
            }
            list($list) = $this->getListWithPage($where, 'money', 'ASC', 20);
        }
        return $list;
    }

    public function checkMoneyAddScore($wid, $money)
    {
        $where['wid'] = $wid;
        $where['money'] = $money;
        $checkData = $this->model->wheres($where)->first();
        $add_score = $checkData['add_score'] ?: 0;
        return $add_score;
    }

    public function checkMoney($wid, $money, $id = 0)
    {
        $where['wid'] = $wid;
        $where['money'] = $money;
        $checkData = $this->model->wheres($where)->first();
        if (empty($checkData)) {
            return true;
        }
        if ($id > 0 && $id == $checkData->id) {
              return true;
        }  
        return false;
    }


    public function addRule($wid, $title, $money, $add_money = 0, $add_score = 0)
    {
        $data['wid']       = $wid;
        $data['title']     = $title;
        $data['money']     = $money;
        $data['add_money'] = $add_money ;
        $data['add_score'] = $add_score;
        return $this->model->insertGetId($data);
    }

    public function countRule($wid)
    {
        $where['wid']       = $wid;
        return $this->model->wheres($where)->count();
    }

    public function delBalanceRule($id, $wid)
    {
        $redis = new BalanceRuleRedis();
        $redis->delete($id);
        $where['id'] = $id;
        $where['wid'] = $wid;
        return $this->model->wheres($where)->delete();
    }

    public function editBalanceRule($id, $wid, $data)
    {
        $where['id'] = $id;
        $where['wid'] = $wid;

        $res = $this->model->wheres($where)->update($data);
        if (!$res){
            return false;
        }
        $redis = new BalanceRuleRedis();
        $re = $redis->updateOne($id,$data);
        return $re;
    }

    public function getRowById($id)
    {
        $result = [];
        $redis = new BalanceRuleRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->add($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new BalanceRuleRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }
}
