<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2019/7/8
 * Time: 9:19
 * Desc: curl 异常处理类
 */

namespace App\Exceptions;


use Throwable;

class CommonException extends \Exception
{
    /**
     * 异常时需要跳转的地址
     *
     * @var string
     */
    public $url;

    /**
     * 异常时传输的数据
     *
     * @var array
     */
    public $data;

    /**
     * CurlException constructor.
     * @param string $message 错误信息
     * @param int $code 错误码
     * @param array $data 异常数据
     * @param string $url 异常时跳转的地址
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $data = null, $url = "", $code = 0, Throwable $previous = null)
    {
        $this->url = $url;
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }
}