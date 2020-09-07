@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/3.2.1 news_detail.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>资讯管理-查看详情</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <p>查看详情</p>
                    <p>所属分类：{{$infoData['type_path']}}</p>
                </div>
                <hr />
                <div class="artical">
                    <h4>{{$infoData['title']}}</h4>
                    <p class="subtitle">{{ $infoData['sub_title']}}</p>
                    <p class="essay">{!! $infoData['content'] !!}</p>
                    <div class="btns">
                        <button type="button" class="btn btn-default recom">@if(empty($infoRecommendData))推荐@else已推荐@endif</button>
                        <button type="button" class="btn btn-primary modify" onclick="window.location.href='/staff/editInformation?id={{$infoData['id']}}'">修改</button>
                        <button id="del" type="button" class="btn btn-warning delete">删除</button>
                        <input id="infoId" type="hidden" value="{{$infoData['id']}}" />
                    </div>
                </div>
            </div>
            <!--推荐内容-->
            <div class="recommend">
                <form id="myForm">
                <div class="row indexPage">
                    <div class="left_title col-xs-2">首页：</div>
                    <div class="checkboxDiv col-xs-3">
                        <input type="hidden" name="id" value="{{$infoData['id']}}">
                        @forelse($recommendData['data'] as $val)
                        <label><input type="checkbox" @if(in_array($val['id'],$infoRecommendData)) checked=checked @endif name="recommentIds[]" id="" value="{{$val['id']}}" />{{$val['name']}}--{{$val['content']}}</label><br />
                            @endforeach
                    </div>
                </div>
                <button  type="button" class="btn btn-primary">确定推荐</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/3.2.1 news_detail.js" type="text/javascript" charset="utf-8"></script>
@endsection