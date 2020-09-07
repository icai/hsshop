$(function(){
	//console.log("相亲，竟不可亲近。。")
	//全局定义总页数和当前页数
	var totalPage = 0, nowPage = 1, countPage = 0, pageSize = 0;
	var id = GetQueryString('mid');
//	var id = 17565;

	//点击佣金详情
	$("#income").click(function(){
		location.href = "/merchants/distribute/partnerIncome?mid="+id;
	})
	//点击合伙人脉
	$("#contacts").click(function(){
		location.href = "/merchants/distribute/partnerContacts?mid="+id;
	})

	//页面第一次加载
	getIncome('', 1, id, '', false)
	
	//点击列表展开二级列表;
	$(document).on("click", ".list_item .picName", function(){
		var parentEle = $(this).parents("ul");
		var cid = parentEle.attr("data-id");
		var dataShow = !$(this).data("show");			//拿到data-show的值
		$(this).data("show",dataShow)					//改变data-show的值
		if($(this).data("show")){
			// if ($(this).hasClass("picName_1")) {			//点击的是一级列表
			// 	getIncome(1, 1, cid, parentEle, true)
			// }else if($(this).hasClass("picName_2")){		//点击的是二级列表
			// 	getIncome(1, 1, cid, parentEle, true, true)
			// }
			$(this).children("span").text("-");
		}else{
			parentEle.siblings(".childrenDiv_"+cid).remove()
			$(this).children("span").text("+");
		};
	})
	
	//点击首页
	$(".firstPage").click(function(){
		nowPage = 1;
		getIncome(1, nowPage, id, '', false)
	});
	//点击尾页
	$(".lastPage").click(function(){
		nowPage = totalPage;
		if(nowPage == 0){
			nowPage = 1;
		}
		getIncome(1, nowPage, id, '', false)
	})
	//点击上一页
	$(".prevPage").click(function(){
		if(nowPage > 1) {
			nowPage--;
			getIncome(1, nowPage, id, '', false)
		}
	})
	//点击下一页
	$(".nextPage").click(function(){
		if(nowPage < totalPage) {
			nowPage++;
			getIncome(1, nowPage, id, '', false)
		}
	});
	//点击查看更多
	var n = 0;
	$(document).on("click", ".look_more", function(){
//		n =1;
		var page = $(this).data("page");		//拿到page值
		$(this).data("page",page+1)
		var page_1 = $(this).data("page");		//更新page值
		
		var pid = $(this).data("pid");			//拿到父级的id值
		var level = $(this).data("level")=="undefined"?false:true;		//得到是那一级的查看更多
		var addEle = $(this);
//		page = page + n;
		console.log(page_1,pid,level)
		getIncome(1, page_1, pid, '', true, level, true, addEle)
//		$(this).remove()
	})
	
	//tag标识；page页数；id接口携带的id；parentEle展开子元素的父级；showChildren是否显示子元素；thirdChild是否是显示三级
	function getIncome(tag, page, id, parentEle, showChildren, thirdChild, lookMore, addEle){		
		$.get("/merchants/distribute/relationship/"+id,{
			tag  : tag,
			page : page,
		}, function(res){
			console.log(res)
			//只有当tag的值为空的时候加载会员信息；后面切换分页则tag=1；
			if(tag != 1 && !showChildren){
				$("#pageInfo span").html("");
				//会员信息
				var member = res.data.member;
				var count = res.data.count;
				var pData = res.data.pData;
				$("#form_1 div:eq(0) span").text(member.nickname);
				$("#form_1 div:eq(1) span").text(member.mobile);
				$("#form_1 div:eq(2) span").text(member.created_at);

				$("#form_3 div:eq(0) span").text(count.one+"人");
				// $("#form_2 div:eq(1) span").text(count.two+"人");
				// $("#form_2 div:eq(2) span").text(count.thr+"人");

				// $("#form_3 div:eq(1) span").text(pData.id);
				$("#form_3 div:eq(1) span").text(pData.nickname);
				$("#form_3 div:eq(2) span").text(pData.mobile);

				
				//页数信息(tag=1的时候获取数据，为空的时候计算页码)
				var pageInfo = res.data.pageInfo;
				$("#pageInfo span").prepend('总条数：'+pageInfo.count+' &nbsp;&nbsp; 当前页码'+pageInfo.pageNow+'/'+pageInfo.pageNum);
				//赋值总页数和当前页
				totalPage = pageInfo.pageNum;		//数据总页数
				countPage = pageInfo.count;			//数据总条数
				pageSize  = pageInfo.pageSize;		//每页数据条数
			}else if(!showChildren){
				$("#pageInfo span").html("");
				$("#pageInfo span").prepend('总条数：'+countPage+' &nbsp;&nbsp; 当前页码'+nowPage+'/'+totalPage);
			}
			
			//佣金列表
			var datas = res.data.list;
			console.log(datas)
            var tab = new Array();
            tab[0] = "公众号";
            tab[1] = "未知";
            tab[2] = "其他";
            tab[3] = "分享";
            tab[4] = "导入";
            tab[5] = "录入";
            tab[6] = "小程序";
			if(!showChildren){
				//每次加载之前先清空(不是展开下级的情况)
				$(".member_list .list_div").html("")
				//$("#pageInfo span").html("");
				for(var i=0; i<datas.length; i++){
					// var level = "一级代理"

					var buy_nun = '';
					if (datas[i].buy_num>0){
                        buy_nun = '<a href="/merchants/order/orderList?mid='+datas[i].id+'">'+datas[i].buy_num+'次</a>'
					}else{
                        buy_nun =  datas[i].buy_num+'次';
					}


					var list_item = '<ul class="list_item list_body" data-id='+datas[i].id+'>';
							list_item +='<li class="picName picName_1" data-show=false><img class="level_1" src="'+datas[i].headimgurl+'" height="30" width="30"/>'+datas[i].truename+'</li>';
							list_item +='<li>'+buy_nun+'</li>';
							list_item +='<li>'+datas[i].mobile+'</li>';
							list_item +='<li>'+tab[datas[i].source]+'</li>';
						list_item +='</ul>';
					$(".member_list .list_div").append(list_item);
					//若有下级则显示前面的+号；否则不显示
					// if (0 > 0) {
					// 	$(".list_item[data-id='"+datas[i].id+"']").children(".picName_1").prepend('<span class="level_1">+</span>')
					// }else{
					// 	$(".list_item[data-id='"+datas[i].id+"']").children(".picName_1").css("padding-left","40px")
					// }
				}
			}else{
				//创建一个容纳下级的容器
				if(!lookMore){
					$(parentEle).after("<div class='childrenDiv childrenDiv_"+id+"'></div>");
				}
				
				for(var i=0; i<datas.length; i++){
					console.log(tab[0]);
					if (!thirdChild) {
						// var level = "二级代理"
						var list_item = '<ul class="list_item list_body" data-id='+datas[i].id+'>';
								list_item +='<li class="picName picName_2" data-show=false><img src="'+datas[i].headimgurl+'" height="30" width="30"/>'+datas[i].truename+'</li>';
								list_item +='<li>'+datas[i].number+'个直系下级</li>';
								list_item +='<li>'+datas[i].mobile+'</li>';
								list_item +='<li>'+tab[datas[i].source]+'</li>';
							list_item +='</ul>';
						if(!lookMore){
							$(parentEle).siblings(".childrenDiv_"+id).append(list_item);
						}else{
							addEle.before(list_item)
						}
						//若有下级则显示前面的+号；否则不显示
						if (datas[i].number > 0) {
							$(".list_item[data-id='"+datas[i].id+"']").children(".picName_2").prepend('<span class="level_2">+</span>')
						}else{
							$(".list_item[data-id='"+datas[i].id+"']").children(".picName_2").css("padding-left","70px")
						}
					}else{
						// var level = "三级代理"
						var list_item = '<ul class="list_item list_body" data-id='+datas[i].id+'>';
								list_item +='<li class="picName picName_3"><img src="'+datas[i].headimgurl+'" height="30" width="30"/>'+datas[i].truename+'</li>';
								list_item +='<li>'+datas[i].number+'个直系下级</li>';
								list_item +='<li>'+datas[i].mobile+'</li>';
								list_item +='<li>'+tab[datas[i].source]+'</li>';
							list_item +='</ul>';
						if(!lookMore){
							$(parentEle).siblings(".childrenDiv_"+id).append(list_item)
						}else{
							addEle.before(list_item)
						}
						$(".list_item[data-id='"+datas[i].id+"']").children(".picName_3").css("padding-left","100px")
					}
				}
				//信息条数未显示完显示查看更多
				if (datas.length == pageSize) {
					$(parentEle).siblings(".childrenDiv_"+id).append('<ul class="list_item look_more" data-page="1" data-pid="'+id+'" data-level="'+thirdChild+'">查看更多</ul>');
				}else if(addEle){
					addEle.text("无更多数据")
				}
			}
		});
	};
})

function GetQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}
