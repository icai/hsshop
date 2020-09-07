<?php
/**
 * Created by PhpStorm.
 * User: hgh
 * Date: 2018/4/2
 * Time: 19:34
 */

namespace App\Module;


use JPush\Client;

class JPushModule
{
    CONST JPUSH_NOTIFICATION  = 'Notification';  //通知
    CONST JPUSH_MESSAGE       = 'Message';       //自定义
    CONST JPUSH_RICH          = 'Rich';          //富文本
    CONST JPUSH_LOCAL         = 'Local';         //本地


    private  $_client;
    private  $_pushPayLoad;

    public function __construct(){
        $this->_setClient();
    }

    private function  _setClient(){
        $appKey = config('app.app_jpush_key');
        $masterSecret = config('app.master_jpush_secret');
        $this->_client = new Client($appKey,$masterSecret);
    }

    public function push($msg, $type=self::JPUSH_NOTIFICATION, $alias=[]){
        $this->_pushPayLoad = $this->_client
            ->push()
            ->setPlatform('all')
            ->addAlias($alias);
        switch ($type){
            case self::JPUSH_NOTIFICATION:
                $this->_pushNotification($msg);
                break;
            case self::JPUSH_MESSAGE:
                $this->_pushMessage($msg);
                break;
            case self::JPUSH_RICH:
                break;
            case self::JPUSH_LOCAL:
                break;
        }
        $response = $this->_pushPayLoad->send();
        return $response;
    }

    private function _pushNotification($msg){
        $this->_pushPayLoad->setNotificationAlert($msg)
            ->iosNotification($msg)
            ->androidNotification($msg);
    }

    private function _pushMessage($msg){
        $this->_pushPayLoad->message($msg);
    }


}