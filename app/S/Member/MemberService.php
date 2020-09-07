<?php

namespace App\S\Member;

use App\Jobs\SalesmanStatistic;
use App\Lib\Redis\Member as MemberRedis;
use App\Lib\Redis\Member;
use App\Module\DistributeModule;
use App\Module\MessagePushModule;
use App\S\MarketTools\MessagesPushService;
use App\S\S;
use App\Services\WeixinService;
use DB;

/**
 * 客户/会员
 */
class MemberService extends S
{
    public function __construct()
    {
        parent::__construct('Member');
    }

    /**
     * 获取固定数据数组
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月14日 10:33:35
     */
    public  static function getStaticList() {
        return [
            /* 来源数组 */
            [ '公众号关注', '公众号关注', '分享', '导入', '录入','小程序','小程序' ],
            /* 购次数组 */
            ['-1'=>'全部' ,'0' => '0', '1' => '1+', '2' => '2+', '3' => '3+', '4' => '4+', '5' => '5+', '10' => '10+', '15' => '15+', '20' => '20+', '30' => '30+', '50' => '50+' ],
        ];
    }

    /**
     * 获取客户列表信息(处理导入文件)
     * @param $customer
     * @param $wid
     * @return array
     * @author 许立
     * @since 2017/03/03 20:00
     */
    public static function getMemberInfo($customer, $wid)
    {
        if (empty($customer)) {
            return [];
        }
        $list = [];
        if (is_string($customer)) {
            $customer = explode(',', $customer);
        }
        list($list['truename'],$list['wechat_id'],$list['nickname'],$list['mobile'],$list['sex']) = $customer;
        $list['wid'] = $wid;
        $list['sex'] = strpos($list['sex'], '男') !== false ? 1 : (strpos($list['sex'], '女') !== false ? 2 : 0);
        $list['is_member'] = 1;
        //来源 导入
        $list['source'] = 4;
        $arr = array_map('trim', $list);
        return $arr;
    }

