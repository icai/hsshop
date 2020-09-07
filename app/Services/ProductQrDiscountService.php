<?php

namespace App\Services;

use App\Model\ProductQrDiscount;
use Carbon\Carbon;

class ProductQrDiscountService extends Service
{
    public function __construct(ProductQrDiscount $productQrDiscount){
        $this->model = $productQrDiscount;
    }

    /*
     * @todo: 添加编辑扫码折扣
     */
    public function setQrDiscount($params = array(),$wid = 0){
        if(!$params || !$wid){
            error('扫码折扣添加参数错误！');
        }
        if(!isset($params['buy_way'])){
            error('请选择购买方式');
        }
        if(!isset($params['discount_way'])){
            error('请选择优惠方式');
        }
        if(!isset($params['product_id']) || !$params['product_id']){
            error('请选择商品');
        }
        if(!isset($params['discount_num']) || !$params['discount_num']){
            error('优惠折扣或金额必须填写');
        }
        if($params['discount_num'] <= 0){
            error('优惠折扣或金额必须大于0');
        }
        # 查询 是否存在 该商品的会员价
        $qrlist = $this->getQrDiscount(array('product_id'=>$params['product_id']),$wid);
        if(!empty($qrlist)){
            foreach($qrlist as $list){
                $params['id'] = $list['id'];
            }
        }

        $insert_data = array(
            'wid'  =>  $wid,
            'product_id' => $params['product_id'],
            'buy_way' => $params['buy_way'],
            'discount_way' => $params['discount_way'],
            'discount_num' => $params['discount_num'],
            'status' => 1
        );
        if(isset($params['id'])&&$params['id']){
            $where['wid'] = $wid;
            $where['product_id'] = $params['product_id'];
            $flag = ProductQrDiscount::wheres($where)->update($insert_data);
        }else{
            $insert_data['created_at'] = Carbon::now();
            $flag = ProductQrDiscount::insertGetId($insert_data);
        }
        if($flag){
            return true;
        }
        return false;
    }

    /*
     * @todo: 删除扫码折扣
     */
    public function delQrDiscount($params = array() ,$wid = 0){
        if(!$wid){
            error('非法操作！');
        }
        $product_id = isset($params['product_id']) && $params['product_id'] ? $params['product_id'] : 0;
        if(!$product_id){
            error('请选择您要设置扫码优惠的产品');
        }
        $where['wid'] = $wid;
        $where['product_id'] = $product_id;
        $flag = ProductQrDiscount::wheres($where)->update(array('status'=>0,'deleted_at'=>Carbon::now()));
        if($flag){
            return true;
        }
        return false;
    }

    /*
     * @todo: 查询扫码折扣
     */
    public function getQrDiscount($params = array() ,$wid = 0){
        if(!$wid){
            error('非法操作！');
        }
        $product_id = isset($params['product_id']) && $params['product_id'] ? $params['product_id'] : 0;
        if(!$product_id){
            error('请选择您要查询扫码优惠的产品');
        }
        $fields = ['id','wid','product_id','buy_way','discount_way','discount_num'];
        $where['wid'] = $wid;
        $where['status'] = 1;
        $where['product_id'] = $product_id;
        $list = ProductQrDiscount::select($fields)->wheres($where)->get()->toArray();
        return $list;
    }
    
}