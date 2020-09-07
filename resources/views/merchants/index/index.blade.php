@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/index_ws21vga9.css" />
<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
@endsection
@section('content')
<div class="content">
    <!--顶部-->
    @if(isset($is_overdue) && $is_overdue == 1)
    <div class="header">
        {{--<p class="p1">续费提示：您的店铺({{ $weixinInfo['shop_name'] }})已打烊，为不影响正常运营，请及时<a href="{{ URL('/merchants/capital/fee/serviceList') }}" class="red">续费订购</a>。<span class="header-tip">×</span></p>--}}
        <p class="p1">续费提示：您的店铺({{ $weixinInfo['shop_name'] }})已打烊，为不影响正常运营，请及时联系客服进行续费订购!<span class="header-tip">×</span></p>
    </div>
    @elseif(isset($soonOverdue) && $soonOverdue == 1)
    <div class="header">
        {{--<p class="p1">续费提示：您的店铺({{ $weixinInfo['shop_name'] }})距打烊<span class="red">仅剩{{ $days }}天</span>，为不影响正常运营，请及时<a href="{{ URL('/merchants/capital/fee/serviceList') }}" class="red">续费订购</a>。<span class="header-tip">×</span></p>--}}
        <p class="p1">续费提示：您的店铺({{ $weixinInfo['shop_name'] }})距打烊<span class="red">仅剩{{ $days }}天</span>，为不影响正常运营，请及时联系客服进行续费订购!<span class="header-tip">×</span></p>
    </div>
    @endif
    <div class="center-body">
        <!--店铺标题-->
        <!--右侧部分-->
        <div class="body-right">
            <div class="kefu">
                <a href="tencent://message/?uin=1658349770&Menu=yes" class="kefu_top">
                    <span class="ico-contact-waiter waiter" class=""></span>
                    <span class="kefu_contact">联系客服</span>
                </a>
             </div>
             <div class="kefu_phone">
             	<span>电话客服：0571-87796692</span>
             </div>
             <div class="download-ercode J_download-ercode">
                <div class="ercode-tips"></div>
                <div class="er-popup">
                    <div class="pop-inner clearfix">
                        <div class="left-tips">
                            <p>扫码下载</p>
                            <p> 会搜云商家版APP</p>
                        </div>
                        <div class="right-ercode">
                            <img src="{{ imgUrl() }}{{ $qrcodeUrl }}" class="ercode-img">
                        </div>
                    </div>
                    <div class="arrow"></div>
                </div>
             </div>

            @if(!empty($_information[Route::current()->getUri()]))
                @if($__isOpen__)
                <div class="especially">
                        <a href="javascript:void(0);">
                    <img src="{{ config('app.source_url') }}static/images/ad02.jpg" alt=""/>
                    </a>

                </div>
            @endif
                @endif
             <!--特别资讯-->
            
            <!---->
            <div class="iphone-sec hidden">
                <span class="iphone-bgm"></span>
                <span class="iphone-content">
                    <div class="iphone-top">商家版app</div>
                    <div class="iphone-bottom">开启移动商务新时代</div>
                </span>
            </div>
            @if($inforData)
            @foreach($inforData as $val)
            <h4 class="right-title1 product-dynamic right_tle"><span>{{ $val['name'] }}</span><a href="/home/index/news?Pid={{ $val['id'] }}" class="more main-a" target="_blank">更多</a></h4>
            @if(isset($val['newList']) && $val['newList'])
            <ul class="a-list">
                @foreach($val['newList'] as $new)
                    <li><a href="/home/index/newsDetail/{{ $new['id'] }}/news" target="_blank">{{ $new['title'] }}</a></li>
                @endforeach
            </ul>
            @endif
            @endforeach
            @endif
        </div>
        <!--左侧部分-->
        
        <h2 class="center-title">
                <span>{{ $weixinInfo['shop_name'] }}
                <!-- 续费 -->
                @if((isset($is_overdue) && $is_overdue == 1)||(isset($soonOverdue) && $soonOverdue == 1))
                    <div class="renew-wrap">
                        <div class="head-image clearfix">
                            <div class="pull-left img">
                            @if ( session('logo') )
                                <img src="{{ imgUrl(session('logo')) }}" />
                            @else
                                <img src="{{ config('app.source_url') }}home/image/huisouyun_120.png" / >
                            @endif
                            </div>
                            <div class="pull-left head">
                                <p>{{$versionName}}</p>
                                @if($beginTime!=""&&$endTime!="")
                                <p>有效期：{{$beginTime}} -{{$endTime}}</p>
                                @endif
                            </div>
                        </div>
                        <div id="renew-flag">
                            {{--<a href="/merchants/capital/fee/serviceList">续费</a>--}}
                        </div>
                    </div>
                @endif
                </span>
            <!-- 续费 end -->
    	</h2>
        <div class="body-left">
            @if($__isOpen__)
            <div class="index_image">
                <a href="javascript:void(0);"><img src="{{ config('app.source_url') }}static/images/ad03.jpg" alt=""/></a>
            </div>
            @endif
           <!--  @if(isset($affiche['content']) && $affiche['content'])
           <div style="width: calc(100% - 20px); padding: 10px; background: #fff7cc; margin-bottom: 15px; border-radius: 5px">
               {!! $affiche['content'] !!}
           </div>
           @endif -->
            @if($is_notice == 1) 
            <div style="width: calc(100% - 20px); padding: 10px; background: #fff7cc; margin-bottom: 15px; border-radius: 5px">
                <div data-v-0b6696d6="" class="rich-text" style="z-index: 999;">
                    <div id="rich_0" style="display: flex; flex-direction: column; padding: 0px 10px;">
                        <p style="white-space: normal;">【重要通知】关于系统服务器迁移的通知</p>
                        <p style="white-space: normal;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                        <p style="white-space: normal;">尊敬的用户：</p>
                        <p style="white-space: normal;">您好！</p>
                        <p style="white-space: normal;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                        <p style="white-space: normal;">为了配合杭州亚运会的工作，我司需要对系统服务器做迁移处理，针对此次迁移的安排如下：</p>
                        <p style="white-space: normal;">1、迁移时间：2019年08月24日 凌晨00:00 - 07:00；</p>
                        <p style="white-space: normal;">2、迁移期间，系统全部功能将无法使用；</p>
                        <p style="white-space: normal;">给您带来不便，我们深表歉意。</p>
                        <p style="white-space: normal;">感谢您对会搜科技股份一直以来的支持与信任！</p>
                        <p style="white-space: normal;"><br></p>
                        <div style='display: flex;display:-webkit-flex;flex-direction: column;align-items: flex-end;width: 565px;'>
                            <p style="white-space: normal;">杭州会搜科技股份有限公司</p>
                            <p style="white-space: normal;">2019年08月01日</p>
                            <p><br></p>
                        </div>
                    </div>
                </div>
            </div>
            @else
                @if(isset($affiche['content']) && $affiche['content'])
                <div style="width: calc(100% - 20px); padding: 10px; background: #fff7cc; margin-bottom: 15px; border-radius: 5px">
                   {!! $affiche['content'] !!}
                </div>
                @endif
            @endif
            
            @if(in_array('view_order_price',session('permission')??[]))
            <div class="transaction-details">
                @if(isset($is_overdue) && $is_overdue == 1)
                    <div class="details-num">
                        <a href="##" class="overdue_item"><h5 style="color: #FF4343;">{{ $orderInfo['status'][1]['count'] or '0' }}</h5></a><br>
                        <h6>待发货订单</h6>
                    </div>
                    <div class="details-num">
                        <a href="##" class="overdue_item"><h5 class="main-a">{{ $orderInfo['refundStatusCount'] or '0' }}</h5></a><br>
                        <h6>维权订单</h6>
                    </div>
                    
                    <div class="details-num">
                        <a href="##" class="overdue_item"><h5 class="main-a">{{ $yestodayInfo['1']['countTotal'] or '0' }}</h5></a><br>
                        <h6>昨日订单</h6>
                    </div>
                    <div class="details-num">
                        <a href="##" class="overdue_item"><h5 class="main-a"><span>{{ $yestodayIcome['income'] or '0' }}</span></h5></a><br>
                        <h6>昨日交易额</h6>
                    </div>
                    <div class="details-num">
                        <a href="##" class="overdue_item"><h5 class="main-a"><span>{{ $orderTotalCount['income'] or '0'}}</span></h5></a><br>
                        <h6>总收入金额</h6>
                    </div>
                @else
                    <div class="details-num">
                        <a href="/merchants/order/orderList?status=2"><h5 style="color: #FF4343;">{{ $orderInfo['status'][1]['count'] or '0' }}</h5></a><br>
                        <h6>待发货订单</h6>
                    </div>
                    <div class="details-num">
                        <a href="/merchants/order/orderList/2" class="main-a"><h5 class="main-a">{{ $orderInfo['refundStatusCount'] or '0' }}</h5></a><br>
                        <h6>维权订单</h6>
                    </div>
                    
                    <div class="details-num">
                        @php
                        $lastDate = date('Y-m-d', strtotime('-1 day'));
                        @endphp
                        <a href="/merchants/order/orderList?start_time={{$lastDate}} 00:00:00&end_time={{$lastDate}} 23:59:59"><h5 class="main-a">{{ $yestodayInfo['1']['countTotal'] or '0' }}</h5></a><br>
                        <h6>昨日订单</h6>
                    </div>
                    <div class="details-num">
                        <a href="/merchants/order"><h5 class="main-a"><span>{{ $yestodayIcome['income'] or '0' }}</span></h5></a><br>
                        <h6>昨日交易额</h6>
                    </div>
                    <div class="details-num">
                        <a href="/merchants/capital"><h5 class="main-a"><span>{{ $orderTotalCount['income'] or '0'}}</span></h5></a><br>
                        <h6>总收入金额</h6>
                    </div>
                @endif
            </div>
            <div class="domain">
                <h3>常用功能</h3>
                @if(isset($is_overdue) && $is_overdue == 1)
                    <div class="flex">
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>
                            <span class="font">公众号</span>
                        </a>
                        <!-- <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">支付宝小程序</span>
                        </a> -->
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">页面管理</span>
                        </a>
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">订单概况</span>
                        </a>
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">优惠券</span>
                        </a>
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">通用设置</span>
                        </a>
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">店铺信息</span>
                        </a>
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">客户管理</span>
                        </a>
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">交易记录</span>
                        </a>
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">帮助中心</span>
                        </a>
                        <a href="##" class="item overdue_item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">分销佣金</span>
                        </a>
                    </div>
                @else
                    <div class="flex">
                        <a href="{{ url('/merchants/wechat') }}" class="item">
                        	<span class="overdue_item_img"></span>
                            <span class="font">公众号</span>
                        </a>
                        <!-- <a href="{{ url('/merchants/marketing/alixcx/list') }}" class="item">
                        	<span class="overdue_item_img"></span>
                            <span class="font">支付宝小程序</span>
                        </a> -->
                        <a href="{{ url('/merchants/currency/index') }}" class="item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">店铺信息</span>
                        </a>
                        <a href="{{ url('/merchants/distribute')  }}" class="item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">分销佣金</span>
                        </a>
                        <a href="{{ url('/merchants/member/customer') }}" class="item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">客户管理</span>
                        </a>
                        <a href="{{ url('/merchants/store/attachmentImage') }}" class="item">
                        	<span class="overdue_item_img"></span>
                            <span class="font">我的文件</span>
                        </a>
                        <a href="{{ url('/merchants/order') }}" class="item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">订单概况</span>
                        </a>
                        <a href="{{ url('/merchants/marketing/coupons') }}" class="item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">优惠券</span>
                        </a>
                        <a href="{{ url('/merchants/capital/transactionRecord') }}" class="item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">交易记录</span>
                        </a>
                        <a href="{{ url('/merchants/currency/index') }}" class="item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">通用设置</span>
                        </a>
                        
                        <a href="{{ url('/home/index/information')  }}" class="item">
                            <span class="overdue_item_img"></span>	
                            <span class="font">帮助中心</span>
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="marketing">
                <h3>营销应用<a href="/merchants/marketing">营销中心</a></h3>
                @if(isset($is_overdue) && $is_overdue == 1)
                    <div class="flex">
                        <a href="##" class="marketing-play white overdue_item">
                            <h4>多人拼团</h4>       
                        	<div>裂变式营销玩法</div>                              
                        </a> 
                        <a href="##"  class="marketing-play seckill white hot overdue_item">
                            <h4>享立减</h4>
                        	<div>裂变式营销玩法</div> 
                        </a>
                        <a href="##"  class="marketing-play mesg-push white overdue_item hot">
                            <h4>秒杀</h4>
                        	<div>快速抢购引导消费</div>
                        	<img src="{{ config('app.source_url') }}mctsource/images/hot.png" /> 
                        </a>
                        <a href="##"  class="marketing-play cashier white overdue_item">
                            <h4>会搜小程序</h4>   
                        	<div>一建生成抢占先机</div> 
                        </a>
                    </div>
                @else
                <div class="flex">
                    <a href="{{url('/merchants/marketing/togetherGroupList')}}" class="marketing-play white">
                        <h4>多人拼团</h4>       
                        <div>裂变式营销玩法</div>                  
                    </a>
                    <a href="{{url('/merchants/shareEvent/list')}}" class="marketing-play seckill white">
                        <h4>享立减</h4>
                        <div>拉新裂变促活精准营销</div> 
                    </a>
                    <a href="{{url('/merchants/marketing/seckills')}}" class="marketing-play mesg-push white hot">
                        <h4>秒杀</h4>
                        <div>快速抢购引导消费</div> 
                        <!--<img src="{{ config('app.source_url') }}mctsource/images/hot.png" />-->
                    </a>
                    <a href="{{url('/merchants/marketing/xcx/list')}}" class="marketing-play cashier white">
                        <h4>会搜小程序</h4>   
                        <div>一建生成抢占先机</div>                    
                    </a>
                </div>
                @endif
            </div>
            @endif
            <!--经营list  学习list-->
            <div class="dashboard clearfix">
                <div class="dashboard_left">
                    <div class="title">
                        <span>经营建议</span>
                        <a class="marketing-center main-a" href="/home/index/news" target="_blank">更多</a>
                    </div>
                    <div class="suggest">
                        @if(!empty($information['suggest']))
                            @forelse($information['suggest'] as $val)                            
                                <a class="swiper" href="/home/index/newsDetail/{{$val['id']}}/news" target="_blank">
                                    <p class="swiper_title">{{$val['title']}}</p>
                                    <p class="swiper_subtitle">{{$val['sub_title']}}</p>
                                </a>                            
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="dashboard_right">
                    <div class="title">
                        <span>学习答疑</span>
                        <a class="marketing-center" href="/home/index/news?Pid=168" target="_blank">更多</a>
                    </div>
                    
                    <div class="study_con">
                    	<div class="study_div study_div_lef">
                    		<a class="study_a" href="/home/index/about">
	                    		<div class="study_img"></div>
	                    		<div class="study_p">
	                    			<p class="study_title">关于我们</p>
	                    			<p class="study_subtitle">了解会搜云</p>
	                    		</div>
	                    	</a>
	                    	<a class="study_a" href="/home/index/helps">
	                    		<div class="study_img"></div>
	                    		<div class="study_p">
	                    			<p class="study_title">咨询中心</p>
	                    			<p class="study_subtitle">学习交流中心</p>
	                    		</div>
	                    	</a>
	                    	<a class="study_a" href="/home/index/helpList">
	                    		<div class="study_img"></div>
	                    		<div class="study_p">
	                    			<p class="study_title">帮助中心</p>
	                    			<p class="study_subtitle">专业解答为您服务</p>
	                    		</div>
	                    	</a>
                    	</div>
                    	<div class="study_div study_div_right">
                    		<a class="study_a" href="/">
	                    		<div class="study_img"></div>
	                    		<div class="study_p">
	                    			<p class="study_title">会搜云</p>
	                    			<p class="study_subtitle">使用会搜云改变零售</p>
	                    		</div>
	                    	</a>
	                    	<a class="study_a" href="/home/index/1/shop">
	                    		<div class="study_img"></div>
	                    		<div class="study_p">
	                    			<p class="study_title">案例展示</p>
	                    			<p class="study_subtitle">各项服务一览无余</p>
	                    		</div>
	                    	</a>
	                    	<a class="study_a" href="/home/index/productServiec">
	                    		<div class="study_img"></div>
	                    		<div class="study_p">
	                    			<p class="study_title">定制服务</p>
	                    			<p class="study_subtitle">定制开发专享功能</p>
	                    		</div>
	                    	</a>
                    	</div>
                	</div>
                </div>
            </div>
            <!-- 更多服务 -->
            <div class="much-server marketing">
                <h3>更多服务</h3>
                <a class="server clearfix" href="{{ config('app.url') }}home/index/customization" target="_blank">
                	
                	<div class="server_img server_img_a"></div>
                	<p>APP定制服务</p>
                </a>
                <a class="server clearfix" href="{{ config('app.url') }}home/index/distribution" target="_blank">
                	
                	<div class="server_img server_img_b"></div>
                	<p>分销3.0</p>
                </a>
                <a class="server clearfix" href="{{ config('app.url') }}home/index/applet" target="_blank">
                	
                	<div class="server_img server_img_c"></div>
                	<p>微信小程序</p>
                </a>                
                
            </div>
            <!-- 权限弹窗 -->

            <!-- 续费弹框 -->
            <div id="renew-pop" class="layer-wrap none">
                <div class="pop-content">
                    
                        <ul class="table-head clearfix table-border">
                            <li class="pull-left">功能板块</li>
                            <li>服务内容</li>
                        </ul>
                        <div class="table-body">
                            <ul class="clearfix table-border">
                                <li class="pull-left"><span class="position-abs-middle">店铺管理<span></li>
                                <li>店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、店铺概况、店铺设、</li>
                            </ul>
                        </div>   
                </div>
                <p><button class="renew-close">关闭</button></p>
            </div>
            <!-- 续费弹框 end -->
         @if($frameType == '1')
            <div class="modal-backdrop false in" style="display: block;"></div>
            <div class="widget-feature-template modal in" aria-hidden="false" style="display: block;">
                <div class="close_model">
                    <img src="{{ config('app.source_url') }}mctsource/images/model_close.png">
                </div>
                <div class="model_img">
                   <img src="{{ config('app.source_url') }}mctsource/images/min_wechat.png">
                   <div class="price1">￥19800</div>
                   <div class="price2">￥29800</div>
                </div>
                <div class="modal_footer">
                    <div class="model_btn">
                        <a href="javascript:void(0);" class="model_btn1">
                            免费升级
                        </a>
                        <a href="javascript:void(0);" class="model_btn2"><img src="{{ config('app.source_url') }}mctsource/images/model_phone.png">0571-87796692</a>
                    </div>
                    <div class="model_info">
                        <img src="{{ config('app.source_url') }}mctsource/images/model_tip.png">
                        <span>用户也可以从小程序版直接升级为小程序+微商城至尊版</span>
                    </div>
                </div>
            </div>
            @elseif($frameType == '2')
            <div class="modal-backdrop false in" style="display: block;"></div>
            <div class="model_bg">
                <div class="close_mode_bg"></div>
                <img src="{{ config('app.source_url') }}mctsource/images/model_bg2.png">
            </div>
            @endif
            <div class="modal-backdrop false in" id="modal_backdrop" style="display: none;"></div>
            <div class="model_bg" style="display:none">
                <div class="close_mode_bg"></div>
                <img src="{{ config('app.source_url') }}mctsource/images/model_bg2.png">
            </div>
        </div>
    </div>
</div>   

@endsection
@section('page_js')
<!-- 当前页面js -->
<script>
    var frameType = {{$frameType}};
    console.log(frameType);
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script src="{{ config('app.source_url') }}static/js/jquery.lazyload.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/index_ws21vga9.js"></script>
@endsection