    /**
     * 构建验证规则和错误信息
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月7日 15:44:15
     *
     * @param  array $verifyField   [需要验证的字段数组]
     *
     * @return array 请求参数
     */
    public static function buildVerify( array $verifyField = [] ) {
        /* 获取验证数据和提示消息 */
        $rules    = [];
        $messages = [];
        foreach ($verifyField as $value) {
            switch ( $value ) {
                // 微信昵称/姓名
                case 'truename':
                    $rules['truename'] = 'required|max:20';
                    $messages['truename.required'] = '请填写昵称';
                    $messages['truename.max'] = '昵称最多可填写20个字';
                    break;
                // 微信昵称/姓名
                case 'nickname':
                    $rules['nickname'] = 'required|max:20';
                    $messages['nickname.required'] = '请填写昵称';
                    $messages['nickname.max'] = '昵称最多可填写20个字';
                    break;
                // 手机号
                case 'mobile':
                    $rules['mobile'] = 'required|regex:mobile';
                    $messages['mobile.required'] = '请填写手机号';
                    $messages['mobile.regex'] = '手机号格式不正确';
                    break;
                default:
                    # code...
                    break;
            }
        }
        return [$rules, $messages];
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170525
     * @desc 根据条件搜索用户
     * @param string $input
     * @update 排序 梅杰2018年9月12日
     * @update 增加按购次 梅杰2018年9月25日
     */
    public function getListByConditionWithPage($input)
    {
        /* 查询条件数组 */
        $where = ['status'=>0];
        /* 参数转换为查询条件数组 */
        if ( $input ) {
            foreach ($input as $key => $value) {
                if ( empty($value) && $key !== 'buy_num') {
                    continue;
                }
                switch ( $key ) {
                    case 'wid':
                        $where['wid'] = $value;
                        break;
                    // 手机号
                    case 'mobile':
                        $where['mobile'] = $value;
                        break;
                    // 微信昵称
                    case 'nickname':
                        $where['nickname'] = ['like','%'.$value.'%'];
                        break;
                    // 来源
                    case 'source':
                        $where['source'] = $value - 1;
                        break;
                    // 会员身份
                    case 'is_member':
                        $where['is_member'] = $value-1;
                        break;
                    // 购次
                    case 'buy_num':
                        if ($value == -1) {
                            continue ;
                        }
                        $where['buy_num'] = $value == 0 ? $value : array('>=', $value);
                        break;
                    // 地域（省份筛选）
                    case 'province_id':
                        $where['province_id'] = $value;
                        break;
                    //openid
                    case 'openid':
                        $where['openid'] = $value;
                        break;
                    case 'xcx_openid':
                        $where['xcx_openid'] = $value;
                        break;
                    //微信号搜索
                    case 'wechat_id':
                        $where['wechat_id'] = $value;
                        break;
                    case 'id':
                        $where['id'] = ['in',$value];
                        break;
                    case 'sex':
                        $where['sex'] = $value;
                        break;
                    case 'is_distribute':
                        $where['is_distribute'] = $value;
                        break;
                    case 'truename':
                        $where['truename'] = $value;
                        break;
                    //add by wuxiaoping 2018.05.15
                    case 'is_pull_black':
                        $where['is_pull_black'] =$value;
                        $orderBy = 'updated_at';
                        $order = 'DESC';
                        break;
                    case 'amount':
                        $where['amount'] = $value;
                        break;
                    case 'visit_time':
                        if ($value == 1) {
                            $orderBy = 'latest_access_time';
                            $order = 'DESC';
                        }
                        break;
                    case 'sort':
                        $params = explode('-',$value);
                        $orderBy = $params[0]??'id';
                        $order = $params[1]??'desc';
                        break;
                    case 'pid':
                        $where['pid'] = $value;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        $request = app('request');
        $order = $request->input('order','');
        $orderBy = $request->input('orderBy','');
        $order = $order && in_array($order,['desc','asc']) ? $order : 'desc' ;
        $orderBy = $orderBy && in_array($orderBy,['buy_num','amount','latest_access_time','score']) ? $orderBy : 'latest_access_time' ;
        //增加按最近访问时间排序
        if (!empty($input['latest_visit_time_start']) && !empty($input['latest_visit_time_end'])) {
            $start = strtotime($input['latest_visit_time_start']);
            $end = $input['latest_visit_time_start'] == $input['latest_visit_time_end'] ? $start + 86400 : strtotime($input['latest_visit_time_end']);
            $where['latest_access_time'] = ['between',[date('Y-m-d H:i:s',$start),date('Y-m-d H:i:s',$end)]];
        }
        return $this->getListWithPage($where,$orderBy,$order);
    }

    public function getRowById($id)
    {
        $result = [];
        $redis = new MemberRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->add($result);
        }
        return $result;
    }

    public function getRowByOpenid($openId)
    {
        $redis = new MemberRedis('openid');
        $id = $redis->getIdByOpenid($openId);

        if (empty($id)) {
            $result = $this->model->wheres(['openid' => $openId])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $id = $result['id'];
            $redis->setIdByOpenid($openId, $id);
        }
        return $this->getRowById($id);
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new MemberRedis();
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

    public function add($input)
    {
        return $this->model->insertGetId($input);
    }

    public function incrementScore($id, $score)
    {
        $this->model->wheres(['id' => $id])->increment('score',$score);
        $redis = new MemberRedis();
        $redis->increment($id,'score',$score);
    }

     /**
     * todo 更改会员表数据
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-05-31
     */
    public function updateData($id=0,$data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        if(empty($data))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }
        $updateReturnValue = $this->model->where(['id'=>$id])->update($data);
        $redis = new MemberRedis();
        $redis->updateHashRow($id, $data);

        if(!$updateReturnValue)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        return $returnData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170606
     * @desc 绑定分销上下级关系
     * @update 何书哲 2018年11月09日 公众号绑定上下级消息通知
     */
    public function bindParent($id,$mid)
    {
        if($id == $mid){
            return false;
        }
        $member = $this->model->select('id','pid','distribute_top_level')->find($mid);
        if ($member->pid != 0 || $member->distribute_top_level == 1){
            return false;
        }
        $member = $this->model->select('id','pid','wid','is_distribute')->find($id);
        if (!$member){
            return false;
        }
        if ($member->pid == $mid){
            return false;
        }
        $member = $member->toArray();
        $isBind = (new DistributeModule())->isBind($member);
        if (!$isBind){
            return false;
        }
        $res = $this->updateData($mid,['pid'=>$id]);
        $this->increment($id,'son_num',1);
        $job = new SalesmanStatistic($mid);
        dispatch($job->onQueue('SalesmanStatistic'));
        if ($res){

            //何书哲 2018年11月09日 公众号绑定上下级消息通知
            $wid = app('request')->input('wid') ? app('request')->input('wid') : session('wid');
            (new MessagePushModule($wid, MessagesPushService::BecomeChild))->sendMsg(['pid'=>$id, 'mid'=>$mid]);
            (new MessagePushModule($wid, MessagesPushService::BecomeChild, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg(['pid'=>$id, 'mid'=>$mid]);

            return true;
        }else{
            return false;
        }
    }

    public function hasMobile($mid){
        $member = $this->getRowById($mid);
        return !!$member['mobile'];
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 增加积分
     * @desc
     * @param $id
     * @param $num
     * @return bool
     */
    public function increment($id,$field,$num)
    {
        $where = [
            'id'    => $id,
        ];
        $res = $this->model->wheres($where)->increment($field,$num);
        if ($res){
            $redis = new Member();
            $redis->increment($id,$field,$num);
            return true;
        }else{
            return false;
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 减少积分
     * @desc
     * @param $id
     * @param $num
     * @return bool
     */
    public function decrement($id,$field,$num)
    {
        $where = [
            'id'    => $id,
            $field =>['>=',$num],
        ];
        $res = $this->model->wheres($where)->decrement($field,$num);
        if ($res){
            $redis = new Member();
            $num = -$num;
            $redis->increment($id,$field,$num);
            return true;
        }else{
            return false;
        }
    }

    /**
     * todo 插入小程序中用户信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-08-18
     */
    public function insertData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='插入的数据为空';
            return $returnData;
        }
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    public function getList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        return parent::getList($where, $skip, $perPage, $orderBy, $order); // TODO: Change the autogenerated stub
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170921
     * @desc 对用户余额进行操作
     * @param $mid 用户id
     * @param $money 操作金额，大于0 为增加，小于 0 为减少
     * @return array  errCode -1,用户不存在,-2 账户余额不足，-3，操作失败，正确返回0，errMsg='错误信息'
     */
    public function operateMoney($mid,$money)
    {
        $result=['errCode'=>0,'errMsg'=>''];
        $memberData = $this->getRowById($mid);
        if (!$memberData){
            $result['errCode'] = -1;
            $result['errMsg'] = '用户不存在';
            return $result;
        }
        $money = intval($money);
        if ($money>0){
            $res = $this->increment($mid,'money',$money);
        }else{
            $money = abs($money);
            if ($memberData['money']<$money){
                $result['errCode'] = -2;
                $result['errMsg'] = '用户余额不足';
            }
            $res = $this->decrement($mid,'money',$money);
        }
        if ($res){
            return $result;
        }else{
            $result['errCode'] = -3;
            $result['errMsg'] = '操作失败';
            return $result;
        }
    }
    /**
     * @date 20170925
     * @desc 批量更新
     * @param $ids
     * @param $data
     * @return bool
     */
    public function batchUpdate($ids,$data)
    {
        $res = $this->model->whereIn('id',$ids)->update($data);
        if ($res){
            $redis = new Member();
            return $redis->batchUpdate($ids,$data);
        }else{
            return false;
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170929
     * @desc 根据手机号码获取信息
     * @param $mobile
     */
    public function getRowByMobile($mobile)
    {
         $data = $this->getList(['mobile'=>$mobile]);
         if ($data){
             return $data[0];
         }else{
             return [];
         }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171114
     * @desc 获取分销老数据
     * @param $wid
     */
    public function getDistributeOldData($wid)
    {
        $sql = 'SELECT m.id FROM ds_member as m INNER JOIN ds_member as pm ON m.id = pm.pid WHERE m.is_distribute=0 AND m.wid='.$wid.' GROUP BY m.id';
        $result = DB::select($sql);
        if (!$result){
            return [];
        }else{
            return json_decode(json_encode($result),true);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171115
     * @desc 获取用户id
     * @param $where
     */
    public function getMemberIds($where)
    {
        $result = $this->model->wheres($where)->get(['id'])->toArray();
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171117
     * @desc 搜索个人用户
     */
    public function searchMember($where=[],$input=[] )
    {
        foreach ($input as $key=>$val){
            if ($val){
                switch ($key){
                    case 'nickname':
                        $where['nickname'] = ['like','%'.$val.'%'];
                        break;
                    case 'truename':
                        $where['truename'] = ['like','%'.$val.'%'];
                        break;
                    case 'mobile':
                        $where['mobile'] = ['like','%'.$val.'%'];
                        break;
                }
            }
        }
        $where['status'] = 0;
       return  $this->getListWithPage($where, '', '',$input['pageSize']??0);

    }


    /*
     * @author fuguowei
     * @date  20180122
     * @desc 根据昵称不分页搜索
     * */
    public function getListByConditionPage($input)
    {
        /* 查询条件数组 */
        $where = ['status'=>0];

        /* 参数转换为查询条件数组 */
        if ( $input ) {
            foreach ($input as $key => $value) {
                if ( empty($value) ) {
                    continue;
                }
                switch ( $key ) {
                    // 微信昵称
                    case 'nickname':
                        $where['nickname'] = ['like','%'.$value.'%'];
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $this->getList($where);
    }

    /**
     *
     * Author: MeiJay
     * @param $xcx_openid
     * @return mixed
     */
    public function getRowByXcxOpenId($xcx_openid)
    {
        $data  = $this->model->where(['xcx_openid'=>$xcx_openid])->first();
        if ($data) {
            $data = $data->toArray();
        }
        return $data;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180507
     * @desc 根据where 获取数据
     */
    public function getPageByWhere($where)
    {
        return $this->getListWithPage($where, $orderBy = '', $order = '',$pageSize=0 );
    }


    /**
     *根据用户昵称获取用户id
     * @param $nickname 昵称
     * @return array id数组
     * @author 张永辉  2018年7月5日
     */
    public function getMidsByNickname($nickname)
    {
        $result = $this->model->where('nickname',$nickname)->get(['id'])->toArray();
        if ($result){
            return array_column($result,'id');
        }else{
            return [];
        }
    }

    /**
     * 获取分页数据
     * @param $input
     * @param string $orderBy
     * @param string $order
     * @return mixed
     * @author: 梅杰 2018年9月6号
     */
    public function getPage($input,$orderBy = '',$order = 'DESC')
    {
        /* 查询条件数组 */
        $where = ['m.status'=>0];
        /* 参数转换为查询条件数组 */
        if ( $input ) {
            foreach ($input as $key => $value) {
                if ( empty($value) ) {
                    continue;
                }
                switch ( $key ) {
                    case 'wid':
                        $where['m.wid'] = $value;
                        break;
                    // 手机号
                    case 'mobile':
                        $where['mobile'] = $value;
                        break;
                    // 微信昵称
                    case 'nickname':
                        $where['nickname'] = ['like','%'.$value.'%'];
                        break;
                    // 来源
                    case 'source':
                        $where['source'] = $value - 1;
                        break;
                    // 会员身份
                    case 'is_member':
                        $where['is_member'] = $value - 1;
                        break;
                    // 购次
                    case 'buy_num':
                        $where['buy_num'] = array('>=', $value);
                        break;
                    // 地域（省份筛选）
                    case 'province_id':
                        $where['province_id'] = $value;
                        break;
                    //openid
                    case 'openid':
                        $where['openid'] = $value;
                        break;
                    case 'xcx_openid':
                        $where['xcx_openid'] = $value;
                        break;
                    //微信号搜索
                    case 'wechat_id':
                        $where['wechat_id'] = $value;
                        break;
                    case 'id':
                        $where['m.id'] = ['in',$value];
                        break;
                    case 'sex':
                        $where['sex'] = $value;
                        break;
                    case 'is_distribute':
                        $where['is_distribute'] = $value;
                        break;
                    case 'truename':
                        $where['truename'] = $value;
                        break;
                    //add by wuxiaoping 2018.05.15
                    case 'is_pull_black':
                        $where['is_pull_black'] =$value;
                        $orderBy = 'updated_at';
                        $order = 'DESC';
                        break;
                    case 'visit_time':
                        if ($value == 1) {
                            $orderBy = 'latest_access_time';
                            $order = 'DESC';
                        }
                        break;
                    case 'is_member':
                        $where['is_member'] = $value;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        //增加按最近访问时间排序
        if (isset($input['latest_visit_time_start']) && isset($input['latest_visit_time_end'])) {
            $start = strtotime($input['latest_visit_time_start']);
            $end = $input['latest_visit_time_start'] == $input['latest_visit_time_end'] ? $start + 86400 : strtotime($input['latest_visit_time_end']);
            $where['latest_access_time'] = ['between',[date('Y-m-d H:i:s',$start),date('Y-m-d H:i:s',$end)]];
        }
        $filed = [
            "m.id","truename","nickname","m.source","mobile","remark",'latest_access_time',
            "is_member",'buy_num','nickname','is_pull_black','money','score','m.updated_at',
            "o.pay_price",DB::raw('SUM(pay_price) AS sum')
        ];
       return  DB::table('member as m')->select($filed)
            ->leftJoin('order as o','m.id','=','o.id')->wheres($where)->groupBy('m.id')->orderBy($orderBy,$order)->paginate(15);
    }

    /**
     * @description： 根据用户昵称获取同一店铺的用户列表数据
     * @param $wid    店铺id
     * @param $nickname 用户昵称
     *
     * @return array
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年09月24日 14:51:47
     */
    public function getListByNickname($wid, $nickname)
    {
        $mids = [];
        $res = $this->model->where('wid', $wid)->where('nickname', $nickname)->get(['id']);
        if (!$res->isEmpty()) {
            $mids = $res->pluck('id')->all();
        }
        return $mids;
    }

}
