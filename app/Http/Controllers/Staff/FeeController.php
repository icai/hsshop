<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/5
 * Time: 14:15
 */

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Module\FeeModule;
use App\S\Fee\SelfOrderService;
use  App\Services\WeixinService;
use App\S\Fee\SelfInvoiceService;
use App\Services\UserService;
use App\Module\PayModule;
use App\S\Fee\SelfOrderDetailService;
use App\S\Foundation\RegionService;
use App\S\Weixin\ShopService;

class FeeController extends Controller
{
    /***
     * todo 服务列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 2018-07-11
     */
    public function serviceList()
    {
        return view('staff.fee.serviceList',[
            'title'     => '店铺管理',
            'sliderba' => 'orderlist',
        ]);
    }

    /***
     * todo 发票列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 2018-07-11
     */
    public function invoiceList()
    {
        return view('staff.fee.invoiceList',[
            'title'     => '店铺管理',
            'sliderba' => 'invoicelist',
        ]);
    }
    /**
     * todo 查询所有的续费订单
     * @param FeeModule $feeModule
     * @return array
     * @author 张国军 2018年07月05日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
     public function selectOrders(Request $request,FeeModule $feeModule,ShopService $shopService)
     {
         try
         {
             $returnData=["errCode"=>0,"errMsg"=>"","data"=>[]];
             $widName=$request->input('name')??'';
             $status=$request->input('status')??'';
             $where=[];
             if(isset($status) && $status != '')
             {
                 $status=intval($status);
                 $where['status']=$status;
             }
             $widArr=[];
             if(!empty($widName))
             {
                 //通过店铺名称查询出wid
                 //$storeResultData=(new WeixinService())->getListByCondition(['shop_name'=>$widName]);
                 $storeResultData = $shopService->getAllList(['shop_name'=>$widName]);
                 if(!empty($storeResultData[0]['data']))
                 {
                     foreach($storeResultData[0]['data'] as $widItem)
                     {
                         $widArr[]=$widItem['id'];
                     }
                 }
                 if(!empty($widArr))
                 {
                     $where['wids']=$widArr;
                 }
                 else
                 {
                     return $returnData;
                 }
             }
             return $feeModule->showOrders($where);
         }
         catch(\Exception $ex)
         {
             $returnData['errCode']=-1000;
             $returnData['errMsg']=$ex->getMessage();
             return $returnData;
         }
     }

    /**
     * todo 显示某个服务详情
     * @param Request $request
     * @param FeeModule $feeModule
     * @return array
     * @author 张国军 2018年07月05日
     */
     public function selectOneOrder(Request $request,FeeModule $feeModule)
     {
         $returnData=["errCode"=>0,"errMsg"=>"","data"=>[]];
         $orderId=$request->input('id')??0;
         $orderId=intval($orderId);
         if(empty($orderId))
         {
             $returnData['errCode']=-1001;
             $returnData['errMsg']="id为空";
             return $returnData;
         }
         $orderData=$feeModule->showOrders(['orderId'=>$orderId]);
         if($orderData['errCode']==0&&!empty($orderData['data']))
         {
             $returnData['data']=$orderData['data'][0];
             return $returnData;
         }
         else
         {
             return $orderData;
         }
     }

    /***
     * todo 更改服务订单状态
     * @param Request $request
     * @param SelfOrderService $selfOrderService
     * @return array
     * @author 张国军 2018年07月05日
     */
     public function updateOrderData(Request $request,SelfOrderService $selfOrderService)
     {
         $id=$request->input('id')??0;
         $status=$request->input('status');
         $id=intVal($id);
         if(empty($id))
         {
             $returnData['errCode']=-1001;
             $returnData['errMsg']="id为空";
             return $returnData;
         }
         if(empty($status))
         {
             $returnData['errCode']=-1002;
             $returnData['errMsg']="status为空";
             return $returnData;
         }
         if(!in_array($status,[1,3]))
         {
             $returnData['errCode']=-1003;
             $returnData['errMsg']="status数值不对";
             return $returnData;
         }
         $result=$selfOrderService->updateData($id,['status'=>$status]);
         //汇款成功，更改店铺的过期时间
         if($result['errCode']==0&&$status==1)
         {
             (new PayModule())->updateStoreExpireTime($id);
         }
         return $result;
     }

