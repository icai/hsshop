<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/3
 * Time: 16:52
 */

namespace App\Http\Controllers\Merchants;
use App\Http\Controllers\Controller;
use App\Services\Permission\WeixinRoleService;
use Illuminate\Http\Request;
use App\S\Fee\SelfProductService;
use App\S\Fee\SelfInvoiceService;
use App\Module\FeeModule;
use App\S\Fee\SelfOrderService;
use OrderCommon;
//use App\Services\WeixinService;
use App\S\Foundation\RegionService;
use App\S\Weixin\ShopService;

class FeeController extends Controller
{
    /***
     * todo 服务列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 2018-07-05
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function serviceList(ShopService $shopService)
    {
        $widName="-";
        //查询店铺名称
        //$weixinData=(new WeixinService())->getStore(session('wid'));
        $weixinData = $shopService->getRowById(session('wid'));
        if(!empty($weixinData))
        {
            //$widName=$weixinData['data']['shop_name'];
            $widName = $weixinData['shop_name'];
        }
        return view('merchants.capital.serviceList',[
            'title'=>'服务列表',
            'leftNav'=>'serviceList',
            'slidebar'=>'serviceList',
            'widName'=>$widName
        ]);
    }

    /***
     * todo 服务详情
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 2018-07-05
     */
    public function serviceDetail(ShopService $shopService)
    {
        $widName="-";
        //查询店铺名称
        //$weixinData=(new WeixinService())->getStore(session('wid'));
        $weixinData = $shopService->getRowById(session('wid'));
        if(!empty($weixinData))
        {
            //$widName=$weixinData['data']['shop_name'];
            $widName = $weixinData['shop_name'];
        }
        //判断如果到期时间大于30天,不能进行续费
        $is_over_month = 0;
        $storeData=(new WeixinRoleService())->getListByCondition(['wid'=>session('wid')]);
        if($storeData['errCode']==0&&!empty($storeData['data'])) {
            $endTime = $storeData['data'][0]['end_time'];
            $diffDays = (strtotime($endTime)-time())/24/3600;
            if ($diffDays>30) {
                $is_over_month = 1;
                error('您店铺到期时间大于30天，如需要再次续费请联系客服!');
            }
        }
        return view('merchants.capital.serviceDetail',[
            'title'=>'服务详情',
            'leftNav'=>'serviceDetail',
            'slidebar'=>'serviceList',
            'widName'=>$widName,
            'isOverMonth'=>$is_over_month
        ]);
    }

    /***
     * todo 支付列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author  张国军 2018年07月18日
     */
    public function payList()
    {
        return view('merchants.capital.payList',[
            'title'=>'支付详情',
            'leftNav'=>'serviceDetail',
            'slidebar'=>'serviceList'
        ]);
    }

    /**
     * todo 支付完成
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 2018年07月18日
     */
    public function payFinish()
    {
        return view('merchants.capital.payFinish',[
            'title'=>'支付完成',
            'leftNav'=>'serviceDetail',
            'slidebar'=>'serviceList'
        ]);
    }

    /***
     * todo 服务列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 服务列表
     */
    public function orderList()
    {
        return view('merchants.capital.orderList',[
            'title'=>'服务列表',
            'leftNav'=>'printInvoice',
            'slidebar'=>'serviceList'
        ]);
    }

    /***
     * todo 发票列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 2018-07-05
     */
    public function invoiceList()
    {
        return view('merchants.capital.invoiceList',[
            'title'=>'发票列表',
            'leftNav'=>'invoiceList',
            'slidebar'=>'invoiceList'
        ]);
    }

    /***
     * todo 打印发票
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 2018-07-05
     */
    public function printInvoice()
    {
        $regions = (new RegionService())->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        $regionList=[];
        foreach($regions as $value){
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];
        return view('merchants.capital.printInvoice',[
            'title'=>'开具发票',
            'leftNav'=>'printInvoice',
            'slidebar'=>'invoiceList',
            'regionList'   => $regionList,
            'provinceList' => $provinceList,
            'regions_data' => json_encode($regionList),
        ]);
    }

    /***
     * todo 支付宝支付页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军 2018-07-29
     */
    public  function aliPayPage()
    {
        return view('merchants.capital.aliPayPage',[
            'title'=>'发票列表',
            'leftNav'=>'invoiceList',
            'slidebar'=>'invoiceList'
        ]);
    }


