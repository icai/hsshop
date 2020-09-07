<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/10/20
 * Time: 15:37
 */

namespace App\Lib;
use Monolog\Logger;
use Illuminate\Log\Writer;


class BLogger
{
    // 所有的LOG都要求在这里注册
    const LOG_ERROR = 'error';

    private static $loggers = array();

    private static $dc_loggers = array();

    // 获取一个实例
    public static function getLogger($type = self::LOG_ERROR,$filename='xcx', $day = 30)
    {
        if (empty(self::$loggers[$type])) {
            self::$loggers[$type] = new Writer(new Logger($type));
            self::$loggers[$type]->useDailyFiles(storage_path().'/logs/'. $filename .'.log', $day);
        }
        $log = self::$loggers[$type];
        return $log;
    }


    // 获取一个实例
    public static function getDCLogger($type = self::LOG_ERROR,$fileType = 'user_pv')
    {
        if (empty(self::$dc_loggers[$type])) {
            self::$dc_loggers[$type] = new Writer(new Logger($type));
            self::$dc_loggers[$type]->useDailyFiles(public_path('hsshop/dc_log/').$fileType.'/'."$fileType.log");
        }
        $log = self::$dc_loggers[$type];
        return $log;
    }

    // 获取一个实例
    public static function getBindLogger($type = self::LOG_ERROR,$filename='bind', $day = 30)
    {
        if (empty(self::$loggers[$type])) {
            self::$loggers[$type] = new Writer(new Logger($type));
            self::$loggers[$type]->useDailyFiles(storage_path().'/logs/'. $filename .'.log', $day);
        }
        $log = self::$loggers[$type];
        return $log;
    }


}