<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MemberServiceProvider extends ServiceProvider {
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
        $this->app->bind('MemberService', function () {
            return new \App\S\Member\MemberService();
        });

        $this->app->bind('MemberCardService', function () {
            return new \App\S\Member\MemberCardService();
        });

        $this->app->bind('MemberImportService', function () {
            return new \App\Services\MemberImportService();
        });

        $this->app->bind('MemberLabelService',function(){
            return new \App\Services\MemberLabelService();
        });

        $this->app->bind('MemberCardRecordService',function(){
            return new \App\Services\MemberCardRecordService();
        });

        $this->app->bind('MemberFansService',function(){
            return new \App\Services\MemberFansService();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['MemberService', 'MemberCardService', 'MemberImportService', 'MemberLabelService', 'MemberCardRecordService', 'MemberFansService'];
    }
}