    /**
     * todo 查询自营服务
     * @param Request $request 请求参数
     * @param SelfProductService $selfProductService 自营服务对象
     * @return array
     * @author 张国军 2018-07-03
     */
    public function selectSelfProducts(SelfProductService $selfProductService)
    {
        //$returnData=["errCode"=>0,"errMSg"=>"","data"=>[]];
        $result=$selfProductService->getListByCondition(['current_status'=>0],'id','desc',15);
        if($result['errCode']==0&&!empty($result['data']))
        {
            foreach($result['data'] as &$item)
            {
                $item['versionName']="-";
                if($item['version_no']==1)
                {
                    $item['versionName']="基础版";
                }
                else if($item['version_no']==2)
                {
                    $item['versionName']="高级版";
                }
                else if($item['version_no']==3)
                {
                    $item['versionName']="至尊版";
                }
                //分转化为元
                $item['price']=sprintf('%.2f',($item['price']/100));
            }
        }
        return $result;
    }

    /**
     * todo 查询某个自营服务
     * @param Request $request 请求参数
     * @param SelfProductService $selfProductService 自营服务对象
     * @return array
     * @author 张国军 2018-07-03
     */
    public function selectOneSelfProduct(Request $request,SelfProductService $selfProductService)
    {
        $returnData=["errCode"=>0,"errMSg"=>"","data"=>[]];
        $id=$request->input('id')??0;
        if(empty($id))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $selfProductData=$selfProductService->getListByCondition(['current_status'=>0,'id'=>$id]);
        if($selfProductData['errCode']==0&&!empty($selfProductData['data']))
        {
            $product=$selfProductData['data'][0];
            if($product['version_no']==1)
            {
                $product['versionName']="基础版";
            }
            else if($product['version_no']==2)
            {
                $product['versionName']="高级版";
            }
            else if($product['version_no']==3)
            {
                $product['versionName']="至尊版";
            }
            //分转化为元
            $product['price']=sprintf('%.2f',($product['price']/100));
            $returnData['data']=$product;
            return $returnData;
        }
        return $selfProductData;
    }

