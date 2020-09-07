<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/3
 * Time: 17:12
 */

namespace App\Module;
use App\S\Fee\SelfOrderService;
use App\S\Fee\SelfOrderDetailService;
use App\S\Fee\SelfProductService;
use DB;
use OrderCommon;
use App\Lib\BLogger;
use App\Services\WeixinService;
use App\Services\UserService;
use App\Jobs\SelfOrderCancel;
use App\S\Weixin\ShopService;

class FeeModule
{
    /***
     * todo 创建服务订单
     * @param array $data
     * @return array
     * @author 张国军 2018年07月04日
     */
      public function submitSelfOrder($data=[])
      {
          $returnData=["errCode"=>0,"errMsg"=>"","data"=>0];
          if(empty($data))
          {
              $returnData["errCode"]=-101;
              $returnData["errMsg"]="数据为空";
              return $returnData;
          }
          $wid=$data["wid"];
          $productId=$data["productId"];
          $errMsg="";
          if(empty($wid))
          {
              $errMsg.="wid为空";
          }
          if(empty($productId))
          {
              $errMsg.="服务id为空";
          }
          if(strlen($errMsg)>0)
          {
              $returnData["errCode"]=-103;
              $returnData["errMsg"]=$errMsg;
              return $returnData;
          }
          //开始事务
          DB::beginTransaction();
          try
          {
              $selfProductData=(new SelfProductService())->getListByCondition(["id"=>$productId]);
              if($selfProductData["errCode"]<0)
              {
                  return $selfProductData;
              }
              else if($selfProductData["errCode"]==0&&empty($selfProductData["data"]))
              {
                  $returnData["errCode"]=-102;
                  $returnData["errMsg"]="商品Id不存在";
                  return $returnData;
              }
              $errMsg="";
              if(empty($selfProductData['data'][0]["title"]))
              {
                  $errMsg.="服务名称为空";
              }
              if(empty($selfProductData['data'][0]["price"]))
              {
                  $errMsg.="服务金额为空";
              }
              if(strlen($errMsg)>0)
              {
                  $returnData["errCode"]=-104;
                  $returnData["errMsg"]=$errMsg;
                  return $returnData;
              }
              $orderData["wid"]=$wid;
              $orderData["order_no"]=OrderCommon::createServiceOrderNumber();
              $orderData["products_amount"]=$selfProductData["data"][0]["price"];
              $orderData["pay_amount"]=$orderData["products_amount"];
              $returnOrderData=(new SelfOrderService())->insertData($orderData);
              if($returnOrderData["errCode"]<0)
              {
                  BLogger::getLogger("info")->info('wid:'.$wid.'服务订单:'.json_encode($returnOrderData));
                  throw new \Exception("创建服务订单失败");
              }
              $orderDetailData["self_order_id"]=$returnOrderData["data"];
              $orderDetailData["product_id"]=$productId;
              $orderDetailData["product_name"]=$selfProductData['data'][0]["title"];
              $orderDetailData["product_version_no"]=$selfProductData['data'][0]["version_no"]??1;
              $orderDetailData["product_price"]=$selfProductData['data'][0]["price"];
              $orderDetailData["product_year"]=$selfProductData['data'][0]["year"]??1;
              $orderDetailData["num"]=1;
              $returnOrderDetailData=(new SelfOrderDetailService())->insertData($orderDetailData);
              if($returnOrderDetailData["errCode"]<0)
              {
                  BLogger::getLogger("info")->info('wid:'.$wid.'服务订单明细:'.json_encode($returnOrderDetailData));
                  throw new \Exception("创建服务订单明细失败");
              }
              dispatch((new SelfOrderCancel($returnOrderData["data"]))->onQueue('cancelSelfOrder')->delay(1800));
              //提交事务
              DB::commit();
          }
          catch(\Exception $ex)
          {
              //事务回滚
              DB::rollback();
              $message=$ex->getMessage();
              $returnData['errCode']=-100;
              $returnData['errMsg']=$message;
              return $returnData;
          }
          //订单编号
          $returnData["data"]=$returnOrderData['data'];
          return $returnData;
      }

