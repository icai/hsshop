@extends('staff.base.head')
@section('head.css')

@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <!-- 表格区域 -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr class="success">
                    <th class="col-xs-1 col-md-1">操作名称</th>
                    <th class="col-xs-1 col-md-1">操作人</th>
                    <th class="col-xs-9 col-md-9">备注信息</th>
                    <th class="col-xs-1 col-md-1">操作时间</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $v)
                    <tr class="order_list">
                        <td style="vertical-align: middle">
                            @if($v['type'] == 1)设置域名
                            @elseif($v['type'] == 2)上传代码
                            @elseif($v['type'] == 3)获取页面
                            @elseif($v['type'] == 4)获取类目
                            @elseif($v['type'] == 5)提交审核
                            @elseif($v['type'] == 6)绑定体验者
                            @elseif($v['type'] == 7)提交发布
                            @elseif($v['type'] == 8)解绑体验者
                            @elseif($v['type'] == 9)查看消息模板
                            @elseif($v['type'] == 10)朕要体验
                            @elseif($v['type'] == 11)设置消息模板
                            @elseif($v['type'] == 12)取消审核
                            @elseif($v['type'] == 13)获取二维码
                            @elseif($v['type'] == 14)获取小程序码
                            @elseif($v['type'] == 15)添加备注
                            @elseif($v['type'] == 16)设置业务域名
                            @elseif($v['type'] == 17)版本回退
                            @elseif($v['type'] == 18)作废
                            @elseif($v['type'] == 19)下架
                            @elseif($v['type'] == 20)审核成功
                            @elseif($v['type'] == 21)审核失败
                            @elseif($v['type'] == 22)授权成功
                            @elseif($v['type'] == 23)加急审核申请
                            @elseif($v['type'] == 24)获取已添加插件
                            @elseif($v['type'] == 25)申请添加插件
                            @elseif($v['type'] == 26)快速更新插件版本
                            @elseif($v['type'] == 26)删除已添加插件
                            @endif
                        </td>
                        <td style="vertical-align: middle">{{ $v['login_name'] }}</td>
                        <td style="max-height:50px;overflow: hidden;border-right: 1px solid #dcdcdc;width: 300px;padding: 5px;word-break: break-all;word-wrap: break-word;overflow:hidden;vertical-align: middle">{{ $v['content'] }}</td>
                        <td style="vertical-align: middle">{{ $v['created_at'] }}</td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="text-align: right">
            {{$pageHtml}}
        </div>
        <!-- 表格区域 -->
    </div>
@endsection
@section('foot.js')
    <script type="text/javascript">
        var url = "{{ config('app.url') }}";
        re=new RegExp("https://","g");
        url=url.replace(re,"");
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
    <!-- ajax分页js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script>
@endsection