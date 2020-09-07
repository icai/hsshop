@extends('shop.common.vote_template')
@section('title', $title)
@section('head_css') 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/LCalendar.min.css"  media="screen">
    <!--时间插件css-->	
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/mobileSelect.css">
    <!--当前css-->	
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/book_detail.css"  media="screen">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/mobileSelect.css"  media="screen">
@endsection
@section('main')
	<div class="app" v-cloak >
		<div class="detail_banner">
			@if(isset($data['banner_img']) && $data['banner_img'])
			<img src="{{ $data['banner_img'] }}" alt="" />
			@else
			<img src="{{ config('app.source_url') }}shop/images/book_detail.png" alt="" />
			@endif
		</div>
		<div class="detail_order">
			<p>我的预约</p>
			<div><a href="/shop/book/user/list/{{ $wid }}/{{ $data['id'] }}">{{ $data['bookNum'] }}</a>&gt;</div>
		</div>
		<div class="detail_explain">
			@if($data['details'])
			<p class="detail_explaintop">预约说明</p>
			<div>
				<p>{!! $data['details'] !!}</p>
			</div>
			@endif
			<p class="detail_explainbottom">{{ $data['limit_text'] }}</p>
		</div>
		@if($data['address'] && $data['phone'])
		<div class="detail_phone">
			@if($data['address'])
			<a href="//map.baidu.com/mobile/webapp/search/search/foo=bar&qt=s&wd={{ $data['address'] }}/vt=map">
				<div class="detail_pdiv">
					<div><img src="" alt="" />地址：{{ $data['address'] }}</div>
					<span>&gt;</span>
				</div>
			</a>
			@endif
			@if($data['phone'])
		    <a onclick="openModal( {{ $data['phone'] }} )">
				<div>
					<div><img src="" alt="" />联系电话：{{ $data['phone'] }}</div>
					<span>&gt;</span>
				</div>
		    </a>
		    @endif	
		</div>
		@endif
		@if(empty($userBookDatas))
		<div class="detail_list">
			<p>请认真填写表单</p>
			@if(isset($data['formData']) && $data['formData'])
			@foreach($data['formData'] as $key => $val)
			<div class="formData">
				@if($val['itype'] == 'text')
				<label for="">{{ $val['ikey'] }}</label><input type="text" placeholder="{{ $val['ival'] or '' }}" class="{{ $val['iclass'] or '' }}" value="{{ $val['icontent'] or '' }}" @if((isset($val['iclass']) && $val['iclass'] == 'book_date') || (isset($val['iclass']) && $val['iclass'] == 'book_time')) readonly="readonly" style="-webkit-user-select: none;" @endif />
				@else
				<label for="">{{ $val['ikey'] }}</label>
				<select>
					@foreach($val['option'] as $item)
					<option value="{{ $item }}" @if(isset($item['icontent']) && $item['icontent'] == $item) selected="selected" @endif>{{ $item }}</option>
					@endforeach
				</select>
				@endif
			</div>
			@endforeach
			@endif
			<div class="detail_lremarks">
				<h2>备注</h2><textarea class="remark" name="" rows="" cols="" placeholder="请输入备注信息"></textarea>
			</div>
		</div>
		<input class="detail_submit" type="submit" value="提交消息"/>
		@endif
	</div>
	
@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";
    var wid = "{{ $wid }}"
    var bookId = "{{ $data['id'] }}";
    var limit_type = "{{$data['limit_type']}}";
    var start_at = "{{$data['start_time']}}";
    var end_at = "{{$data['end_time']}}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!--当前js-->
<script src="{{ config('app.source_url') }}shop/js/mobileSelect.js" type="text/javascript"></script>
<script src="{{ config('app.source_url') }}shop/js/LCalendar.js"></script>
<script src="{{ config('app.source_url') }}shop/js/mobileSelect.js"></script>
<script src="{{ config('app.source_url') }}shop/js/book_detail.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
	function openModal(tel){
		var content="确定拨打电话："+tel+"吗？";
		var sureTitle="确定";
		var cancleTitle="取消";
		tool.notice(1,"提示",content,sureTitle,sureBtn,cancleTitle,cancleBtn)
		function sureBtn(){
			window.location.href="tel:"+tel;
		}
		function cancleBtn(){
			$("#mask").remove();
		}
	}

	// 微信分享
    $(function(){

        $.get("/shop/isSubscribe",function(data){
            if(data.status == 1){
                if(data.data.subscribe == 0)
                {
                    $('.top_attention').text('关注我们');
                    $('.top_attention').removeClass('hide');
                }
            }
        });

        $.get("/shop/getApiName",function(data){
            if(data.status == 1){
                $('.code img').attr('src',data.data.url);
                $('.other_opt').text('若无法识别二维码');
                var html = " <p>1.打开微信，点击“公众号”</p>" +
                    "<p>2.搜索公众号："+ data.data.name +"</p>" +
                    "<p>3.点击“关注”，完成</p>";
                $('.opt').html(html);
                $('.set').removeClass('hide');
                $('.noset').addClass('hide');
            }else {
                $('.set').addClass('hide');
                $('.noset').removeClass('hide');
            }
        });

        var url = location.href.split('#').toString();
        $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
            if(data.errCode == 0){
                wx.config({
                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: data.data.appId, // 必填，公众号的唯一标识
                    timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                    nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                    signature: data.data.signature,// 必填，签名，见附录1
                    jsApiList: [
                        'checkJsApi',
                        'onMenuShareTimeline',
                        'onMenuShareAppMessage',
                        'onMenuShareQQ',
                        'chooseWXPay'
                    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
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
                title: '{{ $shareData["share_title"] }}', // 分享标题
                desc: '{{ $shareData["share_desc"] }}', // 分享描述
                link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                imgUrl: '{{ $shareData["share_img"] }}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数(分享成功后送积分)
                    $.get("/shop/point/addShareRecord/{{ session('wid') }}",function(data){
                    });
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //分享给朋友
            wx.onMenuShareAppMessage({
                title: '{{ $shareData["share_title"] }}', // 分享标题
                desc: '{{ $shareData["share_desc"] }}', // 分享描述
                link: url, // 分享链接
                imgUrl: '{{ $shareData["share_img"] }}', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数(分享成功后送积分)
                    $.get("/shop/point/addShareRecord/{{ session('wid') }}",function(data){
                    });
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //分享到QQ
            wx.onMenuShareQQ({
                title: '{{ $shareData["share_title"] }}', // 分享标题
                desc: '{{ $shareData["share_desc"] }}', // 分享描述
                link: url, // 分享链接
                imgUrl: '{{ $shareData["share_img"] }}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数(分享成功后送积分)
                    $.get("/shop/point/addShareRecord/{{ session('wid') }}",function(data){
                    });
                },
                cancel: function () {
                   // 用户取消分享后执行的回调函数
                }
            });

            //分享到腾讯微博
            wx.onMenuShareWeibo({
                title: '{{ $shareData["share_title"] }}', // 分享标题
                desc: '{{ $shareData["share_desc"] }}', // 分享描述
                link: url, // 分享链接
                imgUrl: '{{ $shareData["share_img"] }}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数(分享成功后送积分)
                    $.get("/shop/point/addShareRecord/{{ session('wid') }}",function(data){
                    });
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.error(function(res){
                // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
                //alert("errorMSG:"+res);
            });
        });

    });
</script>
@endsection
