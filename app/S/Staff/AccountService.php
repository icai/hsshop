<?php

/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  14:52
 * DESC
 */
namespace App\S\Staff;
use App\Jobs\LoginStatistics;
use App\Lib\Redis\AccountRedis;
use App\Lib\Redis\RedisClient;
use App\S\S;
use Hash;
use StaffOperLogService;

class AccountService extends S
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        parent::__construct('Account');
    }



    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703131521
     * @desc 验证用户名和密码是否正确
     * @param $loginName
     * @param $loginPassword
     * @return array
     * @update 何书哲 2018年9月19日 登录日志发送数据中心
     */
    public  function  checkUser($loginName,$loginPassword)
    {
        $result = Array('success'=>0,'message'=>'');
        //获取登陆用户信息
        $where = Array();
        $where['login_name'] = $loginName;
        $where['status'] = 1;
        $redisClient = (new RedisClient())->getRedisClient();
        /*if ($redisClient->get($this->_getKey($loginName))){
            $result['message'] = '该账号错误次数过多锁定一小时';
            return $result;
        }*/

        $userData = $this->model->wheres($where)->get()->toArray();
        if (!$userData){
            $result['message'] = '账号不存在或已禁用';
            return $result;
        }
        $upData['id'] = $userData[0]['id'];
        if (!Hash::check($loginPassword,$userData[0]['login_password'])){
            $upData['wrongs'] = $userData[0]['wrongs']+1;
            if ($upData['wrongs']>=3){
                $this->setWrongTimes($loginName);
            }
            $this->update($userData[0]['id'],$upData);
            $result['message'] = '账号或密码错误';
            return $result;
        }else{
            $upData['logins'] = $userData[0]['logins']+1;
            $upData['login_time'] = date('Y-m-d H:i:s');
            $upData['wrongs'] = 0;
//            $this->init()->where(['id'=>$userData[0]['id']])->update($upData,false);
            $this->update($userData[0]['id'],$upData);
            unset($userData['login_password']);
            $userData[0]['last_login_time'] = $userData[0]['login_time'];
            $userData[0]['login_time'] =  $upData['login_time'];
            $userData[0]['is_login'] = 1;
            session(['userData'=>$userData[0]]);
            $this->request->session()->save();
            //写入登陆日志
            StaffOperLogService::write('用户登陆');
            //何书哲 2018年9月19日 登录日志发送数据中心
            dispatch((new LoginStatistics($userData[0]['id'], getIP(), 2))->onQueue('LoginStatistics'));
            $result['success'] = 1;
            return $result;
        }
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703131547
     * @desc 添加后台用户
     * @return array
     */
    public function  addUser()
    {
        $userData = Array(
            'login_name'           => $this->request->input('loginName'),
            'login_password'       => bcrypt($this->request->input('loginPasswd')),
            'name'                  => $this->request->input('name'),
            'status'                => $this->request->input('status'),
        );
        $result = Array('success'=>0,'message'=>'');

        $selfData = session('userData');
        if ($selfData['is_super'] == 0 && $this->request->input('is_super')==1){
            $result['message'] = '您无权限设置超级用户';
            return $result;
        }
        $userData['is_super'] = $this->request->input('is_super')??0;
        $res = $this->model->where('login_name',$userData)->get()->toArray();
        if ($res && !$this->request->input('id')){
            $result['message'] = '该账号已存在';
            return $result;
        }
        if ($this->request->input('id')){
			$userData['id'] = $this->request->input('id');
            StaffOperLogService::write('修改账号信息,'.json_encode($userData));
//			$this->init()->where(['id'=>$userData['id']])->update($userData);
			$res = $this->update($userData['id'],$userData);
			if ($res){
			    $result['success'] = 1;
            }
            return $result;
		}else{
			if ($this->add($userData)){
				StaffOperLogService::write('添加后台登陆账户,'.json_encode($userData));
				$result['success'] = 1;
				return $result;
			}else{
				$result['message'] = '添加失败!请稍后重试!';
				return $result;
			}
		}


    }



    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id获取列表
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new AccountRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->addArr($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new AccountRedis();
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

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        if ($res){
            $storeRedis = new AccountRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new AccountRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage()
    {
        return $this->getListWithPage([], '', '');
    }



    public function validPasswd($data) {
        $r1='/[A-Z]/';
        $r2='/[a-z]/';
        $r3='/[0-9]/';
        $r4='/[~!@#$%^&*()\-_=+{};:<,.>?]/';
        $result = ['errCode'=> '0','errMsg'=>''];
        if(preg_match_all($r1,$data, $o)<1) {
            $result['errCode'] = 4001;
            $result['errMsg'] = '密码必须包含一个大写字母';
            return $result;
        }
        if(preg_match_all($r2,$data, $o)<1) {
            $result['errCode'] = 4001;
            $result['errMsg'] = '密码必须包含一个小写字母';
            return $result;
        }
        if(preg_match_all($r3,$data, $o)<1) {
            $result['errCode'] = 4001;
            $result['errMsg'] = '密码必须包含一个数字';
            return $result;
        }
        if(preg_match_all($r4,$data, $o)<1) {
            $result['errCode'] = 4001;
            $result['errMsg'] = '密码必须包含一个特殊符号';
            return $result;
        }
        return $result;
    }

    /**
     * 写入错误标识
     * @param $loginName
     * @return bool
     * @author 张永辉 2018年9月3日
     */
    public function setWrongTimes($loginName)
    {
        $redisClient = (new RedisClient())->getRedisClient();
        $key = $this->_getKey($loginName);
        $redisClient->SET($key,1);
        $timeOut = 3600;
        $redisClient->EXPIRE($key, $timeOut);
        return true;
    }

    private function _getKey($loginName){
        return 'staff:login:wrongs:'.$loginName;
    }

}