    /***
     * todo 查询所有的发票信息
     * @param Request $request
     * @param SelfinvoiceService $selfInvoiceService
     * @return array
     * @author 张国军 2018年07月06日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function selectInvoices(Request $request,SelfInvoiceService $selfInvoiceService,ShopService $shopService)
    {
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>[]];
        $widName=$request->input('name');
        $requestNo=$request->input('requestNo');
        $status=$request->input('status');
        $where=['current_status'=>0];
        $widArr=[];
        if(!empty($widName))
        {
            //通过店铺名称查询出wid
            //$storeResultData=(new WeixinService())->getListByCondition(['shop_name'=>$widName]);
            $storeResultData = $shopService->getAllList(['shop_name'=>$widName]);
            if(empty($storeResultData[0]['data']))
            {
                foreach($storeResultData[0]['data'] as $widItem)
                {
                    $widArr[]=$widItem['id'];
                }
            }
            if(!empty($widArr))
            {
                $where['wids']=$widArr;
            }
            else
            {
                return $returnData;
            }
        }
        if(!empty($requestNo))
        {
            $where['request_no']=$requestNo;
        }
        if(isset($status))
        {
            if($status!=999)
            $where['status']=$status;
        }
        $selfInvoiceData=$selfInvoiceService->getListByCondition($where);
        if($selfInvoiceData['errCode']==0&&!empty($selfInvoiceData['data']))
        {
            foreach($selfInvoiceData['data'] as &$item)
            {
                $item['create_time']=date('Y-m-d H:i:s',$item['create_time']);
                //分转化为元
                $item['amount']=sprintf('%.2f',$item['amount']/100);
                $item['typeName'] = "-";
                if (!empty($item['type']) && $item['type'] == 1) {
                    $item['typeName'] = "增值税普通发票(普票)";
                } else if (!empty($item['type']) && $item['type'] == 2) {
                    $item['typeName'] = "增值税专用发票(专票)";
                }

                $item['styleName'] = "-";
                if (!empty($item['style']) && $item['style'] == 1) {
                    $item['styleName'] = "纸质发票";
                } else if (!empty($item['style']) && $item['style'] == 2) {
                    $item['styleName'] = "电子发票";
                }

                $item['titleTypeName'] = "-";
                if (!empty($item['title_type']) && $item['title_type'] == 1) {
                    $item['titleTypeName'] = "企业单位";
                } else if (!empty($item['title_type']) && $item['title_type'] == 2) {
                    $item['titleTypeName'] = "个人/非企业单位";
                }

                $item['statusName'] = "-";
                if (isset($item['status']) && $item['status'] == 0) {
                    $item['statusName'] = "待开具";
                } else if (!empty($item['status']) && $item['status'] == 1) {
                    $item['statusName'] = "已开具";
                } else if (!empty($item['status']) && $item['status'] == 2) {
                    $item['statusName'] = "已邮寄";
                }

                //店铺名称
                $item['widName']='-';
                $uid=0;
                //通过wid 查询出店铺名称
                /*$storeData=(new WeixinService())->getStore($item['wid']);
                if($storeData['errCode']==0&&!empty($storeData['data']))
                {
                    $item['widName']=$storeData['data']['shop_name']??'';
                    $uid=$storeData['data']['uid']??0;
                }*/
                $storeData = $shopService->getRowById($item['wid']);
                if (!empty($storeData)) {
                    $item['widName'] = $storeData['shop_name'] ?? '';
                    $uid = $storeData['uid'] ?? 0;
                }

