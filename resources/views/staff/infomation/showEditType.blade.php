@extends('staff.base.head')
@section('head.css')

    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/3.3.1 add_newClassify.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')

    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>资讯管理-添加分类</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="/staff/getInfoType">分类列表</a>
                    <span class="verLine">|</span>
                    <a href="##" style="color: #333;">添加分类</a>
                </div>
                <br />
                <form class="form-horizontal" id="myForm">
                    <input id="id" type="hidden"  @if($infoTypeData) value="{{$infoTypeData['id']}}"  @endif>
                    <div class="form-group">
                        <label for="thirdClassify" class="col-sm-2 control-label">一级分类名称</label>
                        <div class='input-group col-sm-2'>
                            <select @if($infoTypeData) disabled="disabled" @endif id="one" name="parent_id[]" class="form-control">
                                <option value="">一级分类</option>
                                    @forelse(json_decode($categoryData,true)[0] as $val)
                                        <option @if(!empty($infoTypeData) && $infoTypeData['type_path'][0] == $val['id'])selected='selected'@endif value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="thirdClassify" class="col-sm-2 control-label">二级分类名称</label>
                        <div class='input-group col-sm-2'>
                            <select @if($infoTypeData) disabled="disabled" @endif  id="sec" name="parent_id[]" class="form-control">
                                <option value="">二级分类</option>
                                @if($infoTypeData)
                                    @forelse(json_decode($categoryData,true)[$infoTypeData['type_path'][0]] as $val)
                                        <option @if(isset($infoTypeData['type_path'][1]) && $infoTypeData['type_path'][1] == $val['id'])selected='selected'@endif value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="thirdClassify" class="col-sm-2 control-label">@if($infoTypeData) 修改{{$infoTypeData['type_count']}}级@endif分类名称：</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="thirdClassify" name="name" placeholder="请输入分类名称" @if($infoTypeData) value="{{$infoTypeData['name']}}"  @endif>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="sub" type="button" class="btn btn-primary">确定</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript">
            var categoryData ={!! $categoryData !!};
        </script>
        @endsection
        @section('foot.js')
            <script src="{{ config('app.source_url') }}staff/hsadmin/js/3.3.1 add_newClassify.js" type="text/javascript" charset="utf-8"></script>
@endsection