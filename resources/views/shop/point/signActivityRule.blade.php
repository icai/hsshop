@extends('shop.common.template')
@section('head_css')
<!-- 当前页面css -->
 <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/signRule.css" /> 
@endsection

@section('main')
<body class=" ">

    <div class="container " style="min-height: 667px;">
        <div class="apps-game">
            <div class="checkin-rule-wrap">
            <div class="checkin-rule-title">活动说明：</div>
            <div class="checkin-rule-content description">没有活动说明</div>
            <div class="checkin-rule-title">活动规则：</div>
            <div class="checkin-rule-content">
                <ul class="checkin-rule-list">
                </ul>
            </div>
            <div class="checkin-rule-footer">
                备注：积分和奖品请到<a href="{{ config('app.url') }}/shop/member/index/{{session('wid')}}">会员中心</a>查看
                
                <div class="checkin-rule-footer-opt">
                    <a  href="/shop/point/sign/{{session('wid')}}" class="btn btn-opt">返回签到</a> 
                </div>
            </div>
         </div>    
    </div>


</body>
    
@endsection
@section('page_js')
<!-- 当前页面js -->
	<script src="{{ config('app.source_url') }}shop/js/signRule.js"></script>
    @if($reqFrom == 'aliapp')
    <script type="text/javascript" src="https://appx/web-view.min.js"></script>
    @endif
    @if($reqFrom == 'aliapp')
    <script type="text/javascript">
	    var xcx_share_title = '{{ $shareData["share_title"]??'' }}';
	    var xcx_share_desc = '{{ $shareData["share_desc"]??'' }}';
	    var xcx_image_url = '{{ $shareData["share_img"]??'' }}';
	    var url = location.href.split('#').toString();
	    if(window.location.search){
	      url += '&_pid_='+ '{{ session("mid") }}';
	    }else{
	      url += '?_pid_='+ '{{ session("mid") }}';
	    }
	    var xcx_share_url = url;
	    my.postMessage({share_title:xcx_share_title,share_desc:xcx_share_desc,share_url:xcx_share_url,imageUrl:xcx_image_url});
    </script>
    @endif
@endsection