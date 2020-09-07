<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <!-- <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"> -->
    <link href="{{ config('app.source_url') }}static/css/bootstrap.min.css" rel="stylesheet">
    <!-- 搜索美化插件 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/chosen.css">
    <!-- 核心bootstrap-rewrite.css文件（覆盖bootstrap样式，每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-rewrite.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('static/css/base.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/base.css">
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_uporderprice.css" /> 
</head>
<body>
    <!-- 头部区域 -->
    <div class="header">
        <p class="header-title">订单原价(不含运费)0.01元</p>
        <a href="javascript:;" class="header-close">×</a>
    </div>
    <!-- 头部区域 -->
    <!-- 中间区域 -->
    <div class="content">
        <!-- 订单信息table -->
        <table class="order-table">
            <thead>
                <tr>
                    <th class="w-140">商品</th>
                    <th>单价(元)</th>
                    <th>数量</th>
                    <th>小计(元)</th>
                    <th>店铺优惠</th>
                    <th>涨价或减价</th>
                    <th>运费(元)</th>
                </tr>
            </thead> 
            <tbody>
                <tr>
                    <td class="bule">实购商品(购买时需填写收货地址,测试商品,不发货,不退款)</td>
                    <td>0.01</td>
                    <td>1</td>
                    <td>0.01</td>
                    <td></td>
                    <td> <input type="text" class="form-control w-70" value="0" name=""></td>
                    <td> 
                        <input type="text" class="form-control w-70" value="0.00" name=""> 
                        <a href="javascript:;" class="freight t-bule">直接免运费</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- 中间区域 -->
    <!-- 底部区域 -->
    <div class="footer">
        <p>收货地址：浙江省杭州市江干区东方电子商务园7幢5楼</p>
        <p>买家实付：0.01+0.00+0.00=0.01</p>
        <p>买家实付：原价+运费+涨价或减价</p>
        <!-- 按钮区域 -->
        <button class="btn btn-yes">确定</button>
    </div>
    <!-- 底部区域 -->


    <!-- jQuery.js -->
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- layer -->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script type="text/javascript">
        // 页面加载完成
        $(function(){ 
            //点击确定按钮-提交信息
            $(".btn-yes").click(function(){
                
            });
            //点击取消按钮-关闭layer
            $(".btn-close").click(function(){
                parent.layer.closeAll('iframe');
            }); 
            //点击取消按钮-关闭layer
            $(".header-close").click(function(){
                parent.layer.closeAll('iframe');
            }); 
        }); 
    </script>
</body>
</html>
