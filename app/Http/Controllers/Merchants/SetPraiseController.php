<?php

namespace App\Http\Controllers\Merchants;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\S\ShareEvent\LiEventService;
use Validator;
use WeixinService;
use App\S\ShareEvent\LiRewardService;
use ProductService;
use App\S\Weixin\ShopService;

class SetPraiseController extends Controller
{
    /*
     * @return void
     */
    public function __construct()
    {
        $this->leftNav = 'liEvent';
        $this->liEventService = new LiEventService();
    }

    /**
     * 享立减后台列表
     * @return [type] [description]
     */
    public function index(Request $request)
    {	
    	$wid = session('wid');
    	$input = $request->input() ?? [];
    	$input['wid'] = $wid;
    	$input['status'] = 0;
    	$input['source'] = 1;
    	list($list,$pageHtml) = $this->liEventService->list($input);
    	$is_open = $rewardId = 0;
    	$liRewardService = new LiRewardService();
        $rewardSet = $liRewardService->getAllList($wid,'',false);
    	$reduceData = ['is_open' => $is_open,'id' => $rewardId,'is_open_card' => 0,'share_title' => '','share_img' => ''];
    	if ($rewardSet) {
            $is_open = $rewardSet[0]['is_open'];
            $rewardId = $rewardSet[0]['id'];
			$reduceData['is_open']      = $is_open;
			$reduceData['id']           = $rewardId;
			$reduceData['is_open_card'] = $rewardSet[0]['is_open_card'];
			$reduceData['share_title']  = $rewardSet[0]['share_title'];
			$reduceData['share_img']    = $rewardSet[0]['share_img'];
        }
    	return view('merchants.shareEvent.praise.index',[
			'title'      => '享立减',
			'leftNav'    => $this->leftNav,
			'slidebar'   => 'index',
			'pageHtml'   => $pageHtml,
			'list'       => $list['data'],
			'reduceData' => $reduceData
    	]);
    }


    public function save(Request $request)
    {
    	$input = $request->input();
    	$id = $input['id'] ?? 0;
    	$wid = session('wid');
    	$shareEventData = $showImgs = [];
    	$liRewardService = new LiRewardService();
        $rewardSet = $liRewardService->getAllList($wid);
        if ($rewardSet[0]['data']) {
			$shareEventData['is_open_card'] = $rewardSet[0]['data'][0]['is_open_card'];
			$shareEventData['share_title']  = $rewardSet[0]['data'][0]['share_title'];
			$shareEventData['share_img']    = $rewardSet[0]['data'][0]['share_img'];
        }

    	if ($request->isMethod('post')) {
    		/* 数据验证 */
	        $rules = array(
	            'title'         => 'required',
	            'product_id'    => 'required',
                'lower_price'    => 'required',
	            'share_title'   => 'required',
	            'share_img'     => 'required',
	            'act_img'       => 'required',
	            'show_imgs'     => 'required',
	            'subtitle'      => 'required',
	            'btn_title'     => 'required',
	            'start_time'    => 'required',
	            'end_time'      => 'required',
	            'like_count'	=> 'required',
	            'card_img'		=> 'required'
	        );
	        $messages = array(
	            'mphone.required'       => '请输入标题',
	            'product_id.required'   => '请输入商品ID',
	            'share_title.required'  => '请输入分享标题',
	            'share_img.required'    => '请设置享立减的分享图片',
	            'act_img.required'      => '请设置享立减活动图片',
	            'show_imgs.required'    => '请设置享立减商品图片',
	            'subtitle.required'     => '请输入活动副标题',
	            'btn_title.required'    => '请输按钮名称' ,
	            'start_time.required'   => '请设置生效开始时间',
	            'end_time.required'     => '请设置生效结束时间',
	            'like_count.required'   => '请输入集赞人数',
	            'card_img.required'		=> '请上传卡片图片',
                'lower_price.required'           => '请设置集满赞后购买价格',
	        );
	        $validator = Validator::make($input, $rules, $messages);
	        if ( $validator->fails() ) {
	            error( $validator->errors()->first() );
	        }


	        if (count($input['show_imgs']) > 10) {
	           error('活动商品图片最多只能添加10张');
	        }

	        if (strtotime($input['start_time']) >= strtotime($input['end_time'])) {
	           error('请设置生效结束时间大于开始时间');
	        }

            if ($input['lower_price'] < 0) {
                error( '保底价必须大于0' );
            }

			$data['title']         = $input['title'];
			$data['product_id']    = $input['product_id'];
			/*add by wuxiaoping 添加分享内容到数据库*/
			$data['share_title']   = $input['share_title'] ?? '';
			$data['share_img']     = $input['share_img'] ?? '';
			
			$data['card_img']      = $input['card_img'] ?? '';
			$data['act_img']       = $input['act_img'] ?? '';
			$data['subtitle']      = $input['subtitle'] ?? '';
			$data['is_initial']    = $input['is_initial'] ?? 0;
			$data['initial_value'] = $input['initial_value'] ?? 0;
			$data['show_imgs']     = join(',',$input['show_imgs']);
			$data['button_title']  = $input['btn_title'] ?? '';
			$data['start_time']    = strtotime($input['start_time']);
			$data['end_time']      = strtotime($input['end_time']);
            $data['rule_title']    = $input['rule_title'] ?? '享立减规则';
            $data['rule_img']      = $input['rule_img'] ?? '';
            $data['rule_text']     = $input['rule_text'] ?? '';
            $data['like_count']	   = $input['like_count'] ?? 0;
            $data['lower_price']   = $input['lower_price'];

	        if ($id) {
	        	$rs = $this->liEventService->update(['id'=>$id],$data);
	        }else {
	        	$data['wid'] = $wid;
                $data['source'] = 1;
	        	$rs = $this->liEventService->create($data);
	        }
	        //处理成功操作
	        if ($rs) {
	        	success();
	        }
	        error();
    	}

    	if ($id) {
    		$shareEventData = $this->liEventService->getOne($id,$wid);
    		if ($shareEventData) {
    			$showImgs = explode(',',$shareEventData['show_imgs']);
    			$productData = ProductService::getDetail($shareEventData['product_id']);
    			$shareEventData['product_detail'] = $productData;
    			if(!empty($shareEventData['unit_amount']))
	                $shareEventData['unit_amount']=sprintf('%.2f',$shareEventData['unit_amount']/100);
	            if(!empty($shareEventData['lower_price']))
	                $shareEventData['lower_price']=sprintf('%.2f',$shareEventData['lower_price']/100);
                $shareEventData['start_time'] = $shareEventData['start_time'] ? date('Y-m-d H:i:s',$shareEventData['start_time']) : '';
                $shareEventData['end_time']   = $shareEventData['end_time'] ? date('Y-m-d H:i:s',$shareEventData['end_time']) : '';
    		}
    	}

    	return view('merchants.shareEvent.praise.save',[
			'title'    => $id ? '编辑享立减' : '添加享立减',
			'leftNav'  => $this->leftNav,
			'slidebar' => 'save',
			'data'     => $shareEventData,
			'showImgs' => $showImgs
    	]);
    }

    /**
     * 设置红包开关
     * @return [type] [description]
     */
    public function openReward(Request $request)
    {
        $is_open = $request->input('is_open') ?? 0;
        $id = $request->input('id') ?? 0;
        $wid    = session('wid');
        $liRewardService = new LiRewardService();
        $data['is_open'] = $is_open;
        if (!$id) {
            $data['wid'] = $wid;
            $rs = $liRewardService->add($data);
        }else {
            $rs = $liRewardService->update($id,$data);
        }

        if ($rs) {
            success();
        }

        error();
    }

