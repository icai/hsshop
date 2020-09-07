<?php

namespace App\S\Member;

use App\Lib\Redis\MemberCard as MemberCardRedis;
use App\Module\CouponModule;
use App\S\Market\CouponService;
use App\Module\MemberCardModule;
use App\S\S;
use DB;
use Illuminate\Validation\Rule;
use MemberCardRecordService as MemCardRecordService;
use Validator;
use App\Services\Wechat\ApiService;

class MemberCardService extends S {
    
    
    /**
     * 构造方法
     *
     *
     * @return void
     */
    public function __construct()
    {
        $this->request = app('request');
        parent::__construct('MemberCard');
    }

    
    /**
     * 添加会员卡字段验证
     *
     * @param  array $verifyField   [需要验证的字段数组，默认只验证主键]
     * @return array 请求参数
     * @update 梅杰 2018年8月14日 会员卡名唯一
     */
    public function verify( $verifyField = ['id'] )
    {
        /* 接收数据 */
        $input = $this->request->only($verifyField);

        /* 获取验证数据和提示消息 */
        $rules = [];
        $messages = [];
        $id = $this->request->input('id',0);
        foreach ($verifyField as $value) {
            switch ($value){
                /* 标签名 */
                case 'title':
                    $rules['title'] = [
                        'required',
                        'between:3,18',
                        'unique' => Rule::unique('member_card')->where(function ($query) use ($id) {
                            $query->where('id', '<>',$id)->where(['wid'=> session('wid')])->where('state','<>',-1)->whereNull('deleted_at');
                        })

                    ];
                    $messages['title.required'] = '请输入会员卡名称';
                    $messages['title.between'] = '请输入3-18位会员卡名称';
                    $messages['title.unique']  = '该会员卡名已存在';
                    break;
                case 'member_power':
                    $rules['member_power'] = 'required';
                    $messages['member_power.required'] = '请选择会员特权';
                    break;
                case 'description':
                    $rules['description'] = 'required';
                    $messages['description.required'] = '请输入会员卡描述';
                    break;
                case 'date_limit':
                    $rules['date_limit'] = 'required';
                    $messages['date_limit.required'] = '请选择会员期限';
                    break;
                case 'card_rank':
                    $rules['card_rank'] = 'required';
                    $messages['card_rank.required'] = '请选择会员等级';
                    break;
                    
                default :
                    // code ...
                    break;
            }
        }

        /* 调用验证器执行验证方法 */
        $validator = Validator::make($input, $rules, $messages);

        /* 验证不通过则提示错误信息 */
        if ( $validator->fails() ) {
            error( $validator->errors()->first() );
        }
        return true;
    }
    
    /*
     * 获取某个用户所获得的会员卡信息
     * @author 吴晓平
     * @param int $mid 用户id
     * @param int $wid 店铺id
     * return array $returnData 
     */
    public function getCardList($mid,$wid)
    {
        //用户信息
        $membersData = (new MemberService())->getRowById($mid);
        //该登录用户的领取全部会员卡记录
        list($list) = MemCardRecordService::init('mid', $mid)->getList();
        $cardIds = [];
        $returnCardData = []; 

        //如果没有设置默认卡，则认为第一张为默认
        if($list['data']){
            foreach($list['data'] as $key=>$val)
            {
                $cardIds[] = $val['card_id'];

                if($val['is_default']==1){
                    $default['default'] = $val['card_id'];
                }else{
                    if($key==0){
                        $default['default'] = $val['card_id'];
                    }
                }

            }
            //获取会员卡列表 
            list($cardList) = $this::init('wid',$wid)->getList(false,$cardIds);

            if($cardList['data']){
                $member_power = '';
                foreach($cardList['data'] as $cards){
                    $powerArr = explode(',',$cards['member_power']);
                    if(in_array(1,$powerArr)){
                        $member_power .= '包邮,';
                    }else if(in_array(2,$powerArr)){
                        $member_power .= $cards['discount'].'折,';
                    }else if(in_array(3,$powerArr)){

                    }else if(in_array(4,$powerArr)){
                        $member_power .= '赠送'.$cards['score'].'积分,';
                    }
                    $cards['member_power'] = substr($member_power,0,-1);
                    //排序 把默认卡放在所有会员卡的最前面
                    if($cards['id']==$default['default']){
                        $returnCardData[] = $cards;
                    }else{
                        array_push($returnCardData, $cards);
                    }
                }
            }
            $membersData['cards'] = $returnCardData;
        }else{
            $membersData['cards'] = [];
        }

        return $membersData;
    }

