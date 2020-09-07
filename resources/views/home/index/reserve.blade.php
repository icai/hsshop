@extends('home.base.head')
<title>{{$title}}</title>
@section('head.css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/dinggou2.css"/>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper.min.css"/>
@endsection
@section('content')
	<div class="suc-box">
		<div class="ding-suc">
			<h2><img width="35px" height="35px" src="{{ config('app.source_url') }}home/image/suc.png"/> &nbsp;提交成功！</h2>
			<p>专业的客户代表将在第一时间为您服务，请您耐心等候，也可直接电话预约哦~~~</p>
		</div>
	</div>    
    <div class="main_content">
    	<div class="change-title">
    		@if($type == 2 || $type == 1)
    		<div class="change-banner">
    			<img src="{{ config('app.source_url') }}home/image/pcban1.jpg"/>    		
    		</div>
    		@elseif($type == 3 || $type == 4 || $type == 5)
    		<div class="change-banner">
    			<img src="{{ config('app.source_url') }}home/image/pcban2.jpg"/>  			
    		</div>
    		@endif
    	</div>
        <div class="ding-box">
            <div class="ding-xiangmu">
                <form id="myform">
                    <div class="ding-ul">
                        @if($type == 1)
                            <h1 class="ding-xp2">智慧分销</h1>
                        @elseif($type == 2)
                            <h1 class="ding-xp2">APP定制</h1>
                        @elseif($type == 3)
                            <h1 class="ding-xp2">微信小程序</h1>
                        @elseif($type == 4)
                            <h1 class="ding-xp2">微信营销总裁班</h1>
                        @elseif($type == 5)
                            <h1 class="ding-xp2">微信商城</h1>
                        @endif

                    </div>
                    <div class="ding-input1">
                        <p class="dinp1"><b>您的称呼</b></p>
                        <input type="text" name="name" placeholder="请写下您的姓名/尊称"  class="chenghu get-focus" value="" />
                    </div>
                    <div class="ding-input2">
                        <p class="dinp1"><b>手机号码</b></p>
                        <input type="text" name="phone" placeholder="请输入正确的手机号，以便我们为您服务" class="haoma get-focus" value="" />
                    </div>
                    <div class="ding-input3">
                        <p class="dinp1"><b>所属行业</b></p>
                        <input type="text" name="industry" placeholder="例:电商、物流、零售、工业..." class="hangy get-focus" value="" />
                    </div>
                    <input type="hidden" name="type" value="{{ $type }}">
                    <!--<ul class="ding-hangye">
                        <li>电商</li>
                        <li>零售</li>
                        <li>工业</li>
                        <li>物流</li>
                        <li>食品</li>
                        <li>母婴</li>
                        <li>汽车</li>
                        <li>医疗</li>
                        <li>教育</li>
                        <li>O2O</li>
                        <li>农业</li>

                        <div class="ul-last">更多...</div>
                    </ul>-->
                    <div class="ding-input4">
                        <input style="background: #006285; border: 0; width: 100%;" type="button" name="yuyue" class="yuyue" value="开启预约" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var type = {{$type}}
    </script>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}static/js/swiper.jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}home/js/reserve.js" type="text/javascript" charset="utf-8"></script>
@endsection