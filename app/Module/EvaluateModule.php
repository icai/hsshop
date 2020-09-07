<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/10/12
 * Time: 13:45
 */

namespace App\Module;


use App\S\File\FileInfoService;
use App\S\Product\ProductEvaluateClassifyService;
use App\Services\Order\OrderDetailService;
use App\Services\ProductEvaluateDetailService;
use App\Services\ProductEvaluateService;

class EvaluateModule
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc  获取商品评价信息
     */
    public function getProductEvaluate($pid,$classifyName='')
    {
        $where = [
            'pid'   => $pid,
        ];
        if ($classifyName){
            $res = (new ProductEvaluateClassifyService())->getList(['classify_name'=>$classifyName]);
            $ids = [];
            foreach ($res as $val) {
                $ids[] = $val['eid'];
            }
            $where['id'] = ['in',$ids];
        }

        $productEvaluateService = new ProductEvaluateService();
        list($res) = $productEvaluateService->init()->where($where)->getList();
        $result = $imgIds = $odids = $ids = [];
        foreach ($res['data'] as $val){
            $tmp = [
                'id'            => $val['id'],
                'nickname'      => $val['member']['nickname'],
                'headimgurl'    => $val['member']['headimgurl'],
                'content'       => $val['content'],
                'odid'          => $val['odid'],
                'img'           => $val['img'],
                'created_at'   => $val['created_at'],
                'pid'           => $val['pid'],
            ];
            $odids[] = $val['odid'];
            $ids[] = $val['id'];
            if ($val['img']){
                $imgIds = array_merge($imgIds,explode(',',$val['img']));
            }
            $result[] = $tmp;
        }
        //获取商品购买的规格和评价图片
        if ($odids){
            $orderDetailService = new OrderDetailService();
            $orderDetailData = $orderDetailService->init()->model->whereIn('id',$odids)->get(['id','spec'])->toArray();
            $orderDetailData = $this->deal($orderDetailData);
        }
        if ($imgIds){
            $imgData = (new FileInfoService())->getListById($imgIds);
            $imgData = $this->deal($imgData);
        }
        foreach ($result as &$item){
            $item['spes'] = $orderDetailData[$item['odid']]['spec']??'';
            $imgs = [];
            if ($item['img']){
                foreach (explode(',',$item['img']) as $val){
                    $imgs[] = $imgData[$val]??[];
                }
            }
            $item['img'] = $imgs;
        }
        $this->reply($result,$ids);
        return $result;
    }

    public function reply(&$result,$ids)
    {
        $res = (new ProductEvaluateDetailService())->init()->model->whereIn('eid',$ids)->where('mid',0)->get(['id','content','eid'])->toArray();
        $reply = [];
        foreach ($res as $val){
            $reply[$val['eid']] = $val;
        }
        foreach ($result as &$item){
            $item['reply'] = $reply[$item['id']]??[];
        }
    }



    public function deal($data)
    {
        $result = [];
        foreach ($data as $val){
            $result[$val['id']] = $val;
        }

        return $result;
    }
}