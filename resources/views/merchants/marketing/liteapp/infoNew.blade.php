@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/liteapp_qwhj4x9w.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.liteapp.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">微信小程序</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>
        <!-- 三级导航 结束 -->
        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection
@section('content')
    <div class="content">

        {{--<ul class="tab_nav">--}}
            {{--<li>--}}
                {{--<a href="/merchants/marketing/litePage">小程序微页面</a>--}}
            {{--</li>--}}
            {{--<li>--}}
                {{--<a href="/merchants/marketing/footerBar">底部导航</a>--}}
            {{--</li>--}}
            {{--<li class="">--}}
                {{--<a href="/merchants/marketing/xcx/topnav">首页分类导航</a>--}}
            {{--</li>--}}
            {{--<li class=""> <!-- update 梅杰 新增列表页-->--}}
                {{--<a href="/merchants/marketing/xcx/list">小程序列表</a>--}}
            {{--</li>--}}
            {{--<li class="">--}}
                {{--<a href="/merchants/marketing/liteStatistics">数据统计</a>--}}
            {{--</li>--}}
            {{--<li class="hover">   <!-- update 梅杰 20180710 点击链接 结束 -->--}}
                {{--<a href="/merchants/marketing/Info?id={{ $id }}">小程序设置</a>--}}
            {{--</li>--}}
        {{--</ul>--}}
        <div class="lite_top">
            <p class="top_p">小程序</p>
        </div>
        <div class="lite-con">
            <div class="lite_flx">
                <p class="title_p">小程序:</p>
                <p class="res_title"></p>
            </div>
            <div class="lite_flx">
                <p class="title_p">线上版本:</p>
                <p class="res_version"></p>
            </div>
            <div class="lite_flx">
                <p class="title_p">更新时间:</p>
                <p class="res_time"></p>
            </div>
            <div class="lite_flx">
                <p class="title_p">更新状态:</p>
                <p class="state_p"></p>
            </div>
            <div class="lite_flx">
                <p class="title_p">审核说明:</p>
                <p class="reason"></p>
            </div>
            <a class="title_a ml90" data-toggle="modal" data-target="#myModal">解除授权</a>
            <a class="title_a updateauthorized" target="_blank" href="javascript:;">更新授权</a>
            <!--模态框-->
            <div class="modal fade" tabindex="-1" role="dialog" id="myModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">提示</h4>
                        </div>
                        <div class="modal-body">
                            <p class="red">解除绑定小程序，会造成线上小程序异常，请谨慎操作！</p>
                            <label class="mto20 mt_lab">
                                <input type="checkbox" name="" class="mt_che" value="" />
                                已知晓解除绑定的风险，确认解绑
                            </label>
                            <p class="phide f70 hide">注意:</p>
                            <p class="phide hide">1.1个小程序只能和1个会搜云店铺绑定；</p>
                            <p class="phide hide">2.会搜云小程序后台提供了「解除授权」功能，可将小程序和店铺解除关联，解除授权后，如果这个小程序之前有在线上运行，将不能再正常运行。</p>
                        </div>
                        <div class="modal-footer">
                            <input type="button" name="" class="btn bangd btn-jiec form_remov" value="解除绑定" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--模态框-->
        </div>
        <div class="lite_top">
            <p class="top_p">小程序微信支付</p>
        </div>
        <form id="form" class="sub_form">

            <div class="lite_flx">
                <p class="title_p"><span class="asterisk">*</span>开发者ID(AppID):</p>
                <input type="" name="app_id" class="disabled" value="" />
            </div>
            <div class="lite_flx">
                <p class="title_p"><span class="asterisk">*</span>小程序密钥(AppSecret):</p>
                <input type="" name="app_secret" class="disabled" value="" />
            </div>
            <div class="lite_flx">
                <p class="title_p">商户号:</p>
                <input type="" name="merchant_no"  class="disabled" value="" />
                <p class="point hide">请填写当前微信小程序所对应的微信支付商户号</p>
            </div>
            <div class="lite_flx">
                <p class="title_p">商户密钥:</p>
                <input type="" name="app_pay_secret" class="disabled" value="" />
                <p class="point hide">请登录微信商户平台，进入[账户中心-API安全]页面，设置密钥  <a href="https://www.huisou.cn/home/index/detail/194/help">查看教程</a></p>
            </div>
            <div class="lite_flx checkbox hide">
                <input type="checkbox"><p>已确认商户号和商户密钥配置正确（否则将导致微信支付异常）</p>
            </div>
            <a class="title_a ml90 modify">修改配置</a>
            <button type="button" class="ml90 btn btn-primary save-mod">保存修改</button>
        </form>

        <!--小程序证书上传 梅杰 20180720 -->
        <div class="lite_top">
            <p class="top_p">小程序微信支付商戶书
                @if($mini_flag == 1)
                    <b>（已上传 如有需要请重新上传）</b>
                @else
                    <b>（未上传）</b>
                @endif
            </p>
        </div>
        <form class="uploadForm" enctype="multipart/form-data">

            <div class="lite_flx row1">
                <p class="title_p"><span class="asterisk">*</span>apiclient_cert.pem文件:</p>
                <input type="file" name="file_cert" id="file_cert" accept=".pem">
                @if($mini_flag == 1) <a href="{{url('merchants/currency/downLoadCert?type=1&id='.$id)}}" class="title_a ml90 ">点击下载</a> @endif
            </div>
            <div class="lite_flx row1">
                <p class="title_p"><span class="asterisk">*</span>apiclient_key.pem文件:</p>
                <input type="file" name="file_key" id="file_cert" accept=".pem">
                @if($mini_flag == 1) <a href="{{url('merchants/currency/downLoadCert?type=2&id='.$id)}}" class="title_a ml90 ">点击下载</a> @endif
            </div>
            <input type="hidden" name="type" value="2">
            <button type="button" class="ml90 btn btn-primary certSubBtn">提交保存</button>
        </form>
        <!-- end 小程序证书上传 梅杰 20180720 -->


        <div class="lite_top">
            <p class="top_p">小程序版本信息</p>
        </div>
        <div class="switch-auto">
            <div class="title_p">版本更新:</div>
            <div class="auto-update clearfix">
                <span>开启后，更改底部导航，可直接提交审核</span>
                <span class="switch fr"></span>
            </div>
        </div>
        <form class="update_form" style='padding-bottom: 20px;'>
            <div class="lite_flx">
                <p class="title_p">小程序:</p>
                <p class="title_p text-left name"></p>
            </div>
            <div class="lite_flx">
                <p class="title_p">线上版本:</p>
                <p class="title_p text-left edition"></p>
            </div>
            <div class="lite_flx">
                <p class="title_p">线上版本更新时间:</p>
                <p class="title_p text-left time"></p>
            </div>
            <div class="lite_flx clearfix">
                <p class="title_p hide" id="updata">更新状态:</p>
                <div class=" text-left status audit_failure hide" id='audit_failure'>
                    <p class="audit_title">小程序(<span id='audit_span'></span>)微信审核失败</p>
                    <div class="audit_reason">
                        <p class='audit_reason_p'>失败原因:</p>
                        <div class='audit_reason_div' id='audit_box'>
                            <p>1.小程序内容不符合规格:</p>
                            <P>(1).个人主体类型不支持:商家自营/电商模式(页面含购物车、订单查询等模块)，建议申请企业主体类型小程序</P>
                        </div>
                    </div>
                </div>
            </div>
            <span class="btn-d" style="cursor: pointer">提交稳定版</span>
        </form>

        <!--流量主信息设置 何书哲 20181010 -->
        <div class="lite_top">
            <p class="top_p">小程序流量主</p>
        </div>
        <form id="form_unit" class="sub_form">
            <div class="lite_flx">
                <p class="title_p"><span class="asterisk">*</span>流量主(unitId):</p>
                <input type="" name="unit_id" class="disabled" value="" />
            </div>
            <a class="title_a ml90 modify_unit">修改配置</a>
            <button type="button" class="ml90 btn btn-primary save-mod-unit">保存修改</button>
        </form>
        <!--流量主信息设置 end -->

    </div>
    <div class='loadding hide'>
        <div class='load-box'>
            <div class='load-gif'></div>
            <div>提交中...</div>
        </div>

    </div>