    /**
     * [处理会员卡添加编辑提交的数据]
     * @param  array $postData  表单提交的数据
     * @param  int  $card_status 会员卡类型  0-无门槛  1-按规则  2-购买会员
     * @return array $saveData  
     * @author  吴晓平 
     */
    public function postCardDataHandle( $postData,$card_status,$wid)
    {   
        $saveData = [];//定义返回的数组

        $saveData['cover'] = $postData['cover'];    //会员卡背景选择颜色或图片
        $saveData['cover_value'] = $saveData['cover']==0? $postData['bg_color']: $postData['bg_img'];   //会员卡的颜色值或图片值 
        $saveData['title'] = $postData['title'];    //会员卡标题
        $saveData['member_power'] = join(',',$postData['member_power']); //会员特权

        //获取会员卡特权的值 (会员折扣)
        if(in_array(2,$postData['member_power'])){
            if ($postData['discount'] >= 1 && $postData['discount'] <= 10){
                $saveData['discount'] = $postData['discount'];
            }else{
                error('折扣必须大于1小于10');
            }
            
        }
        //优惠券
        if(in_array(3,$postData['member_power'])){
            $coupon = [];
            foreach ($postData['coupon_type'] as $key=>$val){
                $temp['coupon_id'] = $val;
                if (empty($postData['coupon_num'][$key])){
                    continue;
                }else{
                    $temp['num'] = $postData['coupon_num'][$key];
                }
                $coupon[] = $temp;
            }
            $saveData['coupon_conf'] = json_encode($coupon);
            if (empty($saveData['coupon_conf'])){
                error('请选择优惠券');
            }
        }
        //积分
        if(in_array(4,$postData['member_power'])){
            $saveData['score'] = $postData['score'];
        }
            
        $saveData['description']   = $postData['description'];
        $saveData['service_phone'] = $postData['telephone'];
        $saveData['is_active']     = $postData['active'];

        if($saveData['is_active'] == 1){
            // $saveData['active_remart'] = join(',',$postData['active_condition']);    //激活时需要的条件
        }

        $saveData['is_share']    = $postData['allow']??0;                  //是否允许设置分享
        $saveData['card_status'] = $card_status;                       //会员卡类型

        $saveData['is_sync_wechat'] = isset($postData['isSyncWeixin'])?1:0;         //是否同步到微信卡券
        if($saveData['is_sync_wechat'] == 1){
            $saveData['closure_type'] = $postData['ways'];
            //如果选择卡片的封面是图片，则上传该图片到微信服务器，返回url
            if(isset($postData['bg_img']) && $postData['bg_img']){
                $apiService = new ApiService();
                $filename = str_replace(config('app.url'), '', $postData['bg_img']);
                $filename = substr($filename,1);
                $result = $apiService->uploadFile($wid,$filename);
                try{
                    if(!isset($result['errcode']) && empty($result['errcode'])){
                        $saveData['weixin_bg_img'] = $result['url']; 
                    }else{
                        error('图片数据异常');
                    }
                }catch(exception $e){
                    error($e -> getMessage());
                }
            }
        }
        //会员卡类型
        if(empty($card_status)){
            $saveData['limit_type'] = $postData['date_limit'];
            switch($saveData['limit_type']){
                case 1: 
                    $saveData['limit_days']  = $postData['limit_days'];
                    $saveData['limit_start'] = '';
                    $saveData['limit_end']   = '';
                    break;
                case 2:
                    $saveData['limit_start'] = $postData['startAt'];
                    $saveData['limit_end']   = $postData['endAt'];
                    $saveData['limit_days']  = 0;
                    break;
                default:
                    $saveData['limit_days']  = 0;
                    $saveData['limit_start'] = '';
                    $saveData['limit_end']   = '';
                    break;
                    
            }
        }else if($card_status == 1){
            $saveData['up_condition'] = $postData['cumulative_pay'].'||'.$postData['cumulative_amount'].'||'.$postData['cumulative_score'];
            $saveData['card_rank'] = $postData['card_rank'];
        }
        return $saveData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170515
     * @desc 获取会员卡信息
     * @param $id
     */
    public function getCard($id)
    {
        $card = $this->getRowById($id);
        if ($card) {
            $card['member_power'] = explode(',',$card['member_power']);
        }
        return $card;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170517
     * @desc 按照规则发放会员卡
     * @param $id
     * @update 梅杰 2018年9月19日 自动发卡时修改会会员标志
     */
    public function grant($mid,$wid)
    {
        $result = [
            'status'    => 0,
            'data'      =>[],
        ];
        //獲取規則會員卡詳情
        $cards = $this->model->where(['wid'=>$wid,'card_status'=>1,'state'=>1])->orderBy('card_rank','asc')->get()->toArray();
        $member = (new MemberService())->getRowById($mid);
        $sql = 'SELECT COUNT(id) as num,SUM(pay_price) as amount FROM ds_order WHERE wid='.$wid.' AND mid='.$mid.' AND status>0 AND status<4';
        list($res) = DB::select($sql);
        //纍計消費筆數
        $num = $res->num;
        //纍計消費金額
        $amount = $res->amount??0;
        $card = [];
        foreach ($cards as $key=>$val)
        {
            $rule = explode('||',$val['up_condition']);
            if (!$rule){
                continue;
            }
            if ((isset($rule[0]) && $rule[0] && $rule[0]<=$num) || (isset($rule[1]) && $rule[1]  && $rule[1]<=$amount) || (isset($rule[2]) && $rule[2] && $rule[2]<=$member['score'])){
                $card[] = $val;
            }
        }
        foreach ($card as $item){
            if(!$this->judge($item,$mid,$wid)){
                continue;
            }
            //生成會員卡號
            $no = MemCardRecordService::getCardNo();
            $item['is_active']?$active_status = 0:$active_status=1;
            $cardRecord = [
                'wid'               => $wid,
                'mid'               =>$mid,
                'member_title'     => $member['truename'],
                'card_id'           => $item['id'],
                'card_num'          =>$no,
                'status'            =>1,
                'is_default'        => 0,
                'is_view'           => 0,
                'active_status'    => $active_status,
                'in_card_at'        => date("Y-m-d H:i:s"),
                'is_new'            => 1
            ];
            //贈送會員卡與積分
            if(isset($item['score']) && $item['score'] > 0)
            {
                $MemberService = new MemberService();
                $MemberService->incrementScore($mid,$item['score']);
                $input = [
                    'wid'=>$wid,
                    'mid'=>$mid,
                    'point_type'=> 7,
                    'is_add' => 1,
                    'score' =>$item['score'],
                ];
                \PointRecordService::insertData($input);
            }
            //插入優惠券信息
            $couponConf =json_decode($item['coupon_conf'],true);
            if($couponConf) {
                $couponModule = new CouponModule();
                foreach ($couponConf as $v) {
                    $couponModule->createCouponLog($mid,$v['coupon_id'],$v['num'], $wid);
                }
                (new CouponService())->updateCoupon($couponConf);
            }
            //插入領卡記錄
            $cardRecord['id'] = MemCardRecordService::init('mid',$mid)->add($cardRecord,false);
            (new MemberCardModule())->checkIsMember($mid);
            $result['status'] = 1;
            $cardRecord['cardDetail'] = $item;
            $result['data'][] = $cardRecord;
        }
        return $result;

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 判断用户是否存在会员卡
     * @desc
     * @param $card
     * @param $mid
     * @return bool
     */
    public function judge($card,$mid,$wid)
    {
        $res = MemCardRecordService::init('wid',$wid)->model->where(['mid'=>$mid,'wid'=>$wid,'card_id'=>$card['id']])->get()->toArray();
        if ($res){
            return false;
        }
        return true;
    }

    public function getRowById($id)
    {
        $result = [];
        $redis = new MemberCardRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result){
                return $result;
            }
            $result = $result->toArray();
            $redis->add($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new MemberCardRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    public function getListByWid($wid, $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        return $this->getList(['wid'=>$wid],$skip, $perPage, $orderBy, $order);
    }

    public function getListByWhere($where, $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        return $this->getList($where,$skip, $perPage, $orderBy, $order);
    }

    public function update($id,$data)
    {
        $res = $this->model->where('id',$id)->update($data);
        if (!$res){
            return false;
        }
        $memberCardRedis = new MemberCardRedis();
        $re = $memberCardRedis->updateOne($id,$data);
        return $re;
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }


    public function getListPage($where, $orderBy = '', $order = '',$pagesize = '')
    {
        if ($pagesize){
            $this->perPage = $pagesize;
        }
        return $this->getListWithPage($where, $orderBy, $order);
    }


    public function getMemberCardList($cardIds,$isXCX=0)
    {
        $returnData  = [
            'errCode' => 0,
            'data'     => [],
        ] ;
        $where['id'] = ['in',$cardIds];
        list($list,$html) = $this->getListPage($where);
        if($list['data']){
            foreach ($list['data'] as $k=>$v){
                $returnData['data'][] = [
                    'id' => $v['id'],
                    'name' => $v['title'],
                    'url'   => '/shop/member/detail/'.$v['wid'].'/'.$v['id'],
                    'img'   => config('app.source_url').'mctsource/images/card.png'
                ];
            }
        }
        return $returnData;
    }
}