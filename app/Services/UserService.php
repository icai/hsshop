<?php
namespace App\Services;

use App\Model\User;
use App\Module\BaseModule;
use Validator;
use WeixinService as WXService;
use Hash;
use App\Lib\Redis\UserRedis;

/**
 * 权限设置
 */
class UserService extends Service
{
    /**
     * 构造方法
     *
     *
     * @return void
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id', 'mphone', 'email', 'name', 'logins', 'created_at'];

        /* 设置闭包标识 */
       // $this->closure('capital');
        // 所有关联关系
        $this->withAll = ['userInfo'];
    }

    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {

        $this->initialize(new User(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    public function getUserList()
    {
        $input = $this->request->input();
        $where = ['1'=>1];
        if (isset($input['mphone']) && !empty($input['mphone'])){
            $where['mphone'] = ['like','%'.$input['mphone'].'%'];
        }
        if (isset($input['name']) && !empty($input['name'])){
            $where['name'] = ['like','%'.$input['name'].'%'];
        }
        $shopData = $this->init()->where($where)->order('id desc')->getList();
        return $shopData;
    }

    //根据店铺获取用户信息
    public function getInfoByWid($wid)
    {
        $shop = WXService::init()->where(['id' => $wid])->getInfo($wid);
        $uid = $shop['uid'] ?? 0;
        $info = [];
        if ($uid) {
            $info = $this->init()->where(['id' => $uid])->getInfo($uid);
        }

        return $info;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180227
     * @desc 注册用户信息
     * @param $data
     * @update 梅杰 增加批量注册与单个注册判断
     */
    public function addUser($data)
    {
        $result = ['errCode'=>0,'errMsg'=>''];
        if (count($data) != count($data,1)) {
            foreach ($data as $val){
                if ($this->_checkUserIseExist($val['mphone'])){
                    continue;
                }
                $password = $val['password']??12345678;
                $userData = [
                    'mphone'        => $val['mphone'],
                    'email'         => $val['email']??'',
                    'name'          => $val['name']??$val['mphone'],
                    'head_pic'      => $val['head_pic']??'',
                    'password'      => bcrypt($password),
                ];
                $res = $this->init()->model->insertGetId($userData);
                if (!$res){
                    \Log::info('herry注册失败：'.$val['mphone']);
                }
            }
            $result['errCode'] = 0;
            return $result;
        }

        if ($this->_checkUserIseExist($data['mphone'])){
            return ['errCode'=>1,'errMsg'=>'该用户已存在'];
        }
        $password = $data['password']??12345678;
        $userData = [
            'mphone'        => $data['mphone'],
            'email'         => $data['email']??'',
            'name'          => $data['name']??$data['mphone'],
            'head_pic'      => $data['head_pic']??'',
            'password'      => bcrypt($password),
        ];
        if ($this->init()->model->insertGetId($userData)) {
            \Log::info('注册失败：'.$data['mphone']);
            return $result;
        }
        return ['errCode'=>2,'errMsg'=>'创建失败'];
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180227
     * @desc 检查注册信息是否合法
     */
    private function _checkData($data)
    {
        foreach ($data as $val){
            if(!isset($val['mphone']) || !preg_match("/^1[34578]{1}\d{9}$/",$val['mphone'])){
                return false;
            }
        }
        return true;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180227
     * @desc 检查用户是否存在
     * @param $mphone
     */
    private  function _checkUserIseExist($mphone)
    {
        return !!$this->init()->model->where('mphone',$mphone)->get()->toArray();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180305
     * @desc 验证用户登陆
     * @param $mobile
     * @param $passwd
     */
    public function checkUserLogin($mobile,$passwd,$token)
    {
        $result = ['errCode'=> 0,'errMsg'=>''];
        $user = $this->init()->model->where('mphone',$mobile)->get()->toArray();
        if (!$user){
            $result['errCode'] = 4002;
            $result['errMsg']   = '用户不存在';
            return $result;
        }
        $user = current($user);
        if ( Hash::check($passwd, $user['password']) === false ) {
            $result['errCode'] = 4003;
            $result['errMsg']   = '账号或密码错误';
            return $result;
        }
        $this->init()->model->where('mphone',$mobile)->increment('logins');
        $data = [
            'is_login'      => 1,
            'userInfo'      => $user,
        ];
        $res = (new BaseModule())->setDataInToken($token,$data);
        if ($res['errCode'] == 0){
            $result['data'] = $user;
            return $result;
        }else{
            return $res;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date $20180306
     * @desc 修改密码
     * @param $passwd
     * @param $newpasswd
     */
    public function modifyPasswd($mobile,$passwd,$newpasswd)
    {
        $result = ['errCode'=> 0,'errMsg'=>''];
        $user = $this->init()->model->where('mphone',$mobile)->get()->toArray();
        if (!$user){
            $result['errCode'] = 40002;
            $result['errMsg']   = '用户不存在';
            return $result;
        }
        $user = current($user);
        if ( Hash::check($passwd, $user['password']) === false ) {
            $result['errCode'] = 40003;
            $result['errMsg']   = '原密码错误';
            return $result;
        }
        $newpasswd = bcrypt($newpasswd);
        $res = $this->init()->model->where('id',$user['id'])->update(['password' => $newpasswd]);
        if ($res){
            return $result;
        }else{
            $result['errCode'] = 40011;
            $result['errMsg']   = '修改密码失败';
            return $result;
        }


    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180315
     * @desc 修改手机号码
     * @param $mobile
     * @param $passwd
     */
    public function forgetPasswd($mobile,$passwd)
    {
        $result = ['errCode'=> 0,'errMsg'=>''];
        $user = $this->init()->model->where('mphone',$mobile)->get()->toArray();
        if (!$user){
            $result['errCode'] = 40002;
            $result['errMsg']   = '用户不存在';
            return $result;
        }
        $user = current($user);
        $newpasswd = bcrypt($passwd);
        $res = $this->init()->model->where('id',$user['id'])->update(['password' => $newpasswd]);
        if ($res){
            return $result;
        }else{
            $result['errCode'] = 40011;
            $result['errMsg']   = '修改密码失败';
            return $result;
        }
    }

    /***
     * todo 查询会员数据
     * @param array $data 查询条件
     * @param string $orderBy 排序字段
     * @param string $order 正序/倒序
     * @param int $pageSize 页面数
     * @return array
     * @author jonzhang
     * @date 2018-06-25
     */
    public function getListByCondition($data=[],$orderBy='',$order='',$pageSize=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        /* 查询条件数组 */
        $where = [];
        /* 参数转换为查询条件数组 */
        foreach ($data as $key => $value)
        {
            switch ( $key )
            {
                //手机号码
                case 'mphone':
                    $where['mphone'] = $value;
                    break;
                case 'id':
                    $where['id']=$value;
                    break;
            }
        }
        //查询数据
        $select= $this->init()->model->where($where);
        if(!empty($orderBy))
        {
            $select=$select->orderBy($orderBy,$order??'desc');
        }
        else
        {
            $select=$select->orderBy('id','desc');
        }
        //查询出id,此处为数组
        if(!empty($pageSize))
        {
            //$select->paginate($pageSize)返回的是:LengthAwarePaginator
            //$select->paginate($pageSize)->toArray() 返回的是数组 "total" => 7, "per_page" => 20,"current_page" => 1
            $select=$select->paginate($pageSize)->toArray();
            $idAttr=$select['data'];
            $returnData['total']=$select['total'];
            $returnData['currentPage']=$select['current_page'];
            $returnData['pageSize']=$select['per_page'];
        }
        else
        {
            //$select->get()返回的是集合
            $idAttr=$select->get()->toArray();
        }
        $returnData['data'] = $idAttr;
        return $returnData;
    }

    /**
     * 保存错误登录次数
     * @author 吴晓平 <2018年09月05日>
     * @param string $key [保存到redis的key值]
     */
    public function setErrLogins($key='')
    {
        $userRedis = new UserRedis($key);
        $userRedis->loginIncr();
    }

    /**
     * 获取错误登录次数
     * @author 吴晓平 <2018年09月05日>
     * @param  string $key [保存到redis的key值]
     * @return [int]      [数量]
     */
    public function getErrLogins($key='')
    {
        $userRedis = new UserRedis($key);
        $nums = $userRedis->get();
        return $nums;
        
    }

    /**
     * 清除登录用户错误次数
     * @author 吴晓平 <2018年09月05日>
     * @param  string $key [保存到redis的key值]
     * @return [type] [description]
     */
    public function cleanErrLogins($key='')
    {
        $userRedis = new UserRedis($key);
        return $userRedis->del($key);
    }


    /**
     * @desc 判断是否是弱密码
     * @param $passwd 密码明文
     * @return bool 是否是弱密码
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2020 年 07 月 01 日
     */
    public static function checkIsWeakPasswd($passwd)
    {
        $len = mb_strlen($passwd);
        if ($len < 8) {
            return true;
        }
        $count = 0;
        if (preg_match('/[0-9]/', $passwd)) {
            $count++;
        }
        if (preg_match('/[a-zA-Z]/', $passwd)) {
            $count++;
        }
        if (preg_match('/\W/', $passwd)) {
            $count++;
        }
        if ($count >= 2) {
            return false;
        } else {
            return true;
        }
    }



}