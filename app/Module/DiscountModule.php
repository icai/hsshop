<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/8/6
 * Time: 18:44
 */

namespace App\Module;


use App\S\Market\DiscountService;
use App\S\Product\ProductService;

class DiscountModule
{


    public $discountService;

    public function __construct()
    {
        $this->discountService = new DiscountService();
    }


    /**
     * 保存满减活动
     * @param $input
     * @author 张永辉
     */
    public function save($input,$wid)
    {
        $result = ['errCode'=>0,'errMsg'=>'','data'=>[]];
        if (empty($input['end_time'])){
            $input['end_time'] = '2038-01-01 00:00:00';
        }
        if ($input['use_type'] == '1'){
            $query = $this->discountService->model->where('wid',$wid);
            if (!empty($input['id'])){
                $query = $query->where('id','!=',$input['id']);
            }
            $query->where(function ($query) use ($input){
                $query->where(function ($query) use ($input){
                    $query->where('start_time', '>=', $input['start_time'])->where('start_time', '<=', $input['end_time']);
                });
                $query->orWhere(function ($query) use ($input){
                    $query->where('end_time','>=',$input['start_time'])->where('end_time','<=',$input['end_time']);
                });
            });
            $res = $query->first();
            if ($res){
                $result['errCode'] = 40002;
                $result['errMsg'] = '店铺存在时间重叠满减活动，不能全店商品可用';
                return $result;
            }
            $input['use_content'] = '';
        }
        if ($input['use_type'] == '2'){
            $useContent = $input['use_content'];
            $res = $this->_checkDiscountProduct($useContent,$wid,$input);
            if ($res['errCode'] != '0'){
                return $res;
            }
            $useContent = array_unique($useContent);
            $input['use_content'] = implode(',',$useContent);
        }
        $insertData = [
            'wid'            => $wid,
            'title'          => $input['title'],
            'start_time'     => $input['start_time'],
            'end_time'       => $input['end_time'],
            'type'            => $input['type'],
            'content'        => json_encode($input['content']),
            'use_type'       => $input['use_type'],
            'use_content'    => $input['use_content'],
        ];
        if (empty($input['id'])){
            $res = $this->discountService->add($insertData);
        }else{
            $res = $this->discountService->update($input['id'],$insertData);
        }
        if ($res){
            return $result;
        }else{
            $result['errCode'] = 40019;
            $result['errMsg'] = '添加失败';
            return $result;
        }
    }


    /**
     * 验证是否可以添加满减活动
     * @author 张永辉
     */
    private function _checkDiscountProduct($useContent,$wid,$input)
    {
        $result = ['errCode'=>0,'errMsg'=>''];
        $query = $this->discountService->model->where('wid',$wid)->where('use_type','1');
        if (!empty($input['id'])){
            $query->where('id','!=',$input['id']);
        }
        $query->where(function ($query) use ($input){
            $query->where(function ($query) use ($input){
                $query->where('start_time', '>=', $input['start_time'])->where('start_time', '<=', $input['end_time']);
            });
            $query->orWhere(function ($query) use ($input){
                $query->where('end_time','>=',$input['start_time'])->where('end_time','<=',$input['end_time']);
            });
        });
        $res = $query->first();
        if ($res){
            $result['errCode'] = 40003;
            $result['errMsg'] = '已存在有时间重叠全店商品可用的满减活动';
            return $result;
        }

        $query = $this->discountService->model->where('wid',$wid)->where('use_type','2');
        if (!empty($input['id'])){
            $query->where('id','!=',$input['id']);
        }
        $query->where(function ($query) use ($input){
            $query->where(function ($query) use ($input){
                $query->where('start_time', '>=', $input['start_time'])->where('start_time', '<=', $input['end_time']);
            });
            $query->orWhere(function ($query) use ($input){
                $query->where('end_time','>=',$input['start_time'])->where('end_time','<=',$input['end_time']);
            });
        });
        $res = $query->get()->toArray();
        if (!$res){
            return $result;
        }
        $nowPids = [];
        foreach ($res as $val){
            $tmpPids = explode(',',$val['use_content']);
            $nowPids = array_merge($nowPids,$tmpPids);
        }

        $pid = array_intersect($nowPids,$useContent);
        if ($pid){
            $proudct = (new ProductService())->getListById($pid);
            if ($proudct){
                $titles = array_column($proudct,'title');
                $pids = array_column($proudct,'id');
                $result['errCode'] = 400011;
                $result['errMsg'] = '商品已参与其它满减活动,请查看全部修改';
                $result['data'] = $pids;
                return $result;
            }
        }
        return $result;

    }


