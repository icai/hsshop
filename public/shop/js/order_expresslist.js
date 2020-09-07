$(function(){
    //包裹导航点击事件
    $("body").on("click",".listwrapper li",function(e){
        $(this).addClass("active").siblings().removeClass("active");
        var index = $(this).index();
        $(".express-container").eq(index).removeClass("hide").siblings().addClass("hide");
    }); 
    getLogistics();
});
//获取物流信息
function getLogistics(){
    var id =$("#order_id").val();
//  var wid = 42;
    $.ajax({
        url:'/shop/order/getLogistics/'+wid+'/'+id,// 跳转到 action
        data:'', 
        type:'get', 
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:'json',
        success:function (data) {
            console.log(data);
            if (data.status == 1){
                var json = data.data,top_str='',shop_str='',exp_str='',str='',tip_str='';
                //导航渲染
                for(var i=0;i<json.length;i++){
                    var state = "";//状态
                    switch (json[i].state)
                    {
                        case "0":
                            state = "在途";
                        break;
                        case "1":
                            state = "揽件";
                        break;
                        case "2":
                            state = "疑难";
                        break;
                        case "3":
                            state = "签收";
                        break;
                        case "4":
                            state = "退签";
                        break;
                        case "5":
                            state = "派件";
                        break;
                        case "5":
                            state = "退回";
                        break;
                    }
                    if(i==0){
                        top_str +='<li data-type="package1" class="active" style="cursor:pointer;">包裹'+(i+1)+'</li>'; 
                    }else{
                        top_str +='<li data-type="package1" style="cursor:pointer;">包裹'+(i+1)+'</li>'; 
                    }
                    if(json.length == 1){
                        $(".package-list").hide()
                    }else{
                        tip_str = '<span class="more-tip">商品被拆成多个包裹</span>';
                    }
                    //商品和物流渲染
                    shop_str ='<div class="imagewrapper mg_btm_10"><div class="innerwrapper" style="width:'+json[i].img.length*80+'px;">'
                    console.log(json[i].img.length)
                    for(var k = 0;k < json[i].img.length;k ++){
                        shop_str +='<span><img src="'+imgUrl+json[i]["img"][k]["img"]+'" alt=""></span>'
                    }
                    shop_str +=`</div><div class="express-info"><p>共${json[i].sum}件商品，由【${json[i].com}】承运</p><p>运单编号：<span id="express_nu" class="text-primary">${json[i].nu?json[i].nu:'无需物流'}</span></p><p><span>物流状态：</span><span class="text-primary">${state}</span></p></div></div>`;

                    var exp_list = json[i].data;
                    exp_str ='<div class="express-detail"><ul><div class="leftline"></div>';
                    if(exp_list.length == 0){
                        exp_str+='<div class="leftline"></div>'
                        // exp_str+='<img src="'+imgUrl+'shop/images/current-icon.png" alt="" class="ing">'
                        exp_str+=`<li class="first"><span class="current"></span><span class="desc text-primary">暂无物流信息</span><div class="time">${json[i].created_at}</div></li>`;
                    }
                    for(var j=0;j<exp_list.length;j++){
                        if(j==0) 
                            exp_str+='<li><span class="current"></span><span class="desc text-primary">'+exp_list[j].context+'</span><div class="time">'+exp_list[j].time+'</div></li>';
                        else
                            exp_str+='<li class=""><span class="plain"></span><span class="desc">'+exp_list[j].context+'</span><div class="time">'+exp_list[j].time+'</div></li>'; 
                    } 
                    exp_str+='</ul></div>';
                    if(i==0)
                        str ='<div class="express-container ">'+shop_str+exp_str+'</div>';
                    else
                        str ='<div class="express-container hide">'+shop_str+exp_str+'</div>';
                    $("#express_content").append(str);
                }
                $(".top-tip").append(tip_str);
                $(".listwrapper").html(top_str);
                // $(".listwrapper").css("width",0*json.length+"px"); 
            }else{
                tool.tip(data.info);
            }
        },
        error : function() {
            alert("异常！"); 
        }
    });
}