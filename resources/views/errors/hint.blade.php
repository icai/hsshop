<!DOCTYPE html>
<html>
    <head>
        <title>信息反馈页面</title>
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
                display: table;
                font-weight: 600;
            }
            .container {
                text-align: center;
                margin-top: 200px; 
            }
            .content {
                text-align: center;
                display: inline-block;
                margin: 0 100px;
            }
            .title {
                font-size: 50px;
                margin-bottom: 40px;
                display: block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <img src="{{ config('app.source_url') }}shop/images/no_goods.png">
                <div class="title">
                    <p>{{ $info }}</p>
                    <p id="start_time"></p>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript">
        var _url = "{{ $url }}";
        var res={!! json_encode($data) !!};

        // 许立 2018年07月18日 只有预售报错才显示预售时间
        if (res.err_code == -6) {
            var data = res.data.split(" ");
            data = data[0].split("-");
            var start_time = document.getElementById("start_time");
            start_time.innerHTML = '开售时间:'+data[0]+'年'+data[1]+'月'+data[2]+'日';
        }

        if ( _url ) {
            setTimeout(function() {
                window.location.href = _url;
            }, 3000);
        }
    </script>
</html>
