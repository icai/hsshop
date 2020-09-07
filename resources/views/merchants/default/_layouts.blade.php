<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>微商城系统 - {{ $title or '' }}</title>
	<link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    
    <link href="{{ config('app.source_url') }}static/css/bootstrap.min.css" rel="stylesheet">
    <!-- 搜索美化插件 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/chosen.css">
    <!-- 核心base.css文件（每个页面引入） -->
    
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/help_common.css" />
    @yield('head_css')
</head>
<body {{ $bodyClass or '' }}>
    <!-- 左边 开始 -->
    <div class="left">
        <!-- 一级导航 开始 -->
        <div class="first_items">
            <!-- logo 开始 -->
            <a class="logo_items" href="javascript:void(0);" >
                @if ( session('logo') )
                    <img src="{{ imgUrl(session('logo')) }}" width="40" height="40" />
                @else
                    <img src="{{ config('app.source_url') }}home/image/huisouyun_120.png" width="40" height="40" >
                @endif
            </a>
            <!-- logo 结束 -->
            <!-- 一级导航列表 开始 -->
            <ul class="first_nav" >
                @if ( $leftNav == 'index' )
                <li class="hover">
                    <a href="{{ URL('/merchants/index') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">首页</div>                    		
                    	</div>
                    </a>
                @else
                <li>
                	<a href="{{ URL('/merchants/index') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_leve"></div>
	                        <div class="slider_span">首页</div>                    		
                    	</div>
                    </a>
                @endif
                </li>
                @if ( $leftNav == 'store' )
                    <li class="hover">
                    	<a href="{{ URL('/merchants/store/home') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">店铺</div>                    		
                    	</div>
                    	</a>
                @else
                    <li>
                    	<a href="{{ URL('/merchants/store/home') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_leve"></div>
	                        <div class="slider_span">店铺</div>                    		
                    	</div>
                    	</a>
                @endif
                    
                </li>
                @if ( $leftNav == 'product' )
                    <li class="hover">
                    	<a href="{{ URL('/merchants/product/index/1') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">商品</div>                    		
                    	</div>
                    	</a>
                @else
                    <li>
                    	<a href="{{ URL('/merchants/product/index/1') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_leve"></div>
	                        <div class="slider_span">商品</div>                    		
                    	</div>
                    	</a>
                @endif
                   
                </li>
                @if ( $leftNav == 'order' )
                    <li class="hover">
                    	<a href="{{ URL('/merchants/order') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">订单</div>                    		
                    	</div>
                    	</a>
                @else
                    <li>
                    	<a href="{{ URL('/merchants/order') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_leve"></div>
	                        <div class="slider_span">订单</div>                    		
                    	</div>
                    	</a>
                @endif
                    
                </li>
                
                </li>
                @if ( $leftNav == 'capital' )
                    <li class="hover">
                    	<a href="{{ URL('/merchants/capital') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">资产</div>                    		
                    	</div>
                    	</a>
                @else
                    <li>
                    	<a href="{{ URL('/merchants/capital') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_leve"></div>
	                        <div class="slider_span">资产</div>                    		
                    	</div>
                    	</a>
                @endif
                    
                </li>  
                @if ( $leftNav == 'member' or $leftNav =='indexPoint' )
                    <li class="hover">
                    	<a href="{{ URL('/merchants/member') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">客户</div>                    		
                    	</div>
                    	</a>
                @else
                    <li class="">
                    	<a href="{{ URL('/merchants/member') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_leve"></div>
	                        <div class="slider_span">客户</div>                    		
                    	</div>
                    	</a>
                @endif
                   
                </li>

                @if ( $leftNav == 'statistics' )
                    <li class="hover mb15">
                        <a href="{{ URL('/merchants/statistics/shops/index') }}">
                            
                            <div class="slider_box">
                                <div class="slider_img slider_active"></div>
                                <div class="slider_span">数据</div>                           
                            </div>
                        </a>
                @else
                    <li class="mb15">
                        <a href="{{ URL('/merchants/statistics/shops/index') }}">
                            
                            <div class="slider_box">
                                <div class="slider_img slider_leve"></div>
                                <div class="slider_span">数据</div>                           
                            </div>
                        </a>
                @endif
                    
                   
                </li>
                @if ( $leftNav == 'marketing' )
                    <li class="hover">                    	
                @else
                    <li>
                @endif
                    <a href="{{ URL('/merchants/marketing') }}">
                        
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">营销</div>                    		
                    	</div>
                    </a>
                </li>
                @if ( $leftNav == 'minapp' )
                    <li class="hover">
                        <a href="{{ URL('/merchants/marketing/xcx/list') }}">
                        <div class="slider_box">
                            <div class="slider_img slider_active"></div>
                            <div class="slider_span">小程序</div>                          
                        </div>
                        </a>
                @else
                    <li>
                        <a href="{{ URL('/merchants/marketing/xcx/list') }}">
                        <div class="slider_box">
                            <div class="slider_img slider_leve"></div>
                            <div class="slider_span">小程序</div>                          
                        </div>
                        </a>
                @endif
                @if ( $leftNav == 'wechat' )
                    <li class="hover">
                    	<a href="{{ URL('/merchants/wechat') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">公众号</div>                    		
                    	</div>
                    	</a>
                @else
                    <li>
                    	<a href="{{ URL('/merchants/wechat') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_leve"></div>
	                        <div class="slider_span">公众号</div>                    		
                    	</div>
                    	</a>
                @endif
                    
                </li>
                @if ( $leftNav == 'currency' )
                    <li class="hover mb15">
                    	<a href="{{ URL('/merchants/currency/index') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">设置</div>                    		
                    	</div>
                    	</a>
                @else
                    <li class="mb15">
                    	<a href="{{ URL('/merchants/currency/index') }}">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_leve"></div>
	                        <div class="slider_span">设置</div>                    		
                    	</div>
                    	</a>
                @endif
                    
                </li>
                 @if ( $leftNav == 'commend')
                      <li class="hover mb15">
                      <a href="{{ URL('/merchants/commend/list') }}">
                      <div class="slider_box">
                      <div class="slider_img slider_active"></div>
                       <div class="slider_span">运营</div>
                       </div>
                       </a>
                 @else
                      <li class="mb15">
                      <a href="{{ URL('/merchants/commend/list') }}">
                      <div class="slider_box">
                          <div class="slider_img slider_leve"></div>
                       <div class="slider_span">运营</div>
                      </div>
                      </a>
                 @endif
                  </li>
                   <li>
                    <a href="/">
                    	<div class="slider_box">
	                    	<div class="slider_img slider_active"></div>
	                        <div class="slider_span">会搜云</div>                    		
                    	</div>
                    </a>
                </li>
            </ul>
            <!-- 一级导航级导航列表 结束 -->
        </div>
        <!-- 一级导航 结束 -->
        @yield('slidebar')
        <!-- 个人信息 开始 -->
        <div class="user_info">
            <span class="user_name">{{ session('userInfo')['name'] }}</span>
            <span class="user_arrow"></span>
            <div class="info_items">
                <div class="info_header">
                    <div>{{ session('userInfo')['name'] }}</div>
                    <div>{{ session('userInfo')['mphone'] }}</div>
                </div>
                <div class="info_set">
                    
                    <a href="{{ URL('/auth/set') }}">帐号设置</a>
                    <a href="{{ URL('/merchants/team') }}">切换店铺</a>
                    <a href="{{ URL('/auth/loginout') }}">退出</a>
                </div>
            </div>
        </div>
        <!-- 个人信息 结束 -->
    </div>
    <!-- 左边 结束 -->
    <!-- 中间 开始 -->
    <div class="middle">
        @yield('middle_header')
        <!-- 主体 开始 -->
        <div class="main">
            @yield('content')
            <!--底部开始-->
            <div id="app-footer" class="footer">
                <p><a class="logo" href="{{ URL('/') }}" target="_blank"></a></p>
            </div>
            <!--底部结束-->
        </div>
        <!-- 主体 结束 -->
    </div>
    <!-- 中间 结束 -->
    
    <!-- 右侧 开始 -->
    <div class="right hide_help" style="overflow:initial;height: 100%;">
        <!-- 帮助和服务顶部 开始 -->
        <div class="right_header">
            
            <img src="{{ config('app.source_url') }}mctsource/static/images/help.png"/>
            <span>帮助和服务</span>
            <i id="help-container-close" class="close_btn">x</i>
        </div>
        <!-- 帮助和服务顶部 结束 -->
        <!-- 帮助和服务主体内容 开始 -->
        <div class="right_body">
            <!-- 第一条咨询 -->
            <div class="help-center">
                <p class="help-center-title t-pr">
                    帮助中心
                    <a href="/home/index/helps" class="detail-right" target="_blank">进入></a>
                </p>
                <div class="t-ml15 mt28">
                    @if(!empty($_information[Route::current()->getUri()]))
                        @forelse($_information[Route::current()->getUri()] as $key=>$val)
                            @if($key==0)
                                <p>
                                    <p class="details_title">{{$val['title']}}</p>
                                	<span class="details_con">{{$val['content']}}</span>
                                    <a class="details_right" href="/home/index/detail/{{$val['id']}}" target="_blank">详情</a>
                                </p>

                                <hr style="margin-bottom: 30px;"/>
                                @else
                                <p class="t-mb5"><a href="/home/index/detail/{{$val['id']}}" target="_blank">{{$val['title']}}</a></p>
                                @endif

                            <!-- 其他资讯 -->


                            @endforeach
                            @if(count($_information[Route::current()->getUri()])>1)
                                    <hr  style="margin-bottom: 30px;"/>
                             @endif
                            @else
                                    <hr  style="margin-bottom: 30px;"/>

                            @endif

                </div>
                @if(isset($__isOpen__)&&$__isOpen__)
                <div class="adNav">
                    <a href="javascript:void(0);" class="slide_images"><img src="{{ config('app.source_url') }}static/images/ad02.jpg" alt=""/></a>
                </div>
                @endif
                <!-- 鼠标移动到上面,显示左边的内容 -->
                <p class="help-center-title t-pr pt20">
                    会搜云服务经理
                </p>
                <div class="help-server t-pr">
                    <div class="t-fl">
                        <img src="{{ config('app.source_url') }}static/images/customer_service.jpg" width="50" height="50" class="head-img" />
                    	<a href="tencent://message/?uin=1658349770&Site=&Menu=yes">QQ在线咨询</a>
                    </div>
                    <dl class="t-fl t-ml10 t-mt5">
                        <dt><h5 class="qq_h5"></h5></dt>
                        <dd class="t-mt10 qq_dd">有问题来问我哦</dd>
                    </dl> 
                    <div class="clear"></div>
                    <!-- 浮动框 -->
                    <div class="help-zent-popover">
                        <div class="help-zent-popover-content"> 
                            <div class="t-mt10"> 
                                <p style="color:#333;font-size:15px;"></p>
                                <p class="t-mt10">电话咨询：</p>
                                <p><!--{{isset($CusSerInfo['phone'])&&$CusSerInfo['phone']}}-->0571-87796692</p>
                                <p style="margin-top: 5px;">QQ:</p>
                            	<p>1658349770</p>
                            </div>
                            <!--<hr>-->
                            {{--<a class="panama-entrance"  href="javascript:;">在线留言</a>--}}
                        </div> 
                    </div>

                </div>
            </div> 
            
        </div>
        <!-- 帮助和服务主体内容 结束 -->
    </div>
    <!-- 右侧 结束 -->
    <!-- 消息和通知 开始 -->
    
    <div id="widget-notice-center">
        <div class="notice-center">
            <div class="notice-nav">
                @if ( config('app.chat_url') )
                    <a class="kefu_news" target="_blank" href="{{config('app.chat_url')}}/#/transfer?shopId={{session('wid')}}&custId={{session('userInfo')['id']}}&sign={{md5(session('wid').session('userInfo')['id'].'huisou')}}&custJoinWay=PC">
                        <img src="{{ config('app.source_url') }}mctsource/images/kefu_news.png">
                        <span>客户消息</span>
                        <span class="news-badge hide"></span>
                    </a> 
                @endif
                <a class="btn-msg-tx" href="javascript:void(0);">
                    <span>通知</span>
                </a>                
            </div>
            <div class="noticePanel hide">
                <div class="noticePanel__title"> 
                    通知中心 
                    <span class="span-msg-tip">(0未读)</span>
                    <span class="pull-right icon--close">×</span>
                </div>
                <!-- 有消息 -->
                <div class="noticeList">
                    
                </div>
                <!-- 没消息 -->
                <div class="emptyPanel hide">
                    <div class="icon icon--empty"></div>
                    <p class="panel__tips">暂时没有新通知哦~</p>
                    <a href="{{ URL('/merchants/notification/notificationListView') }}" class="zent-btn zent-btn-primary-outline">查看历史消息</a>
                </div>
                <div class="noticePanel__footer">
                    <a class="pull-left mark_read">全部标为已读</a>
                   
                </div>
            </div>
        </div>
    </div>
    <!-- 消息和通知 结束 -->
    <div class="modal-backdrop false in" id="slide_ad_bg" style="display: none;"></div>
    <div class="model_bg" style="display:none" id="slide_ad_model">
        <div class="close_mode_bg"></div>
        <img src="{{ config('app.source_url') }}mctsource/images/model_bg2.png">
    </div>
    <!-- 弱密码修改弹窗 -->
    @if (session('userInfo')['isWeakPasswd'] ?? false)
    <div class="passwrod-dialog-wrap">
        <div class="passwrod-dialog">
            <div class="password-dialog-title">
                提示
            </div>
            <div class="password-dialog-content">
                <div class="content-title">
                    您的登录密码安全性较弱，需要改为强密码后方可登录
                </div>
                <div class="content-info">
                    会搜云系统网络安全机制升级，为了您的账户安全，请将密码修改为英文字符+数字的组合形式。
                </div>
            </div>
            <div class="password-dialog-footer">
                <button class="password-btn">去修改</button>
            </div>
        </div>
    </div>
    @endif
    @yield('other')
    <script>    
    var SOURCE_URL = "{{ config('app.source_url') }}";
    var MSG_PORT = "{{ config('app.websocket_port') }}";
    var MSG_WID ="{{ session('wid') }}";
    var MSG_HOST = "{{ config('app.url') }}";  
    var MSG_URL = "{{ URL('/merchants/notification/notificationListView') }}";
    var HOST = "{{config('app.url')}}";
    var KEFU_URL = "{{config('app.chat_url')}}";
    var WID = "{{session('wid')}}";
    var CUST_ID = "{{session('userInfo')['id']}}";
    // 客服签名
    var KEFU_SIGN = "{{md5(session('wid').session('userInfo')['id'].'huisou')}}"; 
    </script>
    @if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
    @endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
    <!-- 核心 base.js JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
    <!-- 消息模块JS -->
    @if(config('app.env') != 'local')
    <script src="{{ config('app.source_url') }}mctsource/static/js/msg_common.js"></script>
    @endif
    <script src="{{ config('app.source_url') }}mctsource/static/js/socket.io.js"></script>
    <!-- 消息提醒 -->
    <script type="text/javascript">
        if (window.location.host == 'www.huisou.cn') {
            // 线上
            var socketUrl = "wss://hsim.huisou.cn:9082";
        } else {
            // 测试环境
            var socketUrl = "wss://kf.huisou.cn:9082";
        }
        var socket = io.connect(socketUrl);
        /**
         * @author: 魏冬冬（zbf5279@dingtalk.com）
         * @description: 未读消息socket链接
         * @param {String} res 成功回调参数 
         * @return: {void}
         * @Date: 2019-10-09 09:42:23
         */
        socket.on("custContSuc", res => {
            if (res === 'custContSuc') {
                socket.emit('custListenJoin', {shopId:WID,custId:CUST_ID,custJoinWay: "PC",sign:KEFU_SIGN});
            }
        })
        /**
         * @author: 魏冬冬（zbf5279@dingtalk.com）
         * @description: 监听未读消息事件
         * @param {String} res 消息数量
         * @return: 
         * @Date: 2019-10-09 09:44:23
         */
        socket.on('wscCustUnReadMsgNum',(res) => {
            if ( res > 0) {
                $('.notice-nav .news-badge').html(res).removeClass('hide');
                $('.notice-nav .kefu_news').addClass('active');
                $('.notice-nav .kefu_news img').attr('src',SOURCE_URL+'mctsource/images/kefu_news_active.png');
            } else {
                $('.notice-nav .news-badge').html('').addClass('hide');
                $('.notice-nav .kefu_news').removeClass('active');
                $('.notice-nav .kefu_news img').attr('src',SOURCE_URL+'mctsource/images/kefu_news.png');
            }
        })
        // function getNewCount(){
        //     if(KEFU_URL){
        //         $.get(KEFU_URL + '/list/message/unReadMScount',{shopId:WID,custId:CUST_ID},function(data){
        //             data = JSON.parse(data);
        //             if(data.code == 100){
        //                 if(data.data > 0){
        //                     $('.notice-nav .news-badge').html(data.data).removeClass('hide');
        //                     $('.notice-nav .kefu_news').addClass('active');
        //                     $('.notice-nav .kefu_news img').attr('src',SOURCE_URL+'mctsource/images/kefu_news_active.png');
        //                 }else{
        //                     $('.notice-nav .news-badge').addClass('hide');
        //                     $('.notice-nav .kefu_news').removeClass('active');
        //                     $('.notice-nav .kefu_news img').attr('src',SOURCE_URL+'mctsource/images/kefu_news.png');
        //                 }
        //             }
        //         })
        //     }
        //     setTimeout(getNewCount,10000);
        // }
        // getNewCount();
    </script>
    <script>
        // 弱密码弹窗
        $(function() {
            $('.password-dialog-footer .password-btn').click(function () {
                window.location.href="/auth/changepsd"
            })
        })
    </script>
    @yield('page_js')
</body>
</html>
