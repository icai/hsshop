<?php

namespace App\Http\Controllers\Merchants;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\S\Message\MessageTemplateService;
use Validator;
use App\S\WXXCX\WXXCXCollectFormIdService;
use App\S\Message\MessageTemplateLogService;
use WeixinService;
use App\S\Weixin\ShopService;

class MessageController extends Controller
{

	  /**
     * @return void
     */
    public function __construct()
    {
        $this->leftNav = 'marketing';

    }

    /**
     * 消息模板列表页
     * @author wuxiaoping <2018.01.18>
     * @return [type] [description]
     */
   	public function index(Request $request)
   	{
        $type = $request->input('type') ?? 1;
     		$wid = session('wid');
     		$messageTempService = new MessageTemplateService();
     		list($list,$indexPage) = $messageTempService->getAllList($wid);
        if ($list['data']) {
            foreach ($list['data'] as $key => &$value) {
                $value['content']       = json_decode($value['content'],true);
            }
        }
        
        // 消息模板发送记录
        $messageTemplateLogService = new MessageTemplateLogService();
        list($recordList,$recordPage) = $messageTemplateLogService->getAllList($wid);

        if ($recordList['data']) {
            foreach ($recordList['data'] as $key => &$value) {
              $info = $messageTempService->getRowById($value['message_template_id']);
              $value['template_name'] = $info['template_name'];
              $value['type'] = $info['type'];
            }
        }

        return view('merchants.message.index',[
          'title'      => '消息模板列表',
          'leftNav'    =>  $this->leftNav,
          'slidebar'   => 'index',
          'list'       =>  $list['data'],
          'indexPage'  =>  $indexPage,
          'recordList' =>  $recordList['data'],
          'recordPage' =>  $recordPage,
          'type'       =>  $type
          
     		]);
   	}


   	/**
   	 * 处理添加、编辑消息模板
   	 * @author wuxiaoping <2018.01.18>
   	 * @param  Request $request [description]
   	 * @return [type]           [description]
     * @update 梅杰 2018年8月8日 暂时隐藏小程序跳转路径
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
   	 */
   	public function save(Request $request,ShopService $shopService)
   	{
        $input = $request->input();
        $id = $input['id'] ?? 0;
        $wid = session('wid');
        //$storeInfo=WeixinService::init('id',$wid)->where(['id'=>$wid])->getInfo();
        $storeInfo = $shopService->getRowById($wid);
        $messageTempService = new MessageTemplateService();
        if ($request->isMethod('post')) {
       			$rule = [
                'tempName'    => 'required',
                'remark'      => 'required',
//                'url'         => 'required'
       			];

       			$messages = [
               'tempName.required' => '模板名称不能为空',
               'remark.required'   => '备注不能为空',
//               'url.required'      => '请选择小程序链接地址'
       			];
       			$validator = Validator::make($input,$rule,$messages);
       			if ($validator->fails()) {
       				 error($validator->errors()->first());
       			}
            $type = $input['type'] ?? 0;
            $source = $input['source'] ?? 0;  // 0-表示小程序  1-表示公众号
            $content = $this->validatorContent($type,$input,$source);
            $data['type']          = $type;
            $data['template_name'] = $input['tempName'];
            $data['remark']        = $input['remark'];
            $data['url']           = $input['url'];
            $data['url_title']     = $input['url_title'] ?? '';
            $data['content']       = $content;
            $data['resource']      = 0;
            if ($source) {
                $data['resource'] = 1;  //表示公众号
            }

       			if ($id) {
       				 $rs = $messageTempService->update($id,$data);
       			}else {
       				 $data['wid'] = session('wid');
       				 $rs = $messageTempService->add($data);
       			}
       			if ($rs) {
       				 success();
       			}
       			error();
   		  }

     		$data = [];
     		if ($id) {
     			  $data = $messageTempService->getRowById($id);
     		}

     		return view('merchants.message.save',[
     			'title'	   => $id ? '编辑消息模板' : '添加消息模板',
     			'leftNav'  => $this->leftNav,
    			'slidebar' => 'save',
    			'data'     => $data,
          'storeInfo'  =>  $storeInfo
     		]);
   	}

