@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_39ygjl7x.css" />
    <style>
        /*访问小程序*/
        .modal-dialog{width:400px;width:400px;}
        .modal-content{height: 400px;width:400px;}
        .xcx-mask{position: fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.7);z-index:19891015;}
        .xcx-wrap{position: absolute;width:400px;height:400px;background-color:#fff;border-radius: 5px;top:0;left:0;right:0;bottom:0;margin:auto;padding:30px; box-sizing: border-box;}
        .xcx-wrap dl{text-align: center;}
        .xcx-wrap dt,.xcx-wrap dd{margin:10px 0;}
        .xcx-wrap .xcx-xcximg{width:250px;height:auto;margin:auto;}
        .xcx-wrap-close{
            width: 18px;
            float: right;
        }
        .widget-promotion-tab li{
        	width: 100%;
        }
    </style>
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <div class="third_nav">
            <!-- 二级导航三级标题 开始 -->
            <div class="third_title"><span>营销中心 /</span> 刮刮卡</div>
            <!-- 二级导航三级标题 结束 -->
        </div>
    </div>
@endsection
@section('content')
    <div class="content">
        <ul class="screen_nav nav nav-tabs mgb15" role="tablist">
            <li role="presentation" class="active nav_li">活动列表 </li>
        <!-- <a class="a-rig" href="{{ config('app.url') }}home/index/detail/626/help" target="_blank"><span class="z-cir">?</span>查看如何玩转【幸运大转盘】</a>           -->
        </ul>
        <div class="model_itmes mgb20">
            <a href="/merchants/marketing/addScratch" class="btn btn-success">新建刮刮卡</a>
            <!-- 搜索 开始 -->
            <div class="search_wrap">
                <form action="" method="get" name="searchForm">
                    <label class="search_items">
                        <input class="search_input" type="text" name="title" value="" placeholder="搜索">
                    </label>
                </form>
            </div>
        </div>
        <!--无数据-->
        <!--<div class="no_result">还没有相关数据</div>-->
        <!-- 列表 开始 -->
        <div class="table table-hover condent_data">
            <!-- 标题 -->
            <ul class="active ul_color data_title flex_center">
                <li>活动名称</li>
                <li>参与限制</li>
                <li>有效期</li>
                <li>参与人/数</li>
                <li>领到/未领到人</li>
                <li>操作</li>
            </ul>
            <!-- 列表 -->
            @forelse($data[0]['data'] as $val)
                <ul class="data flex_center">
                    <li>{{$val['title']}}</li>
                    <li>@if($val['rule'] == 1)一人一次@elseif($val['rule'] == 2)一天一次@elseif($val['rule'] == 3)一天两次@endif</li>
                    <li>{{$val['start_time']}}至<br>{{$val['end_time']}}</li>
                    <li>{{$val['participate_user_num']}}/<a href="/merchants/marketing/scratchCount/{{$val['id']}}?status=0">{{$val['participate_total_num']}}</a></li>
                    <li><a href="/merchants/marketing/scratchCount/{{$val['id']}}?status=2">{{$val['receive_user_num']}}</a>/<a href="/merchants/marketing/scratchCount/{{$val['id']}}?status=1">{{$val['unreceive_user_num']}}</a></li>
                    <li class="opt_wrap blue_97f">
                        <a href="{{ URL('/merchants/marketing/addScratch?id='.$val['id']) }}">
                            <span class="blue_97f">编辑</span>
                        </a>-
                        <a class="pagecat-del card_close" data-id={{$val['id']}}>
                            <span class="blue_97f">删除</span>
                        </a>-
                        <a class="two-code btn-small-program" data-id={{$val['id']}}>
                            <span class="blue_97f">推广</span>
                        </a>
                    </li>
                </ul>
                @endforeach
        </div>
        <div style="text-align: right;">{{$data[1]}}</div>

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
        <!-- 推广弹窗 -->
	    <!--add by 韩瑜 2018-8-24-->
	    <div class="widget-promotion widget-promotion1" style="display: none;">
	        <ul class="widget-promotion-tab widget-promotion-tab1 clearfix">
	            <!--<li class="wsc_code active">微商城</li>-->
				<li class="xcx_code">小程序</li>
	        </ul>
	        <div class="widget-promotion-content js-tabs-content">
	        	<!--微商城-->
	            <!--<div class="js-tab-content-wsc" style="display: block;">
	                <div>
	                    <div class="widget-promotion-main">
	                        <div class="js-qrcode-content">
	                            <div class="widget-promotion-content">
		                            <label>商品页链接</label>
		                            <div class="input-append">
		                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="{{config('app.url')}}shop/index/{{session('wid')}}" />
		                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
		                            </div>
		                        </div>
		                        <div class="widget-promotion-content">
		                            <label class="label-b">商品页二维码</label>
		                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
	                                    <div class="qrcode">
	                                        <div class="qr_img"></div>
	                                        <div class="clearfix qrcode-links">
	                                            <a class="down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
	                                        </div>
	                                    </div>
	                               	</div>
		                        </div>
	                        </div>
	                    </div>
	                </div>
	            </div>-->
	            <!--小程序-->
	            <div class="js-tab-content-xcx" style="display: block;">
	                <div>
	                    <div class="widget-promotion-main">
	                        <div class="js-qrcode-content">
	                            <div class="widget-promotion-content">
		                            <label>小程序链接</label>
		                            <div class="input-append">
		                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="pages/scratchCard/scratchCard" />
		                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
		                            </div>
		                        </div>
		                        <div class="widget-promotion-content">
		                            <label class="label-b">小程序二维码</label>
		                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
	                                    <div class="qrcode">
	                                        <div class="qr_img_xcx"></div>
	                                        <div class="clearfix qrcode-links">
	                                            <a class="down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
	                                        </div>
	                                    </div>
	                               </div>
		                        </div>           	
	                        </div> 
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
    	<!--end-->
    </div>
@endsection
@section('page_js')
    <!-- 当前页面js -->
    <script type="text/javascript">
        var host ="{{ config('app.url') }}";
        var _host = "{{ config('app.source_url') }}";
        var wid = {{session('wid')}};
    </script>
    <script>
		/*
	    * @add by 韩瑜
	    * @desc 点击推广
	    * @date 2018-8-24
	    * 刮刮卡只有小程序，微商城推广码隐藏
	    * */
	   var id = ''
	    $('body').on('click','.two-code',function(e){
	        e.stopPropagation(); //阻止事件冒泡
	        var top = $(this).offset().top;
	        var left = $(this).offset().left;
	        id = $(this).attr('data-id')
	        $(".widget-promotion1").css({"top":top-130,"left":left-590});
	        $(".widget-promotion1").show();
	        //获取小程序二维码
		    $.ajax({
		        url:"/merchants/marketing/scratchQrCodeXcx/" + id,
		        type:"get",
		        dataType:"json",
		        success:function(res){
		        	console.log(res)
		        	if(res.status == 1){
		        		if(res.data.errCode==0 && res.data.data){
		              		var xcximg = '<img src="data:image/png;base64,'+res.data.data+'" />'
		              		$(".qrcode-right-sidebar .qr_img_xcx").html(xcximg);
			            }
		        	}
		            
		        }
		    });
		    $('.link_url_xcx').val('pages/scratchCard/scratchCard?scratchId='+id)
	    });
	    // 复制小程序链接
	    $('body').on('click','.js-btn-copy-xcx',function(e){
	        e.stopPropagation(); //阻止事件冒泡
	        var obj = $(this).siblings('.link_url_xcx');
	        copyToClipboard( obj );
	        tipshow('复制成功','info'); 
	    });
	
	    //下载小程序二维码
	    $('.down_qrcode_xcx').click(function(){
	    	window.location.href= '/merchants/marketing/scratchQrCodeXcxDownload/' + id;
	    });
	    //点击空白处隐藏弹出层
	    $('body').click(function(event){
	        var _con = $('.widget-promotion');   // 设置目标区域
	        if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
	            $(".widget-promotion").hide();
	        }
	    });
	    //end

        //删除刮刮乐活动
        var clone_flag = true
        $(".card_close").click(function (e) {
            var cardId = $(this).attr('data-id');
            if(clone_flag){
                clone_flag = false
                $.ajax({
                    url:'/merchants/marketing/delScratch',
                    type:'get',
                    dataType:'json',
                    data:{
                        "id":cardId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function (res) {
                        if(res.status == 1){
                            tipshow("删除成功!");
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }else{
                            tipshow(res.info,'warn');
                        }

                    }
                })
            }
        })
    </script>
@endsection