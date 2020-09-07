@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/materialDetail.css"  media="screen">
@endsection
@section('main')
<div class="app" style="min-height: 100%;">
   <h2>{{ $returnData['title'] }}</h2>
   <div class="top">
   		<span>{{ $returnData['created_at'] }}</span><span>{{ $returnData['author'] or '' }}</span><span>@if(@$weixinData)<a href="weixin://profile/{{ $weixinData['weixinConfigSub']['original_id'] or '' }}">{{ $weixinData['weixinConfigSub']['name'] or '' }}</a>@endif</span>
   </div>
   @if($type == 1 && $returnData['show_cover_pic'] == 1)
   <div class="tw_img">
   		<img src="{{ imgUrl($returnData['cover']) }}" alt="" />
   </div>
   @elseif ($type ==2 && $returnData['cover']) 
   <div class="tw_img">
         <img src="{{ imgUrl($returnData['cover']) }}" alt="" />
   </div>
   @endif
   <div class="tw_content">
      @if($type == 1)
		{!! $returnData['content'] !!}
      @elseif($type == 2)
      {{ $returnData['digest'] }}
      @endif
   </div>
   @if($returnData['content_source_url'])
   <div class="tw_read">	   
	   	<a href="{{ $returnData['content_source_url'] }}">阅读原文</a>
   </div>
   @endif
</div>
@include('shop.common.footer')
@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";;
	var host ="{{ config('app.url') }}";;
	var imgUrl = "{{ imgUrl() }}";
</script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/clipboard.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}shop/js/materialDetail.js" ></script>
@endsection