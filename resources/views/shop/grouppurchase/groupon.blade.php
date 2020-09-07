@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css"> 
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/detail_1083503c2f5268412584b1783760782d.css"  media="screen">
 
@endsection

@section('main')
	<div class="container">
		 <div class="content">
		    <div class="js-groupon-guide guide-container clearfix">
		        <div class="guide-step guide-step1 tuan-guide-step1">
		            <p class="guide-text">
		                1.选择商品开团/参团
		            </p>
		        </div>
		        <div class="guide-step guide-step2 tuan-guide-step2">
		            <p class="guide-text">
		                2.邀请好友参团
		            </p>
		        </div>
		        <div class="guide-step guide-step3 tuan-guide-step3">
		            <p class="guide-text">
		                3.人满成团
		            </p>
		        </div>
		    </div>
		    <div class="block-list block">
		        <a class="block-item name-card goods-name-card" href="/shop/grouppurchase/detail/{{$rule['id']}}/{{session('wid')}}">
		            <div class="thumb">
		                <img data-src="/{{$rule['product']['img']}}"
		                class="js-lazy" src="/{{$rule['product']['img']}}">
		            </div>
		            <div class="detail">
		                <h3 class="l2-ellipsis font-size-14" style="height: 34px;">
		                    {{$rule['product']['title']}}
		                </h3>
		                <div class="price-tag">
		                    <p class="title">
		                        原价
		                    </p>
		                    <p class="price center">
		                        <span class="c-gray-darker">
		                            ¥
		                            <i class="font-size-16">
		                                {{$rule['product']['price']}}
		                            </i>
		                        </span>
		                        起/件
		                    </p>
		                </div>
		                <div class="price-tag red">
		                    <p class="title c-black">
		                        {{$rule['groups_num']}}人团
		                    </p>
		                    <p class="price center">
		                        <span class="c-red-ff574e">
		                            ¥
		                            <i class="font-size-16">
		                                {{$rule['min']}}
		                            </i>
		                        </span>
		                        起/件
		                    </p>
		                </div>
		            </div>
		        </a>
		    </div>
		 	@if($groups['status'] == 1)
		    <div class="tuan-info block center">
		        <p class="info-tips font-size-14">
		            <span class="inprogress icon">
		            </span>
		            <span>
		                已开团，离成团还差
		                <strong class="c-red font-size-16">
		                    {{$rule['groups_num']-$groups['num']}}
		                </strong>
		                人
		            </span>
		        </p>
		        <p class="font-size-12">
		            剩
		            <span class="js-time-count" data-seconds="86394">
		                <i class="time-wrap">
		                    23
		                </i>
		                :
		                <i class="time-wrap">
		                    56
		                </i>
		                :
		                <i class="time-wrap">
		                    13
		                </i>
		            </span>
		            自动结束
		        </p>
		        <p class="font-size-12 c-gray">
		            快去邀请好友参团吧！
		            <a class="font-size-12 c-blue" href="/shop/grouppurchase/guide">
		                玩法详情
		            </a>
		        </p>
		    </div>
			@elseif($groups['status'] == 2)
				 <div class="tuan-info block center">
					 <p class="info-tips font-size-14" style="color:#68A657;">
						 <span class="success icon"></span>
						 <span>赞！已顺利成团</span>
					 </p>
					 <p class="font-size-12 c-gray">
						 商家会尽快为你发货哟
						 <a class="font-size-12 c-blue" href="/shop/grouppurchase/guide">
							 玩法详情
						 </a>
					 </p>
				 </div>
			 @endif

		    <div class="block block-list tuan-member-list">
		        <div class="title c-gray font-size-12">
		            参团记录
		        </div>
				@forelse($groupsDetail as $val)
		        <div class="block-item name-card tuan-member-card">
		            <img src="{{$val['member']['headimgurl']}}" class="thumb">
		            <div class="detail font-size-12">
		                <h3>
		                    {{$val['member']['nickname']}}
							@if($val['is_head'] == 1)
		                    <span class="tag tag-member">
		                        团长大人
		                    </span>
								@endif
		                </h3>
		                <p>
		                    {{$val['created_at']}} @if($val['is_head'] == 1)开团@else 参团@endif
		                </p>
						@if($val['is_head'] == 1 && $groups['status'] == 1)
							<span class="groupon-detail-share js-groupon-share">
								<i>
								</i>
								<em>
									邀请码
								</em>
							</span>
						@endif
		            </div>
		        </div> 
		        @endforeach
		    </div> 
		    <div style="height:50px;"></div>
		    <div class="tuan-bottom bottom-fix">
				@if($groups['status'] == 1)
					<div class="btn-2-1">
						<button class="js-open-share btn btn-red">
							邀请好友参团
						</button>
					</div>
				@endif
				@if($groups['status'] == 2)
					<div class="btn-2-1">
						<button class="js-sel-goods btn btn-red">
							查看更多活动商品
						</button>
					</div>
				@endif

				@if($order_id)
					<div class="btn-2-1">
						<a class="tag tag-red font-size-14" href="/shop/order/detail/{{$order_id}}">
							查看订单详情
						</a>
					</div>
				@endif
				@if($groups['status'] == 1 && !$order_id)
					<div class="btn-2-1">
						@if(isset($is_overdue) && $is_overdue == 1)
						<a href="/shop/index/{{ session('wid') }}" class="btn btn-red">我要参团</a>
						@else
						<button class="js-join-group btn btn-red">
							我要参团
						</button>
						@endif
					</div>
				@endif
			</div>
		    <div id="js-share-guide" class="js-fullguide fullscreen-guide tuan-fullscreen-guide hide"
		    style="font-size: 16px; line-height: 35px; color: #fff; text-align: center;">
		        <div class="guide-arrow">
		        </div>
		        <div class="action-button center">
		            <button class="tag tag-red tag-big font-size-16">
		                我知道啦
		            </button>
		        </div>
		    </div>
		</div>
	</div> 
	<div id="OlYqBLIeVs" class="hide" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; z-index: 1000; transition: none 0.2s ease; opacity: 1; background-color: rgb(0, 0, 0);"></div>
	<div id="yvuLlGnRDS" class="hide" style="position: absolute; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); overflow: initial; visibility: visible; opacity: 1;width: 100%;">
		<div class="groupon-share-popup">
		  	<div class="img-content" style="height:55%;overflow:hidden;    text-align: center;">
		    	<img src="{{ imgUrl() }}{{$rule['product']['img']}}" style="height:100%;width: auto;max-width: none;"> 
		  	</div>
		  	<div style="height:45%;font-size:12px;">
		  		<div style="margin: 0 5%;padding: 40px 5% 0;border: 1px solid #eee;">
		  			<p style="line-height: 18px;letter-spacing: 1.2px;font-size:14px;">{{$rule['product']['title']}}）</p>
		  			<p style="text-align:right;margin-top:10px;font-size:14px; ">
		  				<span>{{$rule['groups_num']}}人团购价：</span>
		  				<span class="c-red-ff574e">￥{{$rule['min']}}</span>
		  			</p>
		  			<p style="margin-top:10px;">
						{!! QrCode::size(140)->generate(URL("/shop/grouppurchase/groupon/".$groups['id']).'/'.session('wid')); !!}
					</p>
		  		</div>
		  	</div>
  			<p class="note" style="width:100%;">手机截图发送给朋友来拼团吧</p>
		</div>
	</div>
	
	<script type="text/javascript">
        var end_time="{!! str_replace('-','/',$end_time) !!}";
        var nowTime="{!! $nowTime !!}";
        var ntime = new Date(nowTime).getTime();
        var groups_id = "{!! $groups['id'] !!}"
		var product_id = "{!! $rule['product']['id'] !!}";
		var rule = {!! json_encode($rule) !!};
		var wid = "{{ session('wid') }}";//店铺ID
        var pid = rule.pid; //商品ID
		var _host='{{ config('app.source_url') }}';
		var imgUrl ="{{ imgUrl() }}";
		var isBind = {{$__isBind__}};
	</script>
@endsection
@section('page_js')
<script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/js/groupon_byj6czr6.js" ></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
    	//微信分享
  		$(function(){	  	
  			var $jifen_tc = $('.jifen_tc');	
  			function jifentcShow(data){
	    		$jifen_tc.find('p').find('span').html(data);
				$jifen_tc.show();
	    	}	
	    	function jifenAjax(){
	    		$.ajax({
					type:"get",
					data:{},
					url:"/shop/point/addShareRecord/"+wid,
					dataType:"json",
					headers:{
						'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
					},
					success:function(data){
						if(data.errCode == 3 || data.errCode == 1 || data.errCode == 2){
							return false;
						}else{
							jifentcShow(data.data);
							setTimeout(function(){
								$jifen_tc.hide();
							},3000)
						}
					},
					error:function(data){
					}
				});
	    	}
		})
    </script>
@endsection