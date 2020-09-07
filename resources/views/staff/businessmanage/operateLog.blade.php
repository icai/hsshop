@extends('staff.base.head')
@section('head.css')
    
@endsection
@section('content')
    <div class="main-log" style="margin-top:160px">
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
                @forelse($list as $v)
                    <tr class="order_list">
                        <td style="vertical-align: middle">{{$v['action_name']}}</td>
                        <td style="vertical-align: middle">{{$v['login_name']}}</td>
                        <td style="max-height:50px;overflow: hidden;border-right: 1px solid #dcdcdc;width: 300px;padding: 5px;word-break: break-all;word-wrap: break-word;overflow:hidden;vertical-align: middle">{{$v['content']}}</td>
                        <td style="vertical-align: middle">{{$v['created_at']}}</td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </div>     
        <!-- 表格区域 -->
        <div style="text-align:right;">{{$pageHtml}}</div>
    </div>
@endsection
@section('foot.js')
    
@endsection