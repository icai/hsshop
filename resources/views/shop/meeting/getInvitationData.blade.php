<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
</head>
<body>

<table class="table table-striped">
    <h1 class="text-center">业务数据查询</h1>
    <thead>
    <tr>
        <th>姓名</th>
        <th>电话</th>
        <th>邀请人数</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $val)
    <tr onclick="window.location.href='/shop/meeting/invitationDetail?mobile={{$val['mobile']}}&name={{$val['name']}}'" style="cursor:pointer;">
        <td>{{$val['name']}}</td>
        <td>{{$val['mobile']}}</td>
        <td>{{$val['num']}}</td>
    </tr>
    @endforeach
    </tbody>
</table>


</body>
</html>