<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css" /> 
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/page-print.css?v=4.0" /> 
</head>
<body>
    <div id="print-area">  
        <!-- 开始渲染 -->
        <div class="print-paper">
            <div class="express-wrapper">
                <div class="express-bill-field-container">
                    <span class="express-bill-field shipper-name">田小文</span>
                    <span class="express-bill-field shipper-city">杭州</span>
                    <span class="express-bill-field shipper-phone">18368029000</span>
                    <span class="express-bill-field shipper-company">会搜云股份</span>
                    <span class="express-bill-field shipper-address">杭州市江干区九和路18号东方电子商务园7幢5层</span>
                    <span class="express-bill-field consignee-name">田小文</span>
                    <span class="express-bill-field consignee-city">杭州</span>
                    <span class="express-bill-field consignee-phone">18368029000</span>
                    <span class="express-bill-field consignee-address">浙江省温州市龙市龙华路16号</span>
                </div>
            </div>
        </div> 
        <div class="print-paper">
            <div class="express-wrapper">
                <div class="express-bill-field-container">
                    <span class="express-bill-field shipper-name">田小文</span>
                    <span class="express-bill-field shipper-phone">18368029000</span>
                    <span class="express-bill-field shipper-company">会搜云股份</span>
                    <span class="express-bill-field shipper-address">杭州市江干区九和路18号东方电子商务园7幢5层</span>
                    <span class="express-bill-field consignee-name">田小文</span>
                    <span class="express-bill-field consignee-phone">18368029000</span>
                    <span class="express-bill-field consignee-address">浙江省温州市龙市龙华路16号</span>
                </div>
            </div>
        </div> 
        <!-- 结束渲染 -->
    </div>
</body>  
<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script> 
<script type="text/javascript">
    $(function(){  
        //前端样式名称要和后台快递简称相同
        var exp = parent.expressId; 
        var orderIds = parent.orderIds; 
        $('.express-bill-field-container').addClass('express-'+exp);   
        var csrf_token = $("meta[name='csrf-token']").attr("content"); 
        $.ajax({
            url:'/merchants/order/printExpressApi',// 跳转到 action
            data:{"express_id":exp,"orderIds":orderIds },
            type:'post', 
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'json',
            success:function (data) {
                if (data.status == 1){
                    var list = data.data;
                    var str ="";
                    for(var i=0;i<list.length;i++){
                        str+='<div class="print-paper"><div class="express-wrapper"><div class="express-bill-field-container">';
                        str+='<span class="express-bill-field shipper-name">'+list[i]["shipper-name"]+'</span>';
                        str+='<span class="express-bill-field shipper-city">'+list[i]["shipper-city"]+'</span>';
                        str+='<span class="express-bill-field shipper-phone">'+list[i]["shipper-phone"]+'</span>';
                        str+='<span class="express-bill-field shipper-time">'+list[i]["shipper-time"]+'</span>';
                        str+='<span class="express-bill-field shipper-company">'+list[i]["shipper-company"]+'</span>';
                        str+='<span class="express-bill-field shipper-address">'+list[i]["shipper-address"]+'</span>';
                        str+='<span class="express-bill-field consignee-name">'+list[i]["consignee-name"]+'</span>';
                        str+='<span class="express-bill-field consignee-city">'+list[i]["consignee-city"]+'</span>';
                        str+='<span class="express-bill-field consignee-phone">'+list[i]["consignee-phone"]+'</span>';
                        str+='<span class="express-bill-field consignee-address">'+list[i]["consignee-address"]+'</span>';
                        str+='</div></div></div>';  
                    }
                    $("#print-area").html(str);
                    $('.express-bill-field-container').addClass('express-'+exp);   
                    window.print();
                    setTimeout(function(){
                        parent.layer.closeAll();
                    },50); 
                }else{
                    parent.tipshow(data.info,"warm");
                    parent.layer.closeAll();
                }
            },
            error : function() { 
                parent.tipshow("请求异常","warm");
                parent.layer.closeAll();
            }
        }); 
    }); 
</script>
</html>