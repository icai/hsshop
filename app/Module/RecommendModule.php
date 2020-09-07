<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/4/3
 * Time: 19:54
 */

namespace App\Module;

use App\S\ShareEvent\ShareEventService;
use ProductService;
use App\S\Market\CommendInfoService;
use App\S\Market\CommendDetailService;

class RecommendModule
{
    /***
     * todo 处理享立减数据
     * @param array $shareGoods
     * @return array
     * @author jonzhang
     * @date 2018-04-04
     * @update 陈文豪 2019年09月24日10:07:33 处理报错，代码规范
     */
    public function processShareGoods($wid = 0, $activityId = 0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => [], 'error' => []];
        if (empty($wid)) {
            $returnData['errCode'] = -101;
            $returnData['errMsg'] = 'wid为空';
            return $returnData;
        }
        if (empty($activityId)) {
            $returnData['errCode'] = -102;
            $returnData['errMsg'] = '活动id为空';
            return $returnData;
        }
        $commendData = (new  CommendInfoService())->getListByCondition(['type' => 1, 'wid' => $wid]);
        if ($commendData['errCode'] == 0 && !empty($commendData['data'])) {
            //定向推荐
            if ($commendData['data'][0]['is_auto']) {
                $cid = $commendData['data'][0]['id'];
                $commendDetailData = (new CommendDetailService())->getListBycondition(['cid' => $cid, 'current_status' => 0], 'id', 'desc', 20);
                if ($commendDetailData['errCode'] == 0 && !empty($commendDetailData['data'])) {
                    $shareEventService = new ShareEventService();
                    $error = [];
                    $i = 0;
                    foreach ($commendDetailData['data'] as $commendDetailItem) {
                        if ($activityId == $commendDetailItem['recommendation_id']) {
                            $error[] = ['activityId' => $activityId, 'code' => 0, 'msg' => '推荐活动id与当前页面的id相同'];
                            continue;
                        }
                        //status为1表示已删除 type为1表示失效
                        $obj = $shareEventService->getRow(['id' => $commendDetailItem['recommendation_id'], 'status' => 0, 'type' => 0]);
                        if ($obj['errCode'] == 0 && !empty($obj['data']['product_id'])) {
                            $shareEventData = $obj['data'];
                            //过期享立减活动不显示
                            if ($shareEventData['end_time'] < time() || time() < $shareEventData['start_time']) {
                                $error[] = ['activityId' => $shareEventData['id'], 'code' => $obj['errCode'], 'msg' => '享立减活动日期问题', 'startTime' => date("Y-m-d H:i:s", $shareEventData['start_time']), 'endTime' => date("Y-m-d H:i:s", $shareEventData['end_time'])];
                                continue;
                            }
                            $productData = ProductService::getDetail($obj['data']['product_id']);
                            $errMsg = "";
                            if (isset($productData['status'])) {
                                //status为1表示上架
                                if ($productData['status'] != 1) {
                                    $errMsg .= "该商品已下架";
                                }
                            }
                            if (isset($productData['is_distribution'])) {
                                //分销商品不能够享立减
                                if ($productData['is_distribution'] == 1) {
                                    $errMsg .= "分销商品不能够享立减";
                                }
                            }
                            if (strlen($errMsg) > 0) {
                                $error[] = ['activityId' => $shareEventData['id'], 'productId' => $shareEventData['product_id'], 'msg' => $errMsg];
                                continue;
                            }
                            //保底价分转化为元
                            if (isset($shareEventData['lower_price']) && $shareEventData['lower_price'] > 0) {
                                $shareEventData['lower_price'] = sprintf('%.2f', $shareEventData['lower_price'] / 100);
                            }
                            //逐减人数
                            $total = 0;
                            $total = $total + $shareEventData['reduce_total'];
                            //开启初始值
                            if ($shareEventData['is_initial']) {
                                $total = $total + $shareEventData['initial_value'];
                            }
                            if ($i < 4) {
                                $returnData['data'][] = [
                                    "id"          => $obj['data']['id'],
                                    "product_id"  => $productData['id'],
                                    "name"        => $productData['title'],
                                    "thumbnail"   => $productData['img'],
                                    "price"       => $productData['price'],
                                    //add by jonzhang 2018-01-10
                                    "title"       => $shareEventData['title'] ?? '',
                                    "subtitle"    => $shareEventData['subtitle'] ?? '',
                                    "activityImg" => $shareEventData['act_img'] ?? '',
                                    "lowerPrice"  => $shareEventData['lower_price'] ?? 0,
                                    "attendCount" => $total,
                                    "buttonTitle" => $shareEventData['button_title'] ?? '',
                                    //add by jonzhang 2018-01-22
                                    "startTime"   => $shareEventData['start_time'] ?? 0,
                                    "endTime"     => $shareEventData['end_time'] ?? 0,
                                    "currentTime" => time()
                                ];
                                $i++;
                            }
                        } else {
                            $error[] = ['activityId' => $commendDetailItem['recommendation_id'], 'code' => $obj['errCode'], 'msg' => $obj['errMsg']];
                        }
                    }
                    //只推荐2,4享立减
                    if ($i % 2 != 0) {
                        unset($returnData['data'][$i - 1]);
                    }
                    $returnData['error'] = $error;
                }
            } else {
                $returnData['errCode'] = 1;
            }
        } else if ($commendData['errCode'] == 0 && empty($commendData['data'])) {
            $returnData['errCode'] = 1;
        }
        return $returnData;
    }
}