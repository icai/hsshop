@extends('shop.common.template')
@section('head_css')
<style>
.content{
    position:absolute;
    left:0;right:0;
    top:132px;
    margin:auto;
    text-align:center;
    font:22px/42px '微软雅黑';
    min-height:300px
}

.limitImage{
    padding-top:24px
}

.limitImage img{
    width:130px
}
footer .footer{
    padding-bottom:0;
    position:absolute;
    bottom:0;
    width:100%
}
</style>
@endsection
@section('main')
	<div class="content">
    <p class="">该商家尚未开通此功能</p>
    {{--<p>方可使用</p>--}}
    <div class="limitImage">
    <img src="{{ config('app.source_url') }}static/images/limitVisitor.png"/>
    </div>
    </div>



@endsection
@section('page_js')
	
@endsection