@endsection
@section('page_js')
    <script type="text/javascript">
        var id = {{ $id }}
        $(".authorize").click(function(){
            $.ajax({
                type: "GET",
                url: "/merchants/xcx/authorizer",
                data:"",
                async: true,
                success: function(res) {
                    window.open(res.data)
                    $('.modal').modal('show');
                    $(".in").show();
                },
                error:function(){
                    alert("数据访问错误")
                }
            })
        })


        //上传文件显示 && 报错
        $(".row1").on("change","input[type='file']",function(){
            var filePath=$(this).val();
            console.log($(this).siblings('.input_shade').children('b'))
            if(filePath.indexOf("pem")!=-1){
                $(this).siblings('.input_shade').children('b').html("");
                var arr=filePath.split('\\');
                var fileName=arr[arr.length-1];
                $(this).siblings('.input_shade').children('b').html(fileName);
            }else{
                $(this).siblings('.input_shade').children('b').html("");
                $(this).siblings('.input_shade').children('b').html("您上传文件类型有误").show();
                return false
            }
        })


            $('.certSubBtn').on('click', function(){
                var formData = new FormData($(".uploadForm")[0]);
            $.ajax({
                    url: '/merchants/currency/cert?id='+ id,
                    type: 'POST',
                    cache: false,
                    data: formData,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(res) {
                        if(res.status == 1){
                            tipshow(res.info,'info');
                            setTimeout(function(){
                                window.location.reload();
                            },2000);
                        }else{
                            tipshow(res.info,'warn');
                        }
                    },
                    error:function(){

                    }
                })
        })
    </script>
    <script src="{{ config('app.source_url') }}mctsource/js/liteappinfo_bd3sozui.js" type="text/javascript" charset="utf-8"></script>
@endsection