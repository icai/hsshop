<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/5/4
 * Time: 9:43
 */

namespace App\Services\Order;


use App\Model\Logistics;
use App\Services\Service;
use OrderDetailService;

class LogisticsService extends Service
{
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */

        $this->field = ['id','logistic_no','express_name','express_id','oid','odid','word','logistic_log','created_at','no_express'];

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new Logistics(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $oid
     * @return array
     * @update 张永辉 2019年8月14日 物流单号不去掉英文字母
     */
    public function getLogistics($oid)
    {
        $result = [
            'success'   => 0,
            'message'   => '',
        ];
        list($data) = $this->init()->where(['oid'=>$oid])->getList(false);
        if (!$data['data']){
            $result['message'] = '物流信息不存在';
            return $result;
        }
        $tmp = [];
        $needArray = [];
        foreach ($data['data'] as $key=>$val){
            if (in_array($val['logistic_no'],$needArray)){
                unset($data['data'][$key]);
                continue;
            }
            $needArray[] = $val['logistic_no'];
            //add MayJay
            if($val['no_express'] == 1){
                $res['message'] = '无需物流';
                $res['no_express']        = 1;
                //end
            }else if (!$val['logistic_log']){
                //去掉运单号中除数字之外的字串 hsz 例如运单号:234325436她,查询报错
                // preg_match_all('/\d+/',$val['logistic_no'],$arr);
                // $val['logistic_no'] = join('', $arr[0]);
                //end
//                $url = 'http://www.kuaidi100.com/query?&type='.$val['word'].'&postid='.$val['logistic_no'];
//                $res = jsonCurl($url);
                $res = $this->logistics($val['word'],$val['logistic_no']);
                if (!empty($res['returnCode'])){
                    $res['data']= [];
                }
                if (isset($res['state']) && $res['state'] == 3){
                    $this->init()->where(['id'=>$val['id']])->update(['id'=>$val['id'],'logistic_log'=>json_encode($res)],false);
                }
            }else{
                $res = json_decode($val['logistic_log'],true);
            }
            //获取商品图片
            $odid = explode(',',$val['odid']);
            $res['img'] = OrderDetailService::init()->model->whereIn('id',$odid)->get(['id','img','num'])->toArray();
            $res['sum'] = 0;
            foreach ($res['img'] as $v){
                $res['sum'] = $res['sum']+$v['num'];
            }
            //change MayJay
            if($val['no_express'] == 0){
                $this->dealLogistics($res);
            }
            //end
            $res['com'] = $val['express_name'];
            $res['created_at'] = $val['created_at'];
            $res['nu'] = $val['logistic_no'];
            $res['id'] = $val['id'];
            $tmp[] = $res;
        }
        $result['data'] = $tmp;
        $result['success'] = 1;
        return $result;
    }


    public function dealLogistics(&$data)
    {
        if ($data['data']){
            $w = [
                '星期日',
                '星期一',
                '星期二',
                '星期三',
                '星期四',
                '星期五',
                '星期六',
            ];
            foreach ($data['data'] as &$val) {
                $tmp = explode("@",date("Y-m-d@H:i:s@w",strtotime($val['time'])));
                $val['date'] = $tmp[0];
                $val['now'] = $tmp[1];
                $val['week'] = $w[$tmp[2]];
            }
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180104
     * @desc
     */
    public function getByOid($oid)
    {
        $result = [];
        $res = $this->init()->model->where('oid',$oid)->get()->toArray();
        if ($res){
            foreach ($res as $val){
                $tem = explode(',',$val['odid']);
                foreach ($tem as $value){
                    $result[$value] = [
                        'id'             => $val['id'],
                        'logistic_no'    => $val['logistic_no'],
                        'express_name'  => $val['express_name'],
                    ];
                }
            }
        }
        return $result;
    }


    /**
     * 快递100获取快递信息
     * @param $word
     * @param $num
     * @return array
     * @author 张永辉 2018年8月13日
     */
    public function logistics($word,$num)
    {
        $params['customer'] = '84E98FFB0F5A3AE68378568A796B1001';
        $key = 'XSfbDRNw9721';
        $params['param'] = json_encode(['com'=>$word,'num'=>$num]);
        $params["sign"] = md5($params["param"].$key.$params["customer"]);
        $params["sign"] = strtoupper($params["sign"]);
        $apiUrl = 'https://poll.kuaidi100.com/poll/query.do';
        $o="";
        foreach ($params as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";
        }
        $post_data=substr($o,0,-1);
        $apiUrl .= '?'.$post_data;
        $result = jsonCurl($apiUrl,$params);
        return $result;
    }



}