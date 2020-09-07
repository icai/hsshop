@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/header.css"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/evaluateDetail.css">
    <style>
        .like{background: url({{ config('app.source_url') }}shop/images/like_icon.png) 0 0 no-repeat; background-size: contain;}
        .unlike{background: url({{ config('app.source_url') }}shop/images/unlike_icon.png) 0 0 no-repeat; background-size: contain;}
    </style>
@endsection
@section('main')
    <input id="wid" type="hidden" value="{{session('wid')}}">
    <input id="eid" type="hidden" value="{{$evaluate['id']}}">
    <input id="share" type="hidden" value="{{request('share')}}">
    <input id="source" type="hidden" value="{{ config('app.source_url') }}">
    <div class="e_container">
        <div class='padddingBox'>
            <div class="main_info info">
                <div class="head">
                    <img src="{{$evaluate['member']['headimgurl']}}" alt="">
                </div>
                <div class="mation">
                    <p>{{$evaluate['member']['nickname']}}</p>
                    <p class="time">{{$evaluate['created_at']}}</p>
                </div>
                <div class="praise">
                    <div style="cursor: pointer;" class="prizeImg @if($evaluate['praise'] == 1) like @else unlike @endif "></div>
                    <span style="vertical-align: middle; font-size: 18px;font-weight: 500;">{{$evaluate['agree_num']}}</span>
                </div>
            </div>
        </div>

        <!--图文内容-->
        <div class='padddingBox'>
            <div class="evaluate-content">
                <div>{{$evaluate['content']}}</div>
                @forelse($evaluate['img'] as $val)
                    <div>
                        <img src="{{ imgUrl() }}{{$val['path']}}" alt="">
                    </div>
                    @endforeach
            </div>
        </div>

        <!--2.1-->
        <div class='padddingBox'>
            <div class="item">
                <!--商品信息-->
                <div class="ware">
                    @if($evaluate['orderDetail']['spec'])
                        <div class='specification'>
                            <span>{{$evaluate['orderDetail']['spec']}}</span>
                        </div>
                    @endif
                    <div class="detail">
                        <img src="{{ imgUrl() }}{{$evaluate['orderDetail']['img']}}" width="50" height="50" alt="">
                        <div class="content">
                            <div class="name">{{$evaluate['orderDetail']['title']}}</div>
                            <div class='num'>x{{$evaluate['orderDetail']['num']}}</div>
                            <div class="price">¥ {{$evaluate['orderDetail']['price']}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 其他人的评论 -->
        <div class='padddingBox commentBox'>
            <div class="comment">
                <div class="title">全部回复</div>
                <div class="list">
                    @foreach($evaluateDetailData['data'] as $val)
                        <div class="info">
                            <div class="head">
                                <img src="{{$val['member']['headimgurl']}}" alt="">
                            </div>
                            <div class="mation">
                                <p>@if($val['mid'] == 0) 商家回复: @else{{$val['member']['nickname']}}@endif</p>
                                <p class="time">{{$val['created_at']}}
                                    <span data-placement="{{$val['member']['id']}}" class="replay"><img src="{{ config('app.source_url') }}shop/images/xx.png" alt="" class="xx-img"></span>
                                </p>
                                @if(!empty($val['reply']))
                                    <p class="info-comment">回复:{{$val['reply']['nickname']}}：{{$val['content']}}</p>
                                @else
                                    <p class="info-comment">{{$val['content']}}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    

    <!-- 评论按钮 -->
    <div class="button">
        <span class="btn publish">发表评论</span>
        <span class="btn share">分享</span>
    </div>

    <div class="modal">
        <div class='cover'></div>
        <div class="box">
            <div class="option">
                <input name="comment" id="comment" />
                <span class="send c-blue">发送</span>
            </div>
            <input id="reply" type="hidden" name="reply" value="" />
        </div>
    </div>
    <!--分享的蒙板-->
    <div id="js-share-guide" class="js-fullguide fullscreen-guide @if(request('share') != 1) hide @endif" style="font-size: 16px; line-height: 35px; color: #fff; text-align: center;">
        <span class="js-close-guide guide-close">×</span>
        <span class="guide-arrow"></span>
        <div class="guide-inner">请点击右上角<br>通过【发送给朋友】功能<br>或【分享到朋友圈】功能<br>把消息告诉小伙伴哟～</div>
    </div>
    @include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var data = {!! json_encode($evaluateDetailData) !!}
        console.log(data);
    </script>
    <script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <!--  <script src="./public/static/js/jquery-weui.min.js"></script> -->
    <script type="text/javascript">
        
        // 微信分享
        $(function(){
            var url = location.href.split('#').toString();
                url = funcUrlDel('share');
            $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
                if(data.errCode == 0){
                    wx.config({
                        debug: false, 
                        appId: data.data.appId, 
                        timestamp: data.data.timestamp, 
                        nonceStr: data.data.nonceStr, 
                        signature: data.data.signature,
                        jsApiList: [
                            'checkJsApi',
                            'onMenuShareTimeline',
                            'onMenuShareAppMessage',
                            'onMenuShareQQ',
                            'chooseWXPay'
                        ] 
                    });
                    
                }
            })
            if(window.location.search){
                url += '&_pid_='+ '{{ session("mid") }}';
            }else{
                url += '?_pid_='+ '{{ session("mid") }}';
            }
            wx.ready(function () {

                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title: '{{ $shareData["share_title"] }}', 
                    desc: '{{ $shareData["share_desc"] }}', 
                    link: url, 
                    imgUrl: '{{ $shareData["share_img"] }}', 
                    success: function () {
                        
                    },
                    cancel: function () {
                        
                    }
                });

                //分享给朋友
                wx.onMenuShareAppMessage({
                    title: '{{ $shareData["share_title"] }}', 
                    desc: '{{ $shareData["share_desc"] }}', 
                    link: url, 
                    imgUrl: '{{ $shareData["share_img"] }}', 
                    type: '', 
                    dataUrl: '', 
                    success: function () {
                        
                    },
                    cancel: function () {
                        
                    }
                });

                //分享到QQ
                wx.onMenuShareQQ({
                    title: '{{ $shareData["share_title"] }}', 
                    desc: '{{ $shareData["share_desc"] }}', 
                    link: url, 
                    imgUrl: '{{ $shareData["share_img"] }}', 
                    success: function () {
                    },
                    cancel: function () {
                    }
                });

                //分享到腾讯微博
                wx.onMenuShareWeibo({
                    title: '{{ $shareData["share_title"] }}', 
                    desc: '{{ $shareData["share_desc"] }}', 
                    link: url, 
                    imgUrl: '{{ $shareData["share_img"] }}', 
                    success: function () {
                       
                    },
                    cancel: function () {
                        
                    }
                });
                wx.error(function(res){
                });
            });



        });
        function funcUrlDel(name){
            var loca = window.location;
            var baseUrl = loca.origin + loca.pathname + "?";
            var query = loca.search.substr(1);
            if (query.indexOf(name)>-1) {
                var obj = {}
                var arr = query.split("&");
                for (var i = 0; i < arr.length; i++) {
                    arr[i] = arr[i].split("=");
                    obj[arr[i][0]] = arr[i][1];
                };
                delete obj[name];
                var url = baseUrl + JSON.stringify(obj).replace(/[\"\{\}]/g,"").replace(/\:/g,"=").replace(/\,/g,"&");
                return url
            };
        }

    </script>
    <script src="{{ config('app.source_url') }}shop/js/evaluateDetail.js"></script>
@endsection