    /**
     * 选择不同模板的数据处理
     * @param  [int] $type  [模板类型  0-预约活动开始提醒  1-产品降价提醒  2-签到提醒 3-卡券到期 4-预约商品开售 5-服务到期]
     * @param  [array] $input [提交的表单数据]
     * @return [type]        [description]
     * @UPDATE 优化代码 并添加模板消息类型
     */
    public function validatorContent($type,$input,$source=0)
    {
        $data = [];
        $content = '';
        if($source == 0) {
            switch ($type) {
                case  0:
                    if (!isset($input['bookContent']) || empty($input['bookContent'])) {
                        error('预约内容不能为空');
                    }else {
                        if (mb_strlen($input['bookContent']) > 8) {
                            error('预约内容限制在8个字以内');
                        }
                    }
                    if(!isset($input['bookTime']) || empty($input['bookTime'])) {
                        error('预约时间不能为空');
                    }
                    $data['bookContent'] = $input['bookContent'];
                    $data['bookTime']    = $input['bookTime'];

                    break;
                case 1:

                    if (!isset($input['productTitle']) || empty($input['productTitle'])) {
                        error('商品名称不能为空');
                    }

                    if (!isset($input['price']) || empty($input['price'])) {
                        error('请输入商品现价');
                    }

                    if (!isset($input['cost_price']) || empty($input['cost_price'])) {
                        error('请输入商品原价');
                    }
                    $data['productTitle']  = $input['productTitle'];
                    $data['price']         = $input['price'];
                    $data['cost_price']    = $input['cost_price'];
                    break;

                case 2:
                    if (!isset($input['remindContent']) || empty($input['remindContent'])) {
                        error('提醒内容不能为空');
                    }
                    $data['remindContent'] = $input['remindContent'];
                    break;

                case 3://小程序卡券到期 CARD_CERTIFICATIONS_EXPIRE_TPL
                    if (!isset($input['name']) || empty($input['name'])) {
                        error('卡券名称不能为空');
                    }

                    if (!isset($input['use_limit']) || empty($input['use_limit'])) {
                        error('使用限制不能为空');
                    }

                    if (!isset($input['expiration_time']) || empty($input['expiration_time'])) {
                        error('过期时间不能为空');
                    }
                    $data['name'] = $input['name'];
                    $data['use_limit'] = $input['use_limit'];
                    $data['expiration_time'] = $input['expiration_time'];
                    break;
                case 4:
                    if (!isset($input['product_name']) || empty($input['product_name'])) {
                        error('商品名称不能为空');
                    }

                    if (!isset($input['sale_time']) || empty($input['sale_time'])) {
                        error('开售时间不能为空');
                    }

                    if (!isset($input['sale_price']) || empty($input['sale_price'])) {
                        error('开售价格不能为空');
                    }

                    $data['product_name'] = $input['product_name'];
                    $data['sale_time']  = $input['sale_time'];
                    $data['sale_price']  = $input['sale_price'];
                    break;
                case 5:
                    if (!isset($input['server_name']) || empty($input['server_name'])) {
                        error('服务名不能为空');
                    }

                    if (!isset($input['expiration_reason']) || empty($input['expiration_reason'])) {
                        error('过期原因不能为空');
                    }

                    if (!isset($input['server_expiration_time']) || empty($input['server_expiration_time'])) {
                        error('过期时间不能为空');
                    }

                    $data['server_name'] = $input['server_name'];
                    $data['expiration_reason']  = $input['expiration_reason'];
                    $data['server_expiration_time']  = $input['server_expiration_time'];
                    break;
                default :
                    error('不支持的提醒内容');
            }

        }else {

            switch ($type) {
                case  0:
                    if (!isset($input['news_type']) || empty($input['news_type'])) {
                        error('消息类型不能为空');
                    }

                    if (!isset($input['follow_time']) || empty($input['follow_time'])) {
                        error('跟进时间不能为空');
                    }

                    if (!isset($input['title']) || empty($input['title'])) {
                        error('标题不能为空');
                    }
                    $data['news_type']   = $input['news_type'];
                    $data['follow_time'] = $input['follow_time'];
                    $data['title']       = $input['title'];
                    break;
                case 1:
                    if (!isset($input['course_name']) || empty($input['course_name'])) {
                        error('课程名称不能为空');
                    }

                    if (!isset($input['start_time']) || empty($input['start_time'])) {
                        error('开始时间不能为空');
                    }

                    if (!isset($input['course_title']) || empty($input['course_title'])) {
                        error('标题不能为空');
                    }
                    $data['course_name'] = $input['course_name'];
                    $data['start_time']  = $input['start_time'];
                    $data['title']       = $input['course_title'];
                    break;

                case 4: //预约商品开售
                    if (!isset($input['sale_content']) || empty($input['sale_content'])) {
                        error('开售内容不能为空');
                    }
                    if (!isset($input['title']) || empty($input['title'])) {
                        error('标题不能为空');
                    }
                    if (!isset($input['sale_time']) || empty($input['sale_time'])) {
                        error('开售时间不能为空');
                    }

                    $data['sale_content']   = $input['sale_content'];
                    $data['sale_time']      = $input['sale_time'];
                    $data['title']          = $input['title'];
                    break;
                case 5: //服务到期
                    if (!isset($input['book_content']) || empty($input['book_content'])) {
                        error('预约项目不能为空');
                    }
                    if (!isset($input['title']) || empty($input['title'])) {
                        error('标题不能为空');
                    }
                    if (!isset($input['book_time']) || empty($input['book_time'])) {
                        error('预约时间不能为空');
                    }
                    $data['book_content']   = $input['book_content'];
                    $data['book_time']      = $input['book_time'];
                    $data['title']          = $input['title'];
                    break;
                default :
                    error('不支持的提醒内容');
            }

        }
       return json_encode($data);
    }

   	/**
   	 * 删除消息模板
   	 * @author wuxiaoping <2018.01.18>
   	 * @param  Request $request [description]
   	 * @return [type]           [description]
   	 */
   	public function delete(Request $request)
   	{
     		$id = $request->input('id') ?? 0;
     		if (!$id) {
     			  error('请选择要删除消息模板');
     		}
     		$messageTempService = new MessageTemplateService();
     		$data = $messageTempService->getRowById($id);
     		if (empty($data)) {
     			  error('该消息模板不存在或已被删除');
     		}
     		if ($messageTempService->del($id)) {
     			  success();
     		}

     		error();

   	}

    /**
     * 发送模板消息
     * @return [type] [description]
     */
   	public function send(Request $request)
   	{
        $id = $request->input('id') ?? 0;
        $wid = session('wid');
        if (!$id) {
          error('请先选择模板类型');
        }
        $dateTime = date('Y-m-d H:i:s',time());
        $sendLogData = [
            'wid'                 => $wid,
            'send_time'           => $dateTime,
            'message_template_id' => $id,
            'send_count'          => 0
        ];
        $messageTempService = new MessageTemplateService();
        $data = $messageTempService->getRowById($id);
        if (empty($data)) {
            error('消息模板不存在或已被删除');
        }
        if ((new MessageTemplateLogService())->add($sendLogData)) {
            $wxxcxCollectFormService = new WXXCXCollectFormIdService();
            if ($wxxcxCollectFormService->send($wid,$id,$dateTime)) {
                success('发送成功，已提交给系统处理,稍后可在历史记录中查看');
            }
            error('模板错误，未发送模板消息');

        }else {
          error('操作有误，未发送模板消息');
        }
        
   	}

    /**
     * 删除发送记录
     * @return [type] [description]
     */
    public function delRecord(Request $request)
    {
        $id = $request->input('id') ?? 0;
        $wid = session('wid');
        if (!$id) {
          error('请先选择要删除的记录');
        }
        $messageTemplateLogService = new MessageTemplateLogService();
        $data = $messageTemplateLogService->getRowById($id);
        if (empty($data)) {
            error('该记录不存在或已被删除');
        }

        if ($messageTemplateLogService->delete($id)) {
            success();
        }
        error();
    }

    /********************************公众号模板**************************************/
    /**
     * 公众号模板列表
     * @return [type] [description]
     */
    public function list()
    {
        $wid = session('wid');
        $messageTempService = new MessageTemplateService();
        $where['resource'] = 1;
        list($list,$indexPage) = $messageTempService->getAllList($wid,$where);
        if ($list['data']) {
            foreach ($list['data'] as $key => &$value) {
                $value['content']       = json_decode($value['content'],true);
            }
        }
        return view('merchants.message.list',[
            'title'    =>  '',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'list',
            'list'     =>  $list['data'],
        ]);
    }

    /**
     * 公众号模板添加、编辑页
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function create(Request $request,ShopService $shopService)
    {
        $input = $request->input();
        $id = $input['id'] ?? 0;
        $wid = session('wid');
        //$storeInfo=WeixinService::init('id',$wid)->where(['id'=>$wid])->getInfo();
        $storeInfo = $shopService->getRowById($wid);
        $messageTempService = new MessageTemplateService();
        $data = [];
        if ($id) {
            $data = $messageTempService->getRowById($id);
        }

        return view('merchants.message.create',[
          'title'    => $id ? '编辑消息模板' : '添加消息模板',
          'leftNav'  => $this->leftNav,
          'slidebar' => 'create',
          'data'     => $data,
          'storeInfo'  =>  $storeInfo
        ]);
    }

    /**
     * 发送公众号模板消息
     * @return [type] [description]
     */
    public function sendWeixinTemp(Request $request)
    {
        $id = $request->input('id') ?? 0;
        if (!$id) {
            error('请先选择模板类型');
        }
        $messageTempService = new MessageTemplateService();
        $data = $messageTempService->getRowById($id);
        $expirTime = $messageTempService->getExpireTime('send',35); //发送倒计时处理
        if (empty($data)) {
            error('消息模板不存在或已被删除');
        }
        $wid = session('wid');
        $dateTime = date('Y-m-d H:i:s',time());
        $sendLogData = [
            'wid'                 => $wid,
            'send_time'           => $dateTime,
            'message_template_id' => $id,
            'send_count'          => 0,
            'source'              => 1
        ];
        if ((new MessageTemplateLogService())->add($sendLogData)) {
            $wxxcxCollectFormService = new WXXCXCollectFormIdService();
            if ($wxxcxCollectFormService->sendWeixinTemp($wid,$id,$dateTime)) {
                success('发送成功，已提交给系统处理,稍后可在历史记录中查看','',$expirTime);
            }
            error('模板错误，未发送模板消息');
        }else {
          error('操作有误，未发送模板消息');
        }
    }
}
