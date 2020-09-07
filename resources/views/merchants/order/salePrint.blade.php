<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title> 
     <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css" /> 
    <!-- 当前页面css --> 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/salePrint.blade.css" /> 
</head>
<body>
	<!--销售单-->
    <div id="print-area">  
    </div>

<script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>   
<script type="text/javascript">
       var orderIds = parent.orderIds;
      
        var csrf_token = $("meta[name='csrf-token']").attr("content"); 
        $.ajax({
            url:'/merchants/order/salePrintApi',// 跳转到 action
            data:{"orderIds":orderIds },
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
               	 	 var detail=list[i].detail
               	 	 console.log(detail)
                    str+='<div class="sale" style="page-break-after:always;">';
                    str+='<p>销售单</p><ul>';
                    str+='<li>录单日期：<span>'+list[i]["create_at"]+'</span></li>';
                    str+='<li>订单号：<span>'+list[i]["oid"]+'</span></li></ul>';
                    str+='<p>买家：<span>'+list[i]["truename"]+'</span></p>';
                    str+='<p>买家留言：<span>'+list[i]["buy_remark"]+'</span></p>';
                    str+='<p>商家备足：<span>'+list[i]["seller_remark"]+'</span></p>';
                    str+='<table border="1" cellspacing="" cellpadding="" bordercolor="#ccc">';
                    str+='<tr><td>商品编号</td><td>商品全名</td><td>单位</td><td>数量</td><td>规格</td><td>单价</td><td>金额</td></tr>';
                    for(var j=0;j<detail.length;j++){
                    	if(!detail[j]["buy_message"]){
                    		detail[j]["buy_message"]='';
                    	}
	                    str+='<tr><td>'+Number(j+1)+'</td>';		
						str+='<td style="width:30%">'+detail[j]["title"]+'</td>';
						str+='<td>'+detail[j]["jian"]+'</td>';		
						str+='<td>'+detail[j]["num"]+'</td>';

						var _specs = "";
						for (var k=0; k<detail[j]["spec"].length; k++){
                            _specs += detail[j]["spec"][k] +"<br />";
                        }

                        str+='<td>'+_specs+'</td>';
						str+='<td>'+detail[j]["price"] +'</td>';
						str+='<td>'+detail[j]["money"]+'</td>';
						str+='</tr>';

						console.log(detail[j]["spec"], "p[p[p[p")
                    }

					
					
					str+='<td colspan="8">总计 :<span style="margin-right:4%">'+list[i]["products_price"]+'</span> 运费：<span style="margin-right:4%">'+list[i]["freight_price"]+'</span>优惠：<span style="margin-right:4%">'+list[i]["coupon_price"]+'</span>实际支付：<span>'+list[i]["pay_price"]+'</span></td>';
					str+='</tr></table>';
					str+='<p>地址：<span>'+list[i]["address_detail"]+'</span></p>';
					str+='<ul><li>手机：<span>'+list[i]["address_phone"]+'</span></li></ul>';
					str+='</div>'; 
                 }
                    $("#print-area").html(str);
                    
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
    
    
</script>
<body>
</html>