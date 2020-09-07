<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/25
 * Time: 17:16
 */

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\S\File\FileInfoService;


class FileInfoServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * 在容器中注册绑定
     *
     * @return void
     */
    public function register() {
        $this->app->bind('fileInfoService', function () {
            return new FileInfoService();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['fileInfoService'];
    }
}