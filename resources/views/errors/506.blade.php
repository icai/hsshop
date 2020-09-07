<!DOCTYPE html>
<html>
    <head>
        <title>店铺已打烊</title>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <style>
            html, body {
                height: 100%;
            }
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                background: #F8F8F8;
                display: table;
                font-weight: 100;
                font-family: 'Lato', sans-serif;
            }
            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }
            .content {
                text-align: center;
                display: inline-block;
            }
            .content .title {
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .content img{ width: 40%; }
            .content .button{ 
            	display: inline-block;
            	text-decoration: none;
            	margin-top: 120px;
            	width: 80%; 
            	height: 40px;
            	line-height: 40px;
            	border: 1px solid #E5E5E5;
            	border-radius: 5px;
            	background: white;
            	color: #333;
            }
        </style>
    </head> 
    <body>
        <div class="container">
            <div class="content">
            	<img src="{{ config('app.source_url') }}mctsource/images/dayangshop.png"/>
                <div class="title">{{ $shopName }}已打烊</div>
                <div class="info">如需查看历史购买记录请查看会员主页</div>
                <a class="button" href="/shop/member/index/{{ session('wid') }}">前往会员主页</a>
            </div>
        </div>
    </body>
</html>