    /**
     * 根据商品id计算优惠金额
     * @param $param = [
     *      [id=>2,num=3,price=33],
     *      [id=>56,num=99,price=44],
     * ]
     * @author 张永辉 2018年8月8日
     */
    public function getDiscountByPids($param,$wid)
    {
        $allDiscount = 0;
        $discountPid = [];
        $nowtime = date('Y-m-d H:i:s',time());
        $query = $this->discountService->model->where('start_time','<',$nowtime)->where('end_time','>',$nowtime)->where('wid',$wid);
        $query->where(function ($query) use($param){
            foreach ($param as $key=>$val){
                $query->orWhereRaw("find_in_set(".$val['id'].",use_content)");
            }
            $query->orWhere('use_type',1);
        });
        $discount = $query->orderBy('use_type','asc')->get()->toArray();
        foreach ($discount as &$item){
            $item['num'] = 0;
            $item['amount'] = 0;
            if ($item['use_type'] == '1'){
                foreach ($param as $key=>$val){
                    $item['num'] += $val['num'];
                    $item['amount'] += $val['price'];
                    $item['discountPids'][] = $val['id'];
                    unset($param[$key]);
                }
            }else{
                $pids = explode(',',$item['use_content']);
                foreach ($param as $key=>$val){
                    if (in_array($val['id'],$pids)){
                        $item['num'] += $val['num'];
                        $item['amount'] += $val['price'];
                        $item['discountPids'][] = $val['id'];
                        unset($param[$key]);
                    }
                }
            }
            $item['discount'] = $this->_computeDiscount($item);
            $allDiscount += $item['discount'];
        }
        foreach ($discount as $key=>$val){
            if ($val['discount'] <= 0 ){
                unset($discount[$key]);
            }
        }

        $result = [
            'discount'  => $allDiscount,
            'discountDetail' => $discount,
        ];
        return $result;
    }


    /**
     * 计算折扣
     * @param $discount
     * @author 张永辉 2018年 8月8日
     */
    private function _computeDiscount($discount)
    {
        $derate = 0;
        $content = json_decode($discount['content'],true);
        array_multisort(array_column($content,'discount'),SORT_DESC,$content);
        $discount['type'] == '2' ? $key='num' : $key='amount';
        foreach ($content as $val){
            if ($val['condition'] <= $discount[$key]){
                $derate = $val['discount'];
                break;
            }
        }
        return $derate;
    }


    /**
     * 计算折扣
     * @param $discount
     * @author 张永辉 2018年 8月8日
     * @update 陈文豪 2018年09月11日处理满减bug
     */
    function getDiscountDetailByPid($pid, $wid )
    {
//        \DB::connection()->enableQueryLog();
        $nowtime = date('Y-m-d H:i:s',time());
        $query  = $this->discountService->model->where('wid', $wid)->where('start_time','<',$nowtime)->where('end_time','>',$nowtime);
        
        $query->where(function ($query) use($pid){
            $query->whereRaw("find_in_set(".$pid.",use_content)");
            $query->orWhere('use_type','1');
        });
        $discount = $query->orderBy('use_type','asc')->get()->toArray();
        if (!$discount){
            return [];
        }
        $discount = current($discount);
//        \DB::getQueryLog()

        $discount['type'] == 1?$type = '元': $type = '件';
        $str = '';
        $detail = [];
        foreach (json_decode($discount['content'],true) as $item) {
            $str .= $detail[] ='满'.$item['condition'].$type.'减'.$item['discount'].'元';
            $str .= ';';
        }
        $discount['detail'] = $detail;
        $discount['str'] = $str;
        return $discount;
    }


    /**
     * 获取满减活动的所有商品id
     * @author 张永辉
     */
    public function getPids($wid)
    {
        $result = ['errCode'=>0,'errMsg'=>''];
        $nowtime = date('Y-m-d H:i:s',time());
        $where = [
            'end_time'=>['>',$nowtime],
            'wid'       => $wid,
        ];
        $data = $this->discountService->getList($where, $skip = "", $perPage = "", $orderBy = "use_type", $order = "asc");
        if ($data && $data[0]['use_type'] == 1){
            $result['errCode'] = -1;
            return $result;
        }
        $pids = [];
        array_map(function ($item) use(&$pids){
            $tmp = explode(',',$item['use_content']);
            $pids = array_merge($pids,$tmp);
        },$data);

        $result['data'] = $pids;
        return $result;
    }


}