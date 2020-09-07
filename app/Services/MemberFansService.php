<?php


namespace App\Services;

use App\Model\MemberFans;
use Illuminate\Support\Facades\DB;
use Redirect;
use RedisPagination;
use Redisx;
use Session;
use UploadedFile;
use Validator;

class MemberFansService extends Service
{
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id', 'wid', 'tag_id', 'gander', 'regions_id', 'fans_level', 'tao_level','tao_vip','cid','fans_name',
            'fans_logo', 'integral', 'tradeCount','avg_price','isw','follow_status','last_say_at','created_at','buy_at'];
        /* 设置闭包标识 */
        //$this->closure('capital');
    }

    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new MemberFans(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }

    public function verifyWhere( $verifyField = ['id'] ,$regionsArr = array() ){
        /* 接收数据 */ //['gander','fans_name','regions_id','fs']
        $input = $this->request->only($verifyField);
        $where = array();

        foreach ($verifyField as $value) {
            if(empty($input[$value]) && $value != 'fs' ){
                continue;
            }
            switch ($value) {
                case 'gander':
                    if(!empty($input[$value])) {
                        $where[$value] = $input[$value];
                    }
                    break;
                case 'fans_name':
                    //$where[$value] = ['>=', $input[$value]];
                    $where[$value] =  ['like',$input[$value].'%'];
                    break;
                case 'regions_id':
                    $regionsArray = explode(',',$input[$value]);
                    foreach ($regionsArray as $regionId) {
                        switch ($regionId) {
                            case '-2'://江浙沪
                                $regionsArrVal = array(2,12, 15);
                                $where[$value] =  ['in',$regionsArrVal];
                                $where['regions_default']['-2'] = '江浙沪';
                                break;
                            case '-3'://珠三角
                                    $regionsArrVal = array(1601, 1607, 1666, 1655, 1609, 1657, 1659, 1690, 1643);
                                    $where[$value] =  ['in',$regionsArrVal];
                                    $where['regions_default']['-3'] = '珠三角';
                                    break;
                            case '-4':////港澳台
                                $regionsArrVal = array(42, 43, 32);
                                $where[$value] =  ['in',$regionsArrVal];
                                $where['regions_default']['-4'] = '港澳台';
                                break;
                            case '-5'://京津
                                $regionsArrVal = array(1, 3);
                                $where[$value] =  ['in',$regionsArrVal];
                                $where['regions_default']['-5'] = '京津';
                                break;
                            default :
                                $regionsArrVal[] = $regionId;
                                $where[$value] =  ['in',$regionsArrVal];
                                foreach ($regionsArr as $rArr) {
                                    switch ($rArr->id) {
                                        case $regionId:
                                            $where['regions_default'][$regionId] = $rArr->title;
                                            break;
                                        default :
                                            // code ...
                                            break;
                                    }
                                }
                                break;
                        }
                    }
                    break;
                case 'fs':
                        $fs = $input['fs'];
                        if($fs == 1){
                            $where['isw'] =  $fs;
                        }else if($fs === '0'){
                            $where['isw'] =  $fs;
                        }else if($fs == 2){
                            $where['follow_status'] =  0;
                        }else if($fs == 3){
                            $where['follow_status'] =  1;
                        }else{
                            // code ...
                        }
                    break;
                default :
                    // code ...
                    break;

            }

        }

        return $where;
    }

    public function verify( $verifyField = ['id'] )
    {
        /* 接收数据 */ //['gander','fans_name','regions_id','fs']
        $input = $this->request->only($verifyField);
        /* 获取验证数据和提示消息 */
        $rules = [];
        $messages = [];

        foreach ($verifyField as $value) {
            switch ( $value ) {
                /* 订单号 */
                case 'id':
                    $rules['id'] = 'required';
                    $messages['id.required'] = '粉丝不存在';
                    break;
//                /* 标签名 */
//                case 'rule_name':
//                    $rules['rule_name'] = 'required|between:6,30';
//                    $messages['rule_name.required'] = '请输入标签';
//                    $messages['rule_name.between'] = '请输入6-30位字符长度的标签';
//                    break;
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

        return $input;
    }

    //累加 积分
    public function increment($datas,$ajaxFlag=true){

        $save = $this->model->wheres($this->where)->increment($datas);
        if($save){
            $fansData = $this->model->select(['id','integral'])->wheres($this->where)->get()->toArray();
            $redisField= ['id','integral'];
            RedisPagination::update($fansData, $redisField);
            $ajaxFlag && success();
            return true;
        }
        return false;
    }

    public function update($datas,$ajaxFlag=true){
        $data = array();
        foreach ($datas as $key=>$d){
            if($key=='id'){
                continue;
            }
            while($d){
                $data[$key] = $d;
                $save = $this->model->wheres($this->where)->update($data);
                if($save){
                    $redisDatas = array();
                    $redisField= ['id',$key];
                    foreach ($datas['id'] as $k=>$d){
                        $redisDatas[$k]['id']  = $d;
                        $redisDatas[$k][$key]  = $data[$key];
                    }
                    RedisPagination::update($redisDatas, $redisField);
                    //$ajaxFlag && success();
                    return true;
                }
                $ajaxFlag && error();
                return false;
            }

        }
    }

    public function getTagNum(){
        return $this->model->select(DB::raw('count(*) as tag_counts,tag_id'))->groupBy('tag_id')->get()->toArray();
    }

    public function getRequest($verifyField){
        return $this->request->only($verifyField);
    }

}