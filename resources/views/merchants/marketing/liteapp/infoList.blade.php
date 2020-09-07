@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/liteapp_qwhj4x9w.css" />
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/liteapp_1qdhfeb3.css" />
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
                {{--<li>--}}
                    {{--<a href="{{ URL('/merchants/marketing') }}">营销中心</a>--}}
                {{--</li>--}}
                <li>
                    小程序列表
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
            {{--<li class="hover"> <!-- update 梅杰 新增列表页-->--}}
                {{--<a href="/merchants/marketing/xcx/list">小程序列表</a>--}}
            {{--</li>--}}
            {{--<li class="">--}}
                {{--<a href="/merchants/marketing/liteStatistics">数据统计</a>--}}
            {{--</li>--}}
        {{--</ul>--}}
        <div class="lite_top">
            <p class="top_p">小程序列表    &nbsp; @if(!empty($flag)) <a class="authorize" href="javascript:void(0)">授权微信小程序</a> @endif</p>
        </div>
        <div class="lite-con">
            <table class="data-table table table-hover">
                <!-- 标题 -->
                <tr class="active">
                    <th>小程序名称</th>
                    <th>小程序应用id(AppID)</th>
                    <th>小程序应用密钥(AppSecret)</th>
                    <th>
                        授权时间
                    </th>
                    <th>操作</th>
                </tr>
            @foreach($list as $v)
                <!-- 列表 -->
                    <tr>
                        <td>{{ $v['title'] }}</td>
                        <td>
                            {{ $v['app_id'] }}
                        </td>
                        <td>
                            {{ $v['app_secret'] }}
                        </td>
                        <td>
                            {{ $v['created_at'] }}
                        </td>
                        <td>
                            <a href="/merchants/marketing/Info?id={{ $v['id'] }}">小程序设置</a>
                            @if($v['template_id'] && $v['request_domain'] && $v['page_list']  && $v['category_list'])
                                <a href="javascript:void(0);" class="code" id="{{ $v['id'] }}">访问小程序</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            {{ $html }}
        </div>
        <!--  显示小程序码-->
        <!-- 访问小程序 -->
        <div class="xcx-mask hide">
            <div class="xcx-wrap">
                <img class="xcx-wrap-close" src="{{ config('app.source_url') }}mctsource/images/guanbi-x.png" alt="">
                <dl>
                    <dd>微信“扫一扫”访问小程序</dd>
                    <dd style="height:262px;">
                        <img id="img_xcxm" src="" class="xcx-xcximg" />
                    </dd>
                    <dd data-url="pages/index/index" >
                        <a id="path_xcxm" data-url="pages/index/index" href="javascript:;">小程序路径</a>
                    </dd>
                    <dd>
                        <a id="down_xcxm" href="javascript:;">下载小程序二维码</a>
                    </dd>
                </dl>
            </div>
        </div>
        @endsection

        @section('page_js')
            <script src="{{ config('app.source_url') }}mctsource/js/liteapp_bd3sozui.js" type="text/javascript" charset="utf-8"></script>
            <script type="text/javascript">
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
            //update 梅杰 20180710 为每个小程序增加小程序码
                //显示小程序码弹窗
                $(".code").click(function(e){
                    getCode($(this).attr('id'));
                    $(".xcx-mask").removeClass('hide');
                });

                $(".xcx-mask").click(function(e){
                    var _con = $('.xcx-wrap');   // 设置目标区域
                    if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
                        $(".xcx-mask").addClass('hide');
                    }
                });

                $(".xcx-wrap-close").click(function(e){
                    $(".xcx-mask").addClass('hide');
                });

                //获取小程序码
                function getCode(xcxConfigId){
                    var imgSrc = $("#img_xcxm").attr("src")
                    if(imgSrc){
                        $("#img_xcxm").attr("src",'')
                    }
                    var href = $("#path_xcxm").parent('dd').attr("data-url")
                    $("#path_xcxm").attr("data-url",href)
                    hstool.load();
                    $.ajax({
                        url:'/merchants/xcx/code',
                        type:'post',
                        data:{'xcxConfigId': xcxConfigId},
                        dataType:'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function (res) {
                            if(res.errCode==0){
                                $("#img_xcxm").attr("src",'data:image/png;base64,'+res.data);
                                $("#down_xcxm").attr("href",$("#img_xcxm").attr("src")).attr("download","xcxm.png");
                            }else{
                                tipshow(res.errMsg,'warn');
                            }
                            hstool.closeLoad();
                        },
                    });
                }

            </script>
@endsection