    /***
     * todo 查询店铺下的所有发票信息
     * @param SelfInvoiceService $selfInvoiceService 发票对象
     * @return array
     * @author 张国军 2018年07月04日
     */
    public function selectInvoices(SelfInvoiceService $selfInvoiceService)
    {
        $returnData=["errCode"=>0,"errMSg"=>"","data"=>[]];
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="登录超时";
            return $returnData;
        }
        $result= $selfInvoiceService->getListByCondition(['wid'=>$wid,'current_status'=>0],'id','desc',15);
        if($result['errCode']==0&&!empty($result['data']))
        {
            foreach($result['data'] as &$item)
            {
                //分转化为元
                $item['amount']=sprintf('%.2f',$item['amount']/100);
                if($item['type']==1)
                {
                    $item['typeName']="普通发票";
                }
                else if($item['type']==2)
                {
                    $item['typeName']="增值税专用发票";
                }
                if($item['style']==1)
                {
                    $item['styleName']="纸质发票";
                }
                else if($item['style']==2)
                {
                    $item['styleName']="电子发票";
                }
                if($item['status']==0)
                {
                    $item['statusName']="待开具";
                }
                else if($item['status']==1)
                {
                    $item['statusName']="已开具";
                }
                else if($item['status']==2)
                {
                    $item['statusName']="已邮寄";
                }
                if(empty($item['express_no']))
                {
                    $item['express_no']="-";
                }
                $item['invoiceLoad']="-";
                //电子发票下载
                if($item['style']==2&&$item['status']==1)
                {
                    $item['invoiceLoad']='<a class="download" href="/merchants/fee/invoice/download?fileName='.$item['request_no'].'" data-file="">下载</a>';
                }
                $item['create_time']=date('Y-m-d H:i:s',$item['create_time']);
            }
        }
        return $result;
    }

    /***
     * todo 查询某个订单对应的发票信息
     * @param Request $request 请求对象
     * @param SelfInvoiceService $selfInvoiceService 发票对象
     * @return array
     * @author 张国军 2018年07月04日
     */
    public function selectOneInvoice(Request $request,SelfInvoiceService $selfInvoiceService)
    {
        $returnData=["errCode"=>0,"errMSg"=>"","data"=>[]];
        $id=$request->input('id')??0;
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="登录超时";
            return $returnData;
        }
        if(empty($id))
        {
            $returnData['errCode']=-1002;
            $returnData['errMsg']="id为空";
            return $returnData;
        }
        $result= $selfInvoiceService->getListByCondition(['id'=>$id,'current_status'=>0],'id','desc',15);
        if($result['errCode']==0&&!empty($result['data']))
        {
            foreach($result['data'] as &$item)
            {
                //分转化为元
                $item['amount']=sprintf('%.2f',$item['amount']/100);
                if($item['type']==1)
                {
                    $item['typeName']="普通发票";
                }
                else if($item['type']==2)
                {
                    $item['typeName']="增值税专用发票";
                }
                $address='';
                if($item['style']==1)
                {
                    $item['styleName']="纸质发票";
                    //地址详情
                    $region = (new RegionService())->getListById([$item['province_id'], $item['city_id'], $item['area_id']]);
                    foreach($region as $addressItem)
                    {
                        $address.=$addressItem['title'];
                    }
                }
                else if($item['style']==2)
                {
                    $item['styleName']="电子发票";
                }
                if($item['status']==0)
                {
                    $item['statusName']="待开具";
                }
                else if($item['status']==1)
                {
                    $item['statusName']="已开具";
                }
                else if($item['status']==2)
                {
                    $item['statusName']="已邮寄";
                }
                if(empty($item['express_no']))
                {
                    $item['express_no']="-";
                }
                $item['create_time']=date('Y-m-d H:i:s',$item['create_time']);
                $item['detail_address']=$address.$item['detail_address'];
            }
            $returnData['data']=$result['data'][0];
        }
        return $returnData;
    }

    /***
     * todo 开具发票
     * @param Request $request 请求参数
     * @param SelfInvoiceService $selfInvoiceService 发票对象
     * @return array
     * @author 张国军 2018年07月04日
     */
    public function insertInvoice(Request $request,SelfInvoiceService $selfInvoiceService,SelfOrderService $selfOrderService)
    {
        $returnData=["errCode"=>0,"errMSg"=>""];
        //发票类型
        $type=$request->input('type');
        //发票性质
        $style=$request->input('style');
        //抬头类型
        $titleType=$request->input('titleType');
        //发票抬头
        $title=$request->input('title');
        //公司地址
        $companyAddress=$request->input('companyAddress');
        //公司联系方式
        $companyTelephone=$request->input('companyTelephone');
        //开户行账号
        $depositBankAccount=$request->input('depositBankAccount');
        //开户行地址
        $depositBankAddress=$request->input('depositBankAddress');
        //收件人
        $receiver=$request->input('receiver');
        //联系电话
        $telephone=$request->input('telephone');
        //收货地址
        $provinceId=$request->input('provinceId');
        $cityId=$request->input('cityId');
        $areaId=$request->input('areaId');
        //详细地址
        $detailAddress=$request->input('detailAddress');
        //纳税人识别号
        $taxNumber=$request->input('taxNumber');
        //发票金额
        $amount=$request->input('amount');
        //订单编号
        $orderIds=$request->input('orderIds');
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="登录超时";
            return $returnData;
        }
        $errMsg='';
        if(empty($orderIds))
        {
            $errMsg.="订单编号不能为空";
        }
        if(empty($title))
        {
            $errMsg.="发票抬头不能为空";
        }
        if(empty($style))
        {
            $errMsg.="发票性质不能为空";
        }
        if(empty($type))
        {
            $errMsg.="发票类型不能为空";
        }
        if(empty($titleType))
        {
            $errMsg.="抬头类型不能为空";
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1002;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $orderIds=json_decode($orderIds,true);
        if(empty($orderIds))
        {
            $returnData['errCode']=-1003;
            $returnData['errMsg']="订单格式不对";
            return $returnData;
        }

        //判断该订单是否开过发票
        $invalidOrderId=[];
        $msg="";
        $amount=0;
        foreach($orderIds as $item)
        {
            $result= $selfOrderService->getListByCondition(['id'=>$item,'current_status'=>0]);
            if($result['errCode']==0&&!empty($result['data']))
            {
                 if(!empty($result['data'][0]['invoice_id']))
                 {
                     $invalidOrderId[] = $result['data'][0]['invoice_id'];
                 }
                 $amount+=$result['data'][0]['pay_amount'];
            }
            else if($result['errCode']<0)
            {
                $msg.="订单编号为:".$item."错误信息:".$result['errMsg'];
            }
        }
        if(!empty($invalidOrderId))
        {
            $returnData['errCode']=-1004;
            $returnData['errMsg']="订单已开过发票,订单号为:".json_encode($invalidOrderId);
            return $returnData;
        }
        if(strlen($msg)>0)
        {
            $returnData['errCode']=-1005;
            $returnData['errMsg']=$msg;
            return $returnData;
        }

        $requestNo=OrderCommon::createInvoiceRequestNumber();
        $data=['request_no'=>$requestNo,'wid'=>$wid,'order_id'=>json_encode($orderIds),'type'=>$type,'style'=>$style,'title'=>$title,'title_type'=>$titleType,'amount'=>$amount];
        if(!empty($companyAddress))
        {
            $data['company_address']=$companyAddress;
        }
        if(!empty($companyTelephone))
        {
            $data['company_telephone']=$companyTelephone;
        }
        if(!empty($depositBankAccount))
        {
            $data['deposit_bank_account']=$depositBankAccount;
        }
        if(!empty($depositBankAddress))
        {
            $data['deposit_bank_address']=$depositBankAddress;
        }
        if(!empty($receiver))
        {
            $data['receiver']=$receiver;
        }
        if(!empty($telephone))
        {
            $data['telephone']=$telephone;
        }
        if(!empty($provinceId))
        {
            $data['province_id']=$provinceId;
        }
        if(!empty($cityId))
        {
            $data['city_id']=$cityId;
        }
        if(!empty($areaId))
        {
            $data['area_id']=$areaId;
        }
        if(!empty($detailAddress))
        {
            $data['detail_address']=$detailAddress;
        }
        if(!empty($taxNumber))
        {
            $data['tax_number']=$taxNumber;
        }
        //添加发票
        $invoiceData=$selfInvoiceService->insertData($data);
        if($invoiceData['errCode']==0)
        {
            foreach($orderIds as $orderId)
            {
                //更改订单数据
                $selfOrderService->updateData($orderId, ['is_invoice' => 1,'invoice_id' => $invoiceData['data']]);
            }
        }
        return $invoiceData;
    }

    /***
     * todo 创建服务订单
     * @param Request $request 请求对象
     * @return array
     * @author 张国军 2018年07月04日
     */
    public function submitSelfOrder(Request $request,SelfOrderService $selfOrderService,FeeModule $feeModule)
    {
        $returnData=["errCode"=>0,"errMSg"=>""];
        $productId=$request->input('productId');
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="登录超时";
            return $returnData;
        }
        if(empty($productId))
        {
            $returnData["errCode"]=-1002;
            $returnData["errMsg"]="服务id为空";
            return $returnData;
        }
        try
        {
            //查询该店铺是否存在未支付订单
            //$selfOrderService=$selfOrderService->getListByCondition(['wid'=>$wid,'current_status'=>0,'status'=>0]);
            //if($selfOrderService['errCode']==0&&!empty($selfOrderService['data']))
            //{
               // $returnData["errCode"]=-1003;
               // $returnData["errMsg"]="存在待支付的续费服务";
               // return $returnData;
            //}
            //下单
            return $feeModule->submitSelfOrder(["wid" => $wid, "productId" => $productId]);
        }
        catch(\Exception $ex)
        {
            $returnData['errCode']=-1000;
            $returnData['errMsg']=$ex->getMessage();
            return $returnData;
        }
    }

    /***
     * todo 服务列表
     * @return array
     * @author 张国军 2018-07-05
     */
    public  function  selectOrders(Request $request ,FeeModule $feeModule)
    {
        $returnData=["errCode"=>0,"errMSg"=>"","data"=>[]];
        $isInvoice=$request->input('isInvoice');
        $status=$request->input('status');
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="登录超时";
            return $returnData;
        }
        try
        {
            $where['wid']=$wid;
            if(isset($isInvoice))
            {
                $where['isInvoice']=$isInvoice;
            }
            if(isset($status))
            {
                $where['status']=$status;
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

    /***
     * todo 查询某个订单信息
     * @param Request $request
     * @param FeeModule $feeModule
     * @return array
     * @author 张国军 2018年07月17日
     */
    public  function  selectOneOrder(Request $request,FeeModule $feeModule)
    {
        $returnData=["errCode"=>0,"errMSg"=>"","data"=>[]];
        $id = $request->input('id');
        $id=intval($id);
        if(empty($id))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="订单编号为空";
            return $returnData;
        }
        try
        {
            $orderData= $feeModule->showOrders(['orderId'=>$id]);
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
        catch(\Exception $ex)
        {
            $returnData['errCode']=-1000;
            $returnData['errMsg']=$ex->getMessage();
            return $returnData;
        }
    }

    /***
     * todo 服务列表
     * @return array
     * @author 张国军 2018-07-05
     */
    public  function  deleteOrder(Request $request,SelfOrderService $selfOrderService)
    {
        $returnData=["errCode"=>0,"errMSg"=>""];
        $id=$request->input('id')??0;
        $id=intval($id);
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="登录超时";
            return $returnData;
        }
        if(empty($id))
        {
            $returnData['errCode']=-1002;
            $returnData['errMsg']="id为空";
            return $returnData;
        }
        try
        {
            return $selfOrderService->delete($id);
        }
        catch(\Exception $ex)
        {
            $returnData['errCode']=-1000;
            $returnData['errMsg']=$ex->getMessage();
            return $returnData;
        }
    }

    /***
     * todo 获取汇款支付账户信息
     * @return array
     * @author 张国军 2018年07月13日
     */
    public function waiPayForRemitConfig()
    {
        $returnData=["errCode"=>0,"errMSg"=>"","data"=>[]];
        $returnData['data']=["receiveCompany"=>config('app.receive_company'),"receiveBank"=>config('app.receive_bank'),"receiveAccount"=>config('app.receive_account')];
        return $returnData;
    }

    /***
     * todo 汇款支付
     * @param Request $request
     * @param SelfOrderService $selfOrderService
     * @return array
     * @author 张国军  2018年07月13日
     */
    public function waitPayForRemit(Request $request,SelfOrderService $selfOrderService)
    {
        $returnData=["errCode"=>0,"errMSg"=>""];
        $orderId=$request->input('orderId');
        $orderId=intval($orderId);
        if(empty($orderId))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="订单编号为空";
            return $returnData;
        }
        //查询订单状态 是否可以支付
        $selfOrderData=$selfOrderService->getListByCondition(['id'=>$orderId,'current_status'=>0]);
        if($selfOrderData['errCode']==0&&empty($selfOrderData['data']))
        {
            $returnData["errCode"]=-1002;
            $returnData["errMsg"]="订单不存在";
            return $returnData;
        }
        else if($selfOrderData['errCode']<0)
        {
            return $selfOrderData;
        }
        if(isset($selfOrderData['data'][0]['status'])&&$selfOrderData['data'][0]['status']!=0)
        {
            $returnData["errCode"]=-1003;
            $returnData["errMsg"]="该订单不是待支付订单，不能够进行支付";
            return $returnData;
        }
        // status为2表示待审核  pay_way为3表示汇款支付
        return $selfOrderService->updateData($orderId,['pay_way'=>3,'status'=>2]);
    }

    /**
     * todo 发票下载
     * @param Request $request
     * @author 张国军 2018年07月16日
     */
    public function download(Request $request,SelfInvoiceService $selfInvoiceService)
    {
        $fileName=$request->input('fileName');
        if(empty($fileName))
        {
            error('文件名为空');
        }
        $pathToFile="";
        //$pathToFile=storage_path("uploads\invoices")."\\".date("Y");
        $selfInvoiceData=$selfInvoiceService->getListByCondition(['request_no'=>$fileName]);
        if($selfInvoiceData['errCode']==0&&!empty($selfInvoiceData['data']))
        {
            $pathToFile=$selfInvoiceData['data'][0]['invoice_image'];
        }
        else
        {
            error('该发票不存在');
        }
        if(empty($pathToFile))
        {
            error('没有找到发票的存在位置');
        }
        if(!file_exists($pathToFile))
        {
            error('该文件服务端不存在');
        }
        $downLoadFileName=basename($pathToFile);
        return response()->download($pathToFile, $downLoadFileName);
    }
}