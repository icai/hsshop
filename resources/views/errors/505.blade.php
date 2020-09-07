<!DOCTYPE html>
<html>
    <head>
        <title>店铺已打烊</title>
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <style>
            html, body {
                height: 100%;
            }
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #797979;
                background: #F8F8F8;
                display: table;
                font-weight: 100;
            }
            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }
            .content {
                text-align: center;
                display: inline-block;
                margin-top: -200px;
            }
            .content .title {
                font-size: 20px
            }
            .content img{width: 160px; margin-bottom: 100px;}
        </style>
    </head>
    <body> 
        <div class="container">
            <div class="content">
            	<img src="{{ config('app.source_url') }}mctsource/images/dayangshop.png"/>
                <div class="title">您的店铺（{{ session('shop_name') }}）已打烊，为不影响正常运营，请拨打电话订购。订购热线：0571-87796692</div>
            </div>
        </div>
    </body>
</html>
