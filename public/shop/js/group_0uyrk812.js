$(function(){   
	dropRefresh.init({
		url:"/shop/grouppurchase/index?page=",
		callBack:function(json){
			if(json.status==1){
				var str ="";
				data = json.data.data;
				for(var i=0;i<data.length;i++){
					var price = data[i].max?data[i].min+"~"+data[i].max:data[i].min;  //价格区间
					str +='<a class="block name-card-vertical" href="/shop/grouppurchase/detail/'+data[i].id+'">';
					str +='<img class="thumb" src="'+imgUrl+data[i].product.img+'" />';
					str +='<div class="detail"><h3 class="goods-name">'+data[i].title+'</h3>';
					str +='<div class="groupon-info"><span class="join-num">'+data[i].num+'人团</span>';
					str +='<span>¥</span><span class="price">'+price+'<span>／件</span></span>'; 
					str +='<span class="join-text pull-right">去开团</span></div></div></a>';
				}
				$("#product_list").append(str);
			}else if(json.status==10){
				$("#product_list").append("<div style='text-align:center;padding-bottom:10px;'>没有更多数据了</div>");
			}		 		
		}
	}); 

	function imgLoad(img,callback){
		var timer =setInterval(function(){
			if(img.complete){
				callback(img);
				clearInterval(timer);
			}
		},50);
	}
	$(".img-outner img").each(function(){
		imgLoad(this,callback)
	});

	function callback(img){
		var h = $(img).height();
		var w = $(img).width();    
		if(h>200){
			var mt = - (h-200)/2;
			$(img).css("margin-top",mt+"px"); 
			$(img).css("visibility","visible"); 
		}else{
            $(img).css("visibility","visible");
       }
	}
});

/*
* 下拉刷新组件 @author txw @date 2017-07-11
* 依赖jQuery 或 zepto
*/
(function(win,$){
	var dropRefresh = {};
	dropRefresh.is_data = true; //下拉是否有数据
	dropRefresh.is_success = false;
	dropRefresh.page = 2; //当前页面
	dropRefresh.init =function(obj){
		$(window).scroll(function(){ 
		    var top = $(this).scrollTop();    //滚动条距离顶部的高度
		    var sheight = $(document).height();   //当前页面的总高度
		    var cheight = $(this).height();    //当前可视的页面高度
		    if(top + cheight >= sheight && dropRefresh.is_data){   
		       	//距离顶部+当前高度 >=文档总高度 即代表滑动到底部 count++;
				$.ajax({
                        type:"get",
                        url:obj.url+dropRefresh.page,
                        data:{},
                    	async: false,
                        dataType:"json",
                        success:function(json){
                            if(json.status==1){
                                dropRefresh.page++;
                                if(json.data.data.length==0){
                                    json.status = 10; //没数据了
                                    json.info ="没数据了";
                                    dropRefresh.is_data=false;
                                }
                                obj.callBack(json);
                            }
                        },
                        error:function(){
                            console.log("异常");
                        }
                    });

		    }
		});	
	}
	win.dropRefresh = dropRefresh;
})(window,$)