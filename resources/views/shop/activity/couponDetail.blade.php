@extends('shop.common.marketing')
@section('head_css')
<link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/promocard_680e494e2e611c4736744ecaff13dda1.css">
@endsection
@section('main')
<body class=" promocard">
    <div class="container">
        <div class="promocard-body">
            <div class="coupon">
                <div class="shop-info">
                    <figure class="bg-pic circle-bg-pic">
                        
                        @if ( !$data['shop_logo'] )
		                	<div class="bg-pic-content">
			               		<img src="{{ config('app.source_url') }}home/image/huisouyun_120.png" width="100%" height="100%">
			                </div>
		                @else
		                	<div class="bg-pic-content" style="background-image: url({{ imgUrl($data['shop_logo']) }});"></div>
		                @endif
                    </figure>
                    <p>{{$data['shop_name']}}</p>
                </div>
                <div class="coupon-msg">送你一张优惠券</div>
            </div>
            <form class="js-form promocard-fetch-form" onsubmit="return false;">
                <input type="hidden" name="couponId" id="couponId" value="{{$data['id']}}"/>
                <div class="form-item">
                    <a class="js-btn-get btn btn-block">领取优惠券</a>
                </div>
            </form>
            @if(!empty($data['description']))
            <div class="text-center">
                <div class="js-show-instruction promocard-instruction">
                    <i class="circle-icon">i</i>
                    <span>使用说明</span>
                </div>
            </div>
            @endif
        </div>
    </div>
    
</body>
<div id="gaEt2zpwQ4" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;display:none">
</div>
<div id="AreQlip9Xl" class="popout-box" style="overflow: hidden; position: absolute; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px; padding: 15px; opacity: 1;display:none">
    <div class="promocard-desc">
        <h3>优惠券使用说明</h3>
        <p>@php echo nl2br(e($data['description'])) @endphp</p>
        <div class="action-container">
            <button type="button" class="btn btn-green btn-block js-ok">我知道了</button>
        </div>
    </div>
</div>
@include('shop.common.footer') 
@endsection
@section('page_js')
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script type="text/javascript">
        $('.js-show-instruction').click(function(){
            $('#gaEt2zpwQ4').show();
            $('#AreQlip9Xl').show();
        })
        $('.js-ok').click(function(){
            $('#gaEt2zpwQ4').hide();
            $('#AreQlip9Xl').hide();
        });
//      领取优惠券
		var wid = "{{ session('wid') }}";
		var id =  "{{$id}}";
		$(".js-btn-get").click(function(e){
			window.location.href="/shop/activity/couponReceive/"+wid+"/"+id;		
		})
    </script>
@endsection