    /**
     * [del description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function del(Request $request)
    {
        $wid    = session('wid');
        $id     = $request->input('id',0);
        $data['type']   = $request->input('type',0);
        $data['status'] = $request->input('status',0);
        if ($data['status'] == 1) {
            $data['type'] = 1;
        }
        $eventData = $this->liEventService->getOne($id,$wid);
        if (empty($eventData)) {
        	error('该享立减不存在或已被删除');
        }

        $rs = $this->liEventService->update(['id'=>$id], $data);
        if ($rs) {
        	success('操作成功', '/merchants/shareEvent/list');
        }
        error();
    }

    /**
     * 一键翻新
     * Author: MeiJay
     * @param Request $request
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function refreshKey(Request $request,ShopService $shopService)
    {
        if($request->isMethod('post')){
            $wid = session('wid');
            //$shopData = WeixinService::getStore($wid);
            $shopData = $shopService->getRowById($wid);
            if($shopData){
                $data['share_event_key'] = $wid.'_'.time();
                //$re = WeixinService::init('wid',$wid)->where(['id'=>$wid])->update($data,false);
                $re = $shopService->update($wid,$data);
                if($re){
                    success();
                }     
            }
        }
        error();
    }

    /**
     * 红包设置
     * @return [type] [description]
     */
    public function rewardSet(Request $request)
    {
        $wid = session('wid');
        $liRewardService = new LiRewardService();
        if ($request->isMethod('post')) {
            $input = $request->input();
            $id = $input['id'] ?? 0;
            if (isset($input['source']) && $input['source']== 'share') {
                $rules = [
                    'share_title' => 'required',
                    'share_img'   => 'required'
                ];

                $messages = [
                    'share_title.required' => '请输入分享标题',
                    'share_img.required'   => '请上传分享图片',
                ];

                $validator = Validator::make($input,$rules,$messages);
                if ($validator->fails()) {
                    error($validator->errors()->first());
                }
				$data['is_open_card'] = $input['is_open_card'] ?? 0;
				$data['share_title']  = $input['share_title'];
				$data['share_img']    = $input['share_img'];

            }else {
                if ($input['type'] == 0) {
                    if (!isset($input['fixed']) || !$input['fixed']) {
                        error('请输入红包固定的助减金额');
                    }else {
                        if (!is_numeric($input['fixed'])) {
                            error('红包助减金额只能为整数或小数');
                        }
                    }
                }
                if ($input['type'] == 1) {
                    if (!isset($input['minimum']) || (!$input['minimum'] && $input['minimum'] <> 0) || !isset($input['maximum']) || (!$input['maximum'] && $input['maximum'] <> 0)) {
                        error('请输入随机红包助减金额范围');
                    }else {
                        $reg = '/^[0-9][0-9]*$/';
                        if (!preg_match($reg,$input['minimum']) || !preg_match($reg,$input['maximum'])) {
                            error('红包随机范围请输入整数');
                        }

                        if ($input['maximum'] <= $input['minimum']) {
                            error('红包范围设置不正确,请查证后重新输入');
                        }
                    }
                }
                $minimum = 0;
                $maximum = 0;
                $fixed_money = 0;
                if ($input['type'] == 1) {
                    $minimum = $input['minimum'];
                    $maximum = $input['maximum'];
                }else {
                    $fixed_money = $input['fixed'];
                }
                $data['type']        = $input['type'];
                $data['fixed_money'] = $fixed_money;
                $data['minimum']     = $minimum;
                $data['maximum']     = $maximum;
            }
            
            if ($id) {
                $rs = $liRewardService->update($id,$data);
            }else {
                $data['wid'] = $wid;
                $rs = $liRewardService->add($data);
            }
            if ($rs) {
                success();
            }
            error();
        }
        list($list,$pageHtml) = $liRewardService->getAllList($wid);

        $returnData = ['status' => 1,'info' => '','data' => []];
        if ($list['data']) {
            $returnData['data'] = $list['data'][0];
        }
        return $returnData;

    }

}
