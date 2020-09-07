<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/12/18
 * Time: 17:36
 */

namespace App\S\Wechat;


/**
 * Class WechatErrorService
 * @package App\S\Wechat
 * 目前只处理了微信菜单相关微信错误码
 */
class WechatErrorService
{

    public function handle($err_code)
    {
        switch ($err_code){
            case 40015:
                $msg = '不合法的菜单类型';
                break;
            case 40016:
            case 40017:
                $msg = '不合法的按钮个数';
                break;
            case 40018:
                $msg = '不合法的菜单名字长度';
                break;
            case 40019:
                $msg = '不合法的按钮KEY长度';
                break;
            case 40020:
                $msg = '不合法的菜单URL长度';
                break;
            case 40021:
                 $msg = '不合法的菜单版本号';
                break;
            case 40022:
                 $msg = '不合法的子菜单级数(最多2级)';
                 break;
            case 40023:
                $msg = '不合法的子菜单按钮个数(最多5个)';
                break;
            case 40024:
                $msg = '不合法的子菜单按钮类型';
                break;
            case 40025:
                $msg = '不合法的子菜单按钮名字长度';
                break;
            case 40026:
                $msg = '不合法的子菜单按钮KEY长度';
                break;
            case 40027:
                $msg = '不合法的子菜单按钮URL长度';
                break;
            case 40028:
                $msg = '不合法的自定义菜单使用用户';
                break;
            case 40166:
                $msg = '菜单中小程序appid配置错误';
                break;
            case 45010:
                $msg = '创建菜单个数超过限制';
                break;
            case 85005:
                $msg = '小程序appid错误(请检查公众号是否与菜单中设置的小程序已关联绑定）';
                break;
            case 40054:
                $msg = '菜单中含有非法外链';
                break;
            default:
                $msg = '未定义错误信息';
        }
        return $msg;
    }
}