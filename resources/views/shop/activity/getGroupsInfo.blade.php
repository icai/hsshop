<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
</head>
<body>


    <table class="table table-striped" >
        <thead>
        <tr>
            <th>团编号</th>
            <th>开团时间</th>
            <th>团状态</th>
            <th>订单id</th>
            <th>团长</th>
            <th>成团时间</th>
            <th>留言信息</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data['data'] as $val)
        <tr>
            <td>{{$val['identifier']}}</td>
            <td>{{$val['open_time']}}</td>
            <td>@if($val['status']==0)未开团@elseif($val['status']==1)待成团@elseif($val['status']==2)已成团@elseif($val['status']==3)未成团@endif</td>
            <td>{{$val['oid']}}</td>
            <td>@if($val['is_head'] == 1)是@else 否@endif</td>
            <td>{{$val['created_at']}}</td>
            <td>
            @forelse($val['remark'] as $item)
            {{$item['title']??''}}:{{$item['content']??''}}<br/>
            @endforeach
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

</body>
</html>