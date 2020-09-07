<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="utf-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/css/swiper-3.4.0.min.css" />
     <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/previewblade.css" />
</head>
<body> 
<div class="main">
	{{--<div class="top">--}}
		{{--<a href="{{URL('/merchants/product/editproduct/'.$product['product']['id'])}}">编辑本商品</a>--}}
		{{--<a href="{{URL('/merchants/product/create/'.$product['product']['id'])}}">发布新商品</a>--}}
	{{--</div>--}}
    <div class="content">
    	<!--内容-->
    	<div class="content_left">
    		<!--轮播-->
    		<div class="content_top">
    			<div class="swiper-container">
	    			<div class="swiper-wrapper">
						@if($product['product']['productImg'])
						@forelse($product['product']['productImg'] as $val)
							<div class="swiper-slide">
								<img src="{{ imgUrl($val['img']) }}" />
							</div>
						@endforeach
						@endif
	    				{{--<div class="swiper-slide">--}}
	    					{{--<img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=281476135,1439443472&fm=27&gp=0.jpg" alt="" />--}}
	    				{{--</div>--}}
	    				{{--<div class="swiper-slide">--}}
	    					{{--<img src="https://ss1.bdstatic.com/70cFvXSh_Q1YnxGkpoWK1HF6hhy/it/u=2935802322,4116373145&fm=27&gp=0.jpg" alt="" />--}}
	    				{{--</div>--}}
	    			</div>
	    			<div class="swiper-pagination"></div>
	    		</div>
                <!-- add by 魏冬冬 2018-7-16 预售倒计时 -->
                @if($product['product']['sale_timestamp'] > $product['product']['now_timestamp'])
                <div class="timeout" v-if="sale_time_flag == 2 && showPreSell">
                    <p class="countdown">距离结束
                        <span>00</span>天<span>00</span>时<span>00</span>分<span>00</span>秒
                    </p>
                </div>
                @endif
                <!-- end -->
    		</div>
    		<!--详情-->
    		<div class="content_detail">
    			<div class="detail_top">
    				<p>{{ $product['product']['title'] or ''}}</p>
					@if($product['product']['is_price_negotiable'] == 0)
						<div class="detail_money"><span>¥</span><i>{{ $product['product']['showPrice'] or 0}}</i></div>
						@if( $product['product']['oprice'] > 0 )
						<div class="detail_mVal">市场价：¥{{ $product['product']['oprice'] }}</div>
						@endif
					@else
					<!--updata by 邓钊 2018-7-13 价格面议-->
						@if($product['product']['negotiable_type'] == 0)
							<div style="color:#E5333A;">价格面议</div>
						@elseif($product['product']['negotiable_type'] == 1)
							<div style='display: flex;justify-content: space-between;'>
								<div style="color:#E5333A; font-size: 16px;">咨询电话:{{$product['product']['negotiable_value']}}</div>
								<a class='btn_a' href="javascript:;void(0)">拨打电话</a>
							</div>
						@else
							<div style='display: flex;justify-content: space-between;'>
								<div style="color:#E5333A; font-size: 16px;">咨询微信:{{$product['product']['negotiable_value']}}</div>
								<a class='btn_a' href="javascript:;void(0)">复制微信</a>
							</div>
						@endif
					<!--end-->
					@endif
    			</div>
                <div class="detail_bottom">
                	<dl>
                		<dt>运费：</dt>
						<!--商品预览 如果设置的是运费模板 预览页没有默认地址 所以暂时显示0-->
                		<dd>¥{{$product['product']['freight_type'] == 1 ? $product['product']['freight_price'] : 0}}</dd>
                	</dl>
					@if(!$product['product']['stock_show'])
						<dl>
							<dt>库存:</dt>
							<dd>{{$product['product']['stock'] or 0}}</dd>
						</dl>
					@endif
                	<dl>
                		<dt>销量：</dt>
                		<dd>{{$product['product']['sold_num'] or 0}}</dd>
                	</dl>
                </div>

			</div>
    		<!--店铺-->
    		<div class="content_shop">
    			<div class="shop_top">
    				<div>
    					<img src="{{ config('app.source_url') }}shop/images/previewblade_shop.png" alt="" />
    					<span>{{ $product['shop']['shop_name'] or ''}}</span>
    				</div>
    				<div class="join">
    					<span></span> &gt;
    				</div>
    			</div>
				@if($__storeNumber__>0)
    			<!-- <div class="shop_top">
    				<div>
    					<img src="{{ config('app.source_url') }}shop/images/previewblade_store.png" alt="" />
    					<span>线下门店</span>
    				</div>
    				<div class="join">
    					<span></span> &gt;
    				</div>
    			</div> -->
				@endif
    			<div class="shop_bottom">
    				<!-- <span class="shop_bottomsapn">
    					<img src="{{ config('app.source_url') }}shop/images/previewblade_shopright.png" alt="" />
    					<span>企业认证</span>
    				</span>
    				<span class="shop_bottomsapn">
    					<img src="{{ config('app.source_url') }}shop/images/previewblade_shopright.png" alt="" />
    					<span>担保交易</span> -->
    				</span>
					@if($__storeNumber__>0)
    				<!-- <span class="shop_bottomsapn">
    					<img src="{{ config('app.source_url') }}shop/images/previewblade_shopright.png" alt="" />
    					<span>线下门店</span>
    				</span> -->
					@endif
    			</div>
    		</div>
    		
    		<!--商品详情/累计评价-->
    		<div class="content_tab">
    			<a href="javascript:;">商品详情</a>
				<a href="javascript:;">累计评价</a>
    		</div>
			{{--<div class="tab_detail">{!!  $content !!}</div>--}}

			<div class="tab_detail">
			@if($content)
				@foreach($content as $v)
					{!!  $v !!}
				@endforeach
			@endif
			</div>
            
            <!--更多精选商品-->
			@if(!empty($product['more']))
    		<div class="content_more">
    			<p>更多精选商品</p>
    			<ul>
					@forelse($product['more'] as $value)
    				<li class="more_li">
    					<a href="/shop/preview/{{ $value['wid'] }}/{{ $value['id'] }}">
    						<div class="li_img"><img src="{{ imgUrl($value['img']) }}" alt="" /></div>
    						<dl>
    							<dt>{{$value['title']}}</dt>
								@if($value['is_price_negotiable'] == 0)
    							    <dd><span>¥</span><i>{{$value['price']}}</i></dd>
								@else
									<div class="detail_top" style="font-size: 16px;color: red;line-height: 30px">价格面议</div>
								@endif
    						</dl>
    					</a>
    				</li>
					@endforeach

    			</ul>
				<p class="center" style="margin: 10px 0 20px;">
					<a href="" class="center btn btn-white btn-xsmall font-size-14 " style="padding:8px 26px;">进店逛逛&gt;</a>
				</p>
    		</div>
		    @endif
    		<!--底部-->
    		<div class="content_bottom">
    			<div class="bottom_cart messageHints">
    				<img class="cart" src="{{ config('app.source_url') }}shop/images/previewblade_cart.png" alt="" />
    				<span>购物车</span>
    			</div>
    			<div class="messageHints">
    				<img class="serve" src="{{ config('app.source_url') }}shop/images/previewblade_kefu.png" alt="" />
    				<span>客服</span>
    			</div>
    			<div class="buyNow">
    				加入购物车
    			</div>
                @if($product['product']['sale_timestamp'] > $product['product']['now_timestamp'])
                <div style="background:#999;color:#fff">
                    立即购买
                </div>
                @else
                <div class="buyNow">
                    立即购买
                </div>
                @endif
    		</div>
    		<!--规格-->
    			<div class="mask hideNoSee"></div>
    			<div class="content_sizeBox hideNoSee">
    				<div class="sizeBox_top">
    					<dl>
	    					<dt><img src="{{ imgUrl($product['product']['productImg'][0]['img'])}}" /></dt>
	    					<dd>
	    						<p>{{ $product['product']['title'] or ''}}</p>
	    						<p>{{ $product['product']['showPrice'] or 0 }}</p>
	    					</dd>
	    				</dl>
	    				<div class="delete">X</div>
    				</div>
					@if($sku['props'])
    				<div class="sizeBox_size">
						@foreach($sku['props'] as $k=>$v)
						<p>{{ $v['props']['title'] }}：</p>
							<ul>
							@foreach($v['values'] as $key=>$val)
								<li>{{ $val['title'] }}</li>
							@endforeach
							</ul>
					    @endforeach
    				</div>
					@endif
    				<div class="sizeBox_num">
    					<div class="num_div">
    						<p>购买数量：</p>
    						<p><span>-</span><span>1</span><span>+</span></p>
    					</div>

    					<p class="num_p">剩余<span>{{ $product['product']['stock'] or 0}}</span>件</p>

    				</div>
    				<div class="sizeBox_bottom">
    					下一步
    				</div>
    			</div>
    	</div>
    	<!--二维码-->
    	<div class="content_right">
    		<div class="right_top">
    			<img src="{{ config('app.source_url') }}shop/images/previewblade_erweima.png" alt="" />
    			<span>手机扫码购买</span>
    		</div>
    		<p>微信“扫一扫”关注购买</p>
    		{{--<img class="right_img" src="{{ config('app.source_url') }}shop/images/previewblade_weixin.png" alt="" />--}}
			<p class="text-center qr-code">
				<!--如果授权小程序则返回小程序商品预览二维码 否则返回公众号二维码-->
				{!! $qr_code_preview !!}
			</p>
    	</div>
    </div>
</div>   
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script> 
    <script src="{{ config('app.source_url') }}shop/static/js/swiper-3.4.0.min.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
    <script type="text/javascript">
        var mySwiper = new Swiper('.swiper-container', {
            autoplay: 2000,//可选选项，自动滑动
            loop : true,
            pagination : '.swiper-pagination'
        });
        var imgUrl = "{{ imgUrl() }}";
    </script>
    <script src="{{ config('app.source_url') }}shop/js/previewblade.js"></script>
</body>
</html>