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
    <h1 class="text-center">{{$name}}的数据详情</h1>
    <thead>
    <tr>
        <th>姓名</th>
        <th>电话</th>
        <th>公司名称</th>
        <th>公司地址</th>
        <th>职位</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $val)
    <tr>
        <td>{{$val['name']}}</td>
        <td>{{$val['phone']}}</td>
        <td>{{$val['company_name']}}</td>
        <td>{{$val['company_address']}}</td>
        <td>{{$val['company_position']}}</td>
    </tr>
    @endforeach
    </tbody>
</table>


</body>
</html>