    /***
     * todo 显示服务列表
     * @param $data 查询条件
     * @return array
     * @author 张国军 2018年07月05日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
      public function showOrders($data=[])
      {
          //$returnData=["errCode"=>0,"errMsg"=>"","data"=>[]];
          $where=['current_status'=>0];
          if(isset($data['wids'])&&is_array($data['wids'])&&count($data['wids'])>0)
          {
              $where['wids']=$data['wids'];
          }
          if(isset($data['orderNo'])&&!empty($data['orderNo']))
          {
              $where['order_no']=$data['orderNo'];
          }
          if(isset($data['orderId'])&&!empty($data['orderId']))
          {
              $where['id']=$data['orderId'];
          }
          if(isset($data['status']))
          {
              $where['status']=$data['status'];
          }
          if(isset($data['wid'])&&!empty($data['wid']))
          {
              $where['wid']=$data['wid'];
          }
          if(isset($data['isInvoice']))
          {
              $where['is_invoice']=$data['isInvoice'];
          }
          $orderData=(new SelfOrderService())->getListByCondition($where);
          if($orderData["errCode"]==0&&!empty($orderData["data"]))
          {
              foreach($orderData["data"] as &$item)
              {
                  //分转化为元
                  $item['pay_amount']=sprintf('%.2f',$item['pay_amount']/100);
                  $item['products_amount']=sprintf('%.2f',$item['products_amount']/100);
                  //支付方式名称
                  $item['payName']="-";
                  if($item['pay_way']==1)
                  {
                      $item['payName']="微信支付";
                  }
                  else if($item['pay_way']==2)
                  {
                      $item['payName']="支付宝";
                  }
                  else if($item['pay_way']==3)
                  {
                      $item['payName']="汇款支付";
                  }

                  //状态名称
                  $item['statusName']="-";
                  if($item['status']==-1)
                  {
                      $item['statusName']="订单关闭";
                  }
                  else if($item['status']==0)
                  {
                      $item['statusName']="待支付";
                  }
                  else if($item['status']==1)
                  {
                      $item['statusName']="支付成功";
                  }
                  else if($item['status']==2)
                  {
                      $item['statusName']="待审核";
                  }
                  else if($item['status']==3)
                  {
                      $item['statusName']="支付失败";
                  }
                  $item['create_time']=date("Y-m-d H:i:s",$item['create_time']);
                  //$item['pay_amount']=$item['pay_amount']."元";
                  $item['serviceVersion']="-";
                  $item['serviceTime']="-";

                  //店铺名称
                  $item['widName']='';
                  $uid=0;
                  //通过wid 查询出店铺名称
                  //$storeData=(new WeixinService())->getStore($item['wid']);
                  $shopService = new ShopService();
                  $storeData = $shopService->getRowById($item['wid']);
                  if(!empty($storeData))
                  {
                      $item['widName']=$storeData['shop_name']??'';
                      $uid=$storeData['uid']??0;
                  }
                  $orderDetailData=(new SelfOrderDetailService())->getListByCondition(['self_order_id'=>$item['id']]);
                  if($orderDetailData['errCode']==0&&!empty($orderDetailData['data']))
                  {
                      $serviceName=$orderDetailData['data'][0]['product_name']??'微商城';
                      $versionName="无";
                      if(!empty($orderDetailData['data'][0]['product_version_no'])&&$orderDetailData['data'][0]['product_version_no']==1)
                      {
                          $versionName="基础版";
                      }
                      else if(!empty($orderDetailData['data'][0]['product_version_no'])&&$orderDetailData['data'][0]['product_version_no']==2)
                      {
                          $versionName="高级版";
                      }
                      else if(!empty($orderDetailData['data'][0]['product_version_no'])&&$orderDetailData['data'][0]['product_version_no']==3)
                      {
                          $versionName="至尊版";
                      }
                      $productYear=$orderDetailData['data'][0]['product_year']??'-';
                      $item['serviceTime']=$productYear.'年';
                      $item['serviceVersion']=$serviceName."(".$versionName.")";
                  }
                  $item['mphone']="-";
                  //通过id 查询出手机号码
                  if(!empty($uid))
                  {
                      $userData = (new UserService())->getListByCondition(['id' => $uid]);
                      if ($userData['errCode'] == 0 && !empty($userData['data'])) {
                          $item['mphone'] = $userData['data'][0]['mphone']??'-';
                      }
                  }

              }
          }
          return $orderData;
      }

    /***
     * todo 未支付订单 在超期后 关闭订单 队列使用
     * @param int $orderId
     * @return array
     * @author 张国军 2018-07-24
     */
      public function cancelSelfOrderForExpire($orderId=0)
      {
          $returnData=["errCode"=>0,"errMsg"=>""];
          if(empty($orderId))
          {
              $returnData['errCode']=-201;
              $returnData['errMsg']="订单编号为空";
              return $returnData;
          }
          $selfOrderService=new SelfOrderService();
          $orderData=$selfOrderService->getListByCondition(['id'=>$orderId,'current_status'=>0]);
          if($orderData["errCode"]==0&&!empty($orderData["data"]))
          {
                if($orderData["data"][0]['status']==0)
                {
                    return $selfOrderService->updateData($orderId,['status'=>-1]);
                }
          }
          return $returnData;
      }
}