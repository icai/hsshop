@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_ufgyf3as.css" />
@endsection
@section('slidebar')
    @include('merchants.member.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{URL('/merchants/member/import')}}">导入记录</a>
                </li>
                <li>
                    <a href="##">新建导入</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>
        <!-- 三级导航 结束 -->

        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection

@section('content')
    <div class="content">
        <form id="defaultForm" method="post" class="form-horizontal" action="" enctype="multipart/form-data" >
            <div class="form-group">
                <label class="col-lg-2 control-label" for="">会员卡：</label>
                <div class="col-lg-9">
                    <select name="card_id">
                        @foreach($cardRow as $c)
                            <option value="{{$c['id']}}">{{$c['title']}}</option>
                        @endforeach
                    </select>
                    <p class="hint">目前仅支持外部会员导入，如该客户已存在于系统中，可在客户管理/会员管理中发放或调整会员卡，已存在或导入过的客户不支持再次导入</p>
                </div>
            </div>
           
            <div class="form-group">
                <label class="col-lg-2 control-label" for="">文件：</label>
                <div class="col-lg-9 ">
                    <div class="file_board position_rel">
                        <a href="##" class="add_file">+选择文件。。</a>
                        <p class="fileName">
                            <span class="file_name">1.csv</span>
                            <span class="file_size">0.16KB</span>
                        </p>
                    </div>
                    <input type="file" name="file" id="add_file" class="form-control position_abs" value="" accept="*.csv"/>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <p class="hint">（当前仅支持导入csv和excel格式的文件（大小在10M以内），为保证您的客户能顺利导入，请按模板格式提交文件
                        <a href="{{ config('app.url') }}hsshop/other/template/member_import.csv">下载模板</a> ）
                    </p>
                </div>
            </div>
            <div class="form-group footer">
                <label class="col-lg-2 control-label" for=""></label>
                <div class="col-lg-7">
                    <button type="submit" class="btn btn-primary sure">确定</button>
                    <a href="{{URL('/merchants/member/import')}}" type="button" class="btn btn-default cancle" data-dismiss="modal">返回</a>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('page_js')
    <!--bootstrap表单验证js文件引入-->
    <script src="{{config('app.source_url')}}static/js/bootstrapValidator.min.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/member_niiopy3e.js"></script>
    <script src="{{ config('app.source_url') }}static/js/bootstrapValidator.min.js"></script>
@endsection