                //通过id 查询出手机号码
                if(!empty($uid))
                {
                    $userData = (new UserService())->getListByCondition(['id' => $uid]);
                    if ($userData['errCode'] == 0 && !empty($userData['data'])) {
                        $item['mphone'] = $userData['data'][0]['mphone']??'-';
                    }
                }
                $item['serviceName']="-";
                $orderId=$item['order_id'];
                $orderId=json_decode($orderId,true);
                if(empty($orderId))
                {
                    continue;
                }
                //续费服务
                $selfOrderDetailData=(new SelfOrderDetailService())->getListByCondition(['self_order_id'=>$orderId[0]]);
                if($selfOrderDetailData['errCode']==0&&!empty($selfOrderDetailData['data']))
                {
                    $product=$selfOrderDetailData['data'][0];
                    $item['serviceName']=$product['product_name'];
                    if($product['product_version_no']==1)
                    {
                        $item['serviceName'].="(基础版)";
                    }
                    else if($product['product_version_no']==2)
                    {
                        $item['serviceName'].="(高级版)";
                    }
                    else if($product['product_version_no']==3)
                    {
                        $item['serviceName'].="(至尊版)";
                    }
                }
            }
        }
        return $selfInvoiceData;
    }

    /***
     * todo 查询某个发票的明细
     * @param Request $request 请求参数对象
     * @param SelfinvoiceService $selfInvoiceService 发票对象
     * @return array
     * @author 张国军 2018年07月06日
     */
     public function selectOneInvoice(Request $request,SelfInvoiceService $selfInvoiceService)
     {
         $returnData=["errCode"=>0,"errMsg"=>"","data"=>[]];
         $id=$request->input('id')??0;
         $id=intval($id);
         if(empty($id))
         {
             $returnData["errCode"]=-1001;
             $returnData["errMsg"]="id为空";
             return $returnData;
         }
         $selfInvoiceData=$selfInvoiceService->getListByCondition(['id'=>$id,'current_status'=>0]);
         if($selfInvoiceData['errCode']==0&&!empty($selfInvoiceData['data']))
         {
             $invoiceItemData=$selfInvoiceData['data'][0];
             //分转化为元
             $invoiceItemData['amount']=sprintf('%.2f',$invoiceItemData['amount']/100);
             $invoiceItemData['typeName']="-";
             if(!empty($selfInvoiceData['data'][0]['type'])&&$selfInvoiceData['data'][0]['type']==1)
             {
                 $invoiceItemData['typeName']="增值税普通发票(普票)";
             }
             else if(!empty($selfInvoiceData['data'][0]['type'])&&$selfInvoiceData['data'][0]['type']==2)
             {
                 $invoiceItemData['typeName']="增值税专用发票(专票)";
             }

             $invoiceItemData['styleName']="-";
             $address='';
             if(!empty($selfInvoiceData['data'][0]['style'])&&$selfInvoiceData['data'][0]['style']==1)
             {
                 $invoiceItemData['styleName']="纸质发票";
                 $region = (new RegionService())->getListById([$invoiceItemData['province_id'], $invoiceItemData['city_id'], $invoiceItemData['area_id']]);
                 foreach($region as $addressItem)
                 {
                     $address.=$addressItem['title'];
                 }
             }
             else if(!empty($selfInvoiceData['data'][0]['style'])&&$selfInvoiceData['data'][0]['style']==2)
             {
                 $invoiceItemData['styleName']="电子发票";
             }

             $invoiceItemData['titleTypeName']="-";
             if(!empty($selfInvoiceData['data'][0]['title_type'])&&$selfInvoiceData['data'][0]['title_type']==1)
             {
                 $invoiceItemData['titleTypeName']="企业单位";
             }
             else if(!empty($selfInvoiceData['data'][0]['title_type'])&&$selfInvoiceData['data'][0]['title_type']==2)
             {
                 $invoiceItemData['titleTypeName']="个人/非企业单位";
             }

             $invoiceItemData['statusName']="-";
             if(isset($selfInvoiceData['data'][0]['status'])&&$selfInvoiceData['data'][0]['status']==0)
             {
                 $invoiceItemData['statusName']="待开具";
             }
             else if(!empty($selfInvoiceData['data'][0]['status'])&&$selfInvoiceData['data'][0]['status']==1)
             {
                 $invoiceItemData['statusName']="已开具";
             }
             else if(!empty($selfInvoiceData['data'][0]['status'])&&$selfInvoiceData['data'][0]['status']==2)
             {
                 $invoiceItemData['statusName']="已邮寄";
             }
             $invoiceItemData['create_time']=date('Y-m-d H:i:s',$invoiceItemData['create_time']);
             $invoiceItemData['detail_address']=$address.$invoiceItemData['detail_address'];
             $returnData['data']=$invoiceItemData;
             return $returnData;
         }
         else
         {
             return $selfInvoiceData;
         }
     }

    /***
     * todo 更改发票明细
     * @param Request $request
     * @param SelfinvoiceService $selfInvoiceService
     * @return array
     * @author 张国军 2018年07月06日
     */
    public function updateInvoiceData(Request $request,SelfInvoiceService $selfInvoiceService)
    {
        $returnData=["errCode"=>0,"errMsg"=>""];
        $id=$request->input('id')??0;
        $status=$request->input('status')??0;
        $uRemark=$request->input('remark');
        $expressId=$request->input('expressId')??0;
        //上传图片
        $invoiceImage=$request->file('invoiceImage');
        $status=intval($status);
        $id=intval($id);
        if(empty($id))
        {
            $returnData["errCode"]=-1001;
            $returnData["errMsg"]="id为空";
            return $returnData;
        }
        
        //查询该发票是否有备注
        $selfInvoiceData=$selfInvoiceService->getListByCondition(['id'=>$id,'current_status'=>0]);
        if($selfInvoiceData['errCode']==0&&empty($selfInvoiceData['data']))
        {
            $returnData["errCode"]=-1002;
            $returnData["errMsg"]="没有查询到数据";
            return $returnData;
        }
        else if($selfInvoiceData['errCode']<0)
        {
            return $selfInvoiceData;
        }
        $data=[];
        $requestNo=$selfInvoiceData['data'][0]['request_no'];
        $style=$selfInvoiceData['data'][0]['style'];
        //对发票备注进行累加
        if(!empty($selfInvoiceData['data'][0]['remark']))
        {
            $remark=$selfInvoiceData['data'][0]['remark'];
            $remark=json_decode($remark,true);
            if(!empty($uRemark)&&empty($remark))
            {
                $returnData["errCode"]=-1003;
                $returnData["errMsg"]="备注出现问题";
                return $returnData;
            }
            if(!empty($uRemark)&&is_array($remark)&&count($remark)>0)
            {
                array_push($remark,$uRemark);
                $remark=json_encode($remark);
                $data['remark']=$remark;
            }
        }
        else
        {
            if(!empty($uRemark))
            {
                $remark=[];
                array_push($remark,$uRemark);
                $data['remark']=json_encode($remark);
            }
        }

        if(!empty($expressId))
        {
            $data['express_no']=$expressId;
        }
        if(!empty($status))
        {
            $data['status']=$status;
            //已开具的电子发票需要上传
            if($data['status']==1&&$style==2)
            {
                if ($invoiceImage->isValid())
                {
                    //获取文件格式
                    $fileType = $invoiceImage->getMimeType();
                    if ($fileType != "application/pdf") {
                        $returnData["errCode"] = -1004;
                        $returnData["errMsg"] = "文件格式不合法";
                        return $returnData;
                    }

                    $size=$invoiceImage->getSize();
                    //设置上传文件的大小
                    if($size > 1*1024*1024)
                    {
                        $returnData["errCode"] = -1005;
                        $returnData["errMsg"] = "上传文件不能超过1M";
                        return $returnData;
                    }
                    //文件的存放路径
                    $filePath = storage_path("uploads/invoices/"). date("Y");
                    if (!file_exists($filePath)) {
                        @mkdir($filePath, 0777, true);
                    }

                    $filename=$invoiceImage->getClientOriginalName();//获取文件名称
                    //$realPath = $invoiceImage->getRealPath();   //临时文件的绝对路径
                    //$type = $invoiceImage->getClientMimeType();     // image/jpeg
                    $ext = $invoiceImage->getClientOriginalExtension(); // 扩展名
                    //$targetFileName=$requestNo.'.'.$ext;
                    $targetFileName=$filename;
                    //目标文件存在的话则删除它
                    $targetFilePath=$filePath . "/" .$targetFileName;
                    if(file_exists($targetFilePath))
                    {
                        @unlink($targetFilePath);
                    }
                    try
                    {
                        $invoiceImage->move($filePath, $targetFileName);
                    }
                    catch (\Exception $ex)
                    {
                        $returnData["errCode"] = -1005;
                        $returnData["errMsg"] = "上传发票失败";
                        return $returnData;
                    }
                    $data['invoice_image'] =$targetFilePath;
                }
            }
        }
        if(!empty($data))
        {
            $returnData=$selfInvoiceService->updateData($id, $data);
        }
        return $returnData;
    }
}