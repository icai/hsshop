<?php

namespace App\Http\Controllers\Merchants;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use WeixinService;
use Validator;
use App\S\Lift\ReceptionService;
use App\S\Foundation\RegionService;
use App\S\Order\OrderZitiService;
use App\S\Weixin\ShopService;

class ReceptionController extends Controller
{
    //
    public function __construct()
    {
    	$this->leftNav = 'currency';
    	$this->service = new ReceptionService();
    }

    /**
     * 自提点列表信息
     * @return [type] [description]
     */
    public function list(Request $request)
    {   
    	$wid = session('wid');
    	list($list,$pageHtml) = $this->service->getAllList($wid);
        $count = 0;
    	if ($list['data']) {
    		foreach ($list['data'] as $key => $val) {
	            $temp[] = $val['province_id'];
	            $temp[] = $val['city_id'];
	            $temp[] = $val['area_id'];
    		}
    		//根据省份,城市,区域id获取对应的名称
    		$regionService = new RegionService();
    		$region = $regionService->getListByIdWithoutDel($temp);
    		$tmpAddr = [];
	        foreach ($region as $value){
	            $tmpAddr[$value['id']] = $value['title'];
	        }

	        foreach ($list['data'] as &$item){
				$item['province_id'] = $tmpAddr[$item['province_id']];
				$item['city_id']     = $tmpAddr[$item['city_id']];
				$item['area_id']     = $tmpAddr[$item['area_id']];
	        }
            $count = $this->service->countList($wid);
    	}
        $isOpenZiti = D('Weixin')->model->where('id', $wid)->value('is_ziti_on');
    	return view('merchants.currency.zitiList',[
            'title'      => '自提点列表',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'reseptionList',
            'list'       => $list['data'],
            'pageHtml'   => $pageHtml,
            'isOpenZiti' => $isOpenZiti,
            'count'      => $count

    	]);
    }

    /**
     * 设置自提是否启动
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function startZiti(Request $request,ShopService $shopService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '更新启动成功', 'data' => []];
        $is_on = $request->input('is_on') ?? 0;
        $wid = session('wid');
        list($list,$pageHtml) = $this->service->getAllList($wid);
        if (empty($list['data'])) {
           $returnData['errCode'] = -2;
           $returnData['errMsg']  = '还未添加自提点地址，不能开启自提功能';
           return $returnData;
        }
        $uid = D('Weixin')->model->where('id', $wid)->value('uid');
        //$weixinService = D('Weixin', 'uid', $uid);
        $datas['is_ziti_on'] = $is_on; 
        //$rs = $weixinService->where(['id'=>$wid])->update($datas, false);
        $rs = $shopService->update($wid,$datas);
        if (!$rs) {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '更新启动失败';
        }
        return $returnData;
    }

    /**
     * 添加/编辑页面
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function editSeception(Request $request)
    {
    	$id = $request->input('id') ?? 0;
    	//获取地区全部数据
    	$regionService = new RegionService();
        $regions = $regionService->getAllWithoutDel();
        foreach ($regions as $key=>$item){
            $regionList[$item['pid']][] = $item;
        }
        //单独列出省份列表信息
        $provinceList = $regionList[-1];
        $data = $cityList = $areaList = [];
        $weekData = [1 => '周一', 2 => '周二', 3 => '周三', 4 => '周四', 5 => '周五', 6 => '周六', 7 => '周日'];
        if ($id) {
        	$data = $this->service->getRowById($id);
            $cityList = $regionList[$data['province_id']];
		    $areaList = $regionList[$data['city_id']];
            $data['area_code'] = '';
            $data['phone'] = $data['telphone'];
        	if (strpos($data['telphone'], '-')) {
        		$telphone = explode('-',$data['telphone']);
        		$data['area_code'] = $telphone[0];
        		$data['phone']	= $telphone[1];
        	}
            $data['reception_timesArr'] = json_decode($data['reception_times'],true);
            if ($data['reception_timesArr']) {
                foreach ($data['reception_timesArr'] as &$reTime) {
                    $reTime['weekDay'] = '';
                    foreach ($reTime['days'] as $day) {
                        $reTime['weekDay'] .= $weekData[$day]." ";
                    }
                }
            }
            $data['ziti_timesArr'] = json_decode($data['ziti_times'],true);
            if ($data['ziti_timesArr']) {
                foreach ($data['ziti_timesArr'] as &$ziTime) {
                    $ziTime['weekDay'] = '';
                    foreach ($ziTime['days'] as $day) {
                        $ziTime['weekDay'] .= $weekData[$day]." ";
                    }
                }
            }
            $data['imageArr'] = explode(',',$data['images']);
		}
        return view('merchants.currency.editSeception',[
			'title'        => $id ? '编辑自提信息' : '添加自提信息',
			'leftNav'      => $this->leftNav,
			'slidebar'     => 'reseptionList',
			'data'         => $data,
			'provinceList' => $provinceList,
			'regions'      => json_encode($regionList,JSON_UNESCAPED_UNICODE),
            'cityList'     => $cityList,
            'areaList'     => $areaList,
			'images'       => isset($data['imageArr']) && $data['imageArr'] ? json_encode($data['imageArr'],JSON_UNESCAPED_UNICODE) : '',
			'reception_times' => isset($data['reception_times']) && $data['reception_times'] ? json_encode($data['reception_times'],JSON_UNESCAPED_UNICODE) : '',
			'ziti_times'	=> isset($data['ziti_times']) && $data['ziti_times'] ? json_encode($data['ziti_times'],JSON_UNESCAPED_UNICODE) : '',
        ]);
    }


    /**
     * 添加/编辑自提信息
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function saveZiti(Request $request)
    {
		$input = $request->input() ?? [];
    	$id = $input['id'] ?? 0;
    	$rule = [
			'title'          => 'required',
			'provinceId'     => 'required',
			'cityId'         => 'required',
			'areaId'         => 'required',
			'address'        => 'required',
			'telphone'       => 'required',
			'receptionTimes' => 'required',
			'images'         => 'required',
    	];
    	$messages = [
			'title.required'      => '请填写自提点名称',
			'provinceId.required' => '请选择省份',
			'cityId.required'     => '请选择城市',
			'areaId.required'     => '请选择区/县',
			'address.required'    => '请填写详细地址',
			'telphone.required'   => '请填写联系电话',
			'receptionTimes'      => '请设置接待时间',
			'images.required'     => '请上传自提点照片',
    	];
    	/*对提交的表单进行规则验证*/
    	$validator = Validator::make($input,$rule,$messages);
    	if ($validator->fails()) {
    		error($validator->errors()->first());
    	}
        if ((!isset($input['longitude']) && empty($input['longitude'])) || (!isset($input['latitude']) && empty($input['latitude']))) {
            error('请点击图片确认经纬度');
        }
    	/*对接待时间，自提时间是否符合正确格式进行验证*/
    	$this->dealTimeSection($input['receptionTimes'],1);
    	if (isset($input['is_set_time']) && $input['is_set_time']) {
    		$this->dealTimeSection($input['zitiTimes'],2);
    	}

		$saveData['title']           = $input['title'];      //自提点名称 
		$saveData['province_id']     = $input['provinceId']; // 省份
		$saveData['city_id']         = $input['cityId'];     // 城市
		$saveData['area_id']         = $input['areaId'];     // 区/县
		$saveData['address']         = $input['address'];	 // 详细地址
		$saveData['telphone']        = $input['telphone'];   // 联系电话
		$saveData['reception_times'] = json_encode($input['receptionTimes'],JSON_UNESCAPED_UNICODE);  // 设置接待时间
		$saveData['is_set_time']     = $input['is_set_time'] ?? 0; //选择自提时间
		$saveData['ziti_times']      = $input['is_set_time'] ? json_encode($input['zitiTimes'],JSON_UNESCAPED_UNICODE) : ''; //设置自提点照片
		$saveData['images']          = join(',',$input['images']);     //上传的自提点照片
		$saveData['comment']         = $input['comment'] ?? '';         // 商家推荐信息
		$saveData['store_reception'] = $input['store_reception'] ?? 0; //是否作为门店接待
        $saveData['longitude']       = $input['longitude']; //自提点经度
        $saveData['latitude']        = $input['latitude']; //自提点纬度

    	if ($id) {
    		$rs = $this->service->update($id,$saveData);
    		$msg = '更新成功';
    	}else {
    		$saveData['wid'] = session('wid');
    		$rs = $this->service->add($saveData);
    		$msg = '添加成功';
    	}
    	if ($rs) {
    		success($rs);
    	}
    	error();
    }

    /**
     * 删除自提地址
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delReception(Request $request)
    {
    	$id = $request->input('id') ?? 0;
    	if (!$id) {
    		error('请先选择要删除的自提地址');
    	}
        $data = (new OrderZitiService())->getDataByCondition(['ziti_id' => $id]);
        if (isset($data['id']) && $data['id']) {
            error('该自提点地址已被应用，无法删除');
        }

    	if ($this->service->del($id)) {
    		success('删除成功');
    	}
    	error('删除失败');
    }

    /**
     * 处理时间段
     * @param  array  $times [数组格式时间段]
     * @param  int $type 区别时间段类别   1表示处理接待时间  2表示处理自提时间
     * @return [type]        [description]
     */
    public function dealTimeSection($times=[],$type=1)
    {
    	if ($times && !is_array($times)) {
    		error('时间段格式不正确');
    	}

    	foreach ($times as $key => $value) {
    		if ($value['startTime'] >= $value['endTime']) {
    			if ($type == 1) {
    				error('请设置接待时间开始时间要小于结束时间');
    			}else if ($type == 2) {
    				error('请设置自提时间开始时间要小于结束时间');
    			}
    		}

    		if (!isset($value['days']) && empty($value['days'])) {
    			if ($type == 1) {
    				error('请设置接待时间的日期（周一至周日）');
    			}else if($type == 2){
    				error('请设置自提时间的日期（周一至周日）');
    			}
    		}
    	}
    }	
}
