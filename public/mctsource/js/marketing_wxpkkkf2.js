$(function(){
	//全局定义总页数和当前页数
	var totalPage = 0, nowPage = 1,sort='created_at-desc';
	//进入页面获取数据
	getMemberInfo('', '', '' ,1,sort)
	//点击筛选, 点击首页
	$("#search, .firstPage").click(function(){
		nowPage = 1;
		pageSeach(nowPage);
	});
	$(document).keydown(function (e) {
	    if (e.keyCode==13) {
	    	nowPage = 1;
			pageSeach(nowPage);
	    }
	});
	//点击尾页
	$(".lastPage").click(function(){
		nowPage = totalPage;
		pageSeach(nowPage);
	})
	//点击上一页
	$(".prevPage").click(function(){
		if(nowPage > 1) {
			nowPage--;
			pageSeach(nowPage);
		}
	})
	//点击下一页
	$(".nextPage").click(function(){
		if(nowPage < totalPage) {
			nowPage++;
			pageSeach(nowPage);
		}
	});
	
	//分页数据
	function pageSeach(page){
		var name = $("#nickName").val();
		var phone = $("#phoneNum").val();
		var source = $("#orderSource").val();
 		getMemberInfo(name, phone, source,page,sort);
	}
	
	//清空筛选
	$("#clearJudge").click(function(){
		$("#nickName, #phoneNum").val("")
	})
	//点击收入
	$(document).on("click", ".income", function(){
		var id = $(this).attr("data-id")
		window.open("/merchants/distribute/partnerIncome?mid="+id);
	});
	//点击人脉
	$(document).on("click", ".contacts", function(){
		var id = $(this).attr("data-id")
		window.open("/merchants/distribute/partnerContacts?mid="+id) ;
	})
	//点击清退 add by 黄新琴 2018/10/9
	var clearId = 0;
	$(document).on("click", ".backward", function(){
		$('.js-popup').show();
		clearId = $(this).attr("data-id");
	})
	$('.js-close-wraper,.js-cancle-btn').click(function(){
		$('.js-popup').hide();
		$('.js-reason').val('');
	});
	$('.js-sure-btn').click(function(){
		var reason = $('.js-reason').val();
		if (reason!=''){
			$.get('/merchants/distribute/purge/'+clearId,{reason},function(res){
				if (res.status == 1){
					tipshow(res.info);
					window.location.reload();
				} else {
					tipshow(res.info,'warn');
				}
			})
		} else {
			tipshow('请输入清退理由', 'warn');
		}
		
	})
	//点击空白处隐藏弹出层
	$('body').click(function(event){
		var _con = $('.popup-wraper');   // 设置目标区域
		if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
			$(".js-popup").hide();
			$('.js-reason').val('');
		}
	});
	
	// 排序
	$('.J_sort').click(function(){
		var type = $(this).data('type'),
			sortType = +$(this).data('sort');
		switch (sortType){
			case 1:
				sort = type + '-desc';
				$(this).data('sort',2).addClass('active1').removeClass('active2').siblings().removeClass('active2 active1');
				break;
			case 2:
				sort = type + '-asc';
				$(this).data('sort',1).addClass('active2').removeClass('active1').siblings().removeClass('active2 active1');
				break;
		}
		pageSeach(nowPage);
	});



	function getMemberInfo(nick_name, mobile, source,page,sort){
		$.get("/merchants/distribute/getMember",{
			nickname: nick_name,
			mobile  : mobile,
			source  : source,
			page    : page,
			sort    : sort,
		}, function(res){
			//会员信息
			var members = res.data[0].data;
			//页数信息
			var pageInfo =res.data[0];
			//每次加载之前先清空
			$(".member_list .list_div").html("")
			$("#pageInfo span").html("");
			var source = '';
			for(var i=0; i<members.length; i++){
				if(members[i].source == 1){
					source = '未知';
				}else if(members[i].source == 2){
					source = '微商城';
				}else if(members[i].source == 3){
					source = '分享';
				}else if(members[i].source == 4){
					source = '导入';
				}else if(members[i].source == 5){
					source = '录入';
				}else if(members[i].source == 6){
					source = '小程序';
				}else if(members[i].source == 0){
                    source = '微商城';
				}
				var member_item = '<ul class="list_item list_body">';
						member_item +='<li>'+members[i].nickname+'</li>';
						member_item +='<li>'+members[i].mobile+'</li>';
						member_item +='<li>'+source+'</li>';
						member_item +='<li>'+members[i].distribute_grade+'</li>';
						member_item +='<li>'+members[i].cash+'</li>';
						member_item +='<li class="color-blue">'+members[i].total_cash+'</li>';
						member_item +='<li class="color-blue">'+members[i].son_num+'</li>';
						member_item +='<li class="color-blue">'+members[i].trade_amount+'</li>';
						member_item +='<li class="color-blue">'+members[i].created_at+'</li>';
						member_item +='<li>';
							member_item +='<span class="income" data-id='+members[i].id+'>查看详情-</span>';
							member_item +='<span class="item-operate" data-level="'+members[i].distribute_top_level+'" data-id='+members[i].id+'>操作</span>';
							// member_item +='<span class="income" data-id='+members[i].id+'>收入-</span>';
							// member_item +='<span class="contacts" data-id='+members[i].id+'>人脉</span>';
							// if (distribute_grade == 1) {
							// 	member_item +='<span class="backward" data-id='+members[i].id+'>-清退</span><br/>';
							// } else {
							// 	member_item += '<br/>';
							// }
							
							// member_item +='<span class="sel-goods zx_li_sp1" data-id='+members[i].id+'><i class="icon-add"></i>添加下级</span>';
							// member_item +='<span class="zx_li_sp2 give_commission" data-id='+members[i].id+'>添加佣金</span>';
						member_item +='</li>';
					member_item +='</ul>';
				$(".member_list .list_div").append(member_item)
			}
			$("#pageInfo span").prepend('总条数：'+pageInfo.total+' &nbsp;&nbsp; 当前页码'+pageInfo.current_page+'/'+pageInfo.last_page);
			//赋值总页数和当前页
			totalPage = pageInfo.last_page;
		});
	};
	
	// 操作
	$("body").on('click','.item-operate',function(e){
		e.stopPropagation();
		var id = $(this).data('id');
		var level = $(this).data('level');
		var top = $(this).offset().top;
		var left = $(this).offset().left;
		$(".operate-popup").css({"top":top-35,"left":left-240});
		$(".operate-popup").show();
		$(".sel-goods").attr('data-id',id);
		$(".give_commission").attr('data-id',id);
		$(".backward").attr('data-id',id);
		$(".distribution-level").attr('data-id',id);
		$(".set_top_level").attr('data-level',level);
		$(".set_top_level").attr('data-id',id);
		if(level == 0){
			$(".set_top_level span").text('设为顶级');
		}else{
			$(".set_top_level span").text('取消顶级')
		}
	})
	//点击空白处隐藏弹出层
    $('body').click(function(event){
        var _con = $('.operate-popup');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
            $(".operate-popup").hide();
        }
    });
	//添加子分销商
	var mid;
    $("body").on("click",".sel-goods",function(e){ 
		mid = $(this).data('id');
        // if($(this).find(".icon-add").length>0){
            e.preventDefault();
            var href = "/merchants/distribute/getDistributeMember"; 
            distribute.open({success:callback,href:href,is_multiple:1}); 
        // } 
    });

    function callback(json){
        var _json = json;
        var ids = [];
        for(var i = 0 ; i<_json.length; i++){
        	ids.push(_json[i].id);
        }
        $(".sel-goods").attr("href",json[0].url);
        $(".sel-goods").attr("target","_blank"); 
        $("#title").val(json[0].title);
        $("#goods_id").val(json[0].id);
        $.ajax({
            type:"POST",
            url: "/merchants/distribute/addJunior",
            data:{
            	mid:mid,
            	ids:ids
            },
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
            success:function(res){
                if(res.status == 1){  
                	tipshow(res.info)
                }else{
                	tipshow(res.info,'warm')
                }
            }
        });
    }

    //添加佣金
    $("body").on("click",".give_commission",function(e){
        $(".t-pop").remove();
        e.stopPropagation();//阻止事件冒泡 
        var id = $(this).data("id");
        var div = document.createElement("div");
        div.className ="t-pop";
        div.style.top =$(this).offset().top-10+"px";
        div.style.left=$(this).offset().left-284+"px";
        var html ='<div class="t-pop-header">添加佣金</div>';
        html += '<div class="t-pop-content"><input type="text"class="form-control input-sm" id="input_integral" value="" placeholder="添加佣金"/></div>';
        html += '<div class="t-pop-footer"><button class="btn btn-primary t-pop-footer-yes btn-xs" data-id="'+id+'" data-type="2">确定<tton>';
        html += '<button class="btn btn-default btn-xs t-pop-footer-clear">取消<tton></div>'
        div.innerHTML=html;
		$("body").append(div);
		console.log(11111)
    });
    //是否为数字
    function isNum(value) { 
        var patrn = /^(-)?\d+(\.\d+)?$/;
        if (patrn.exec(value) == null || value == "") {
            return false
        } else {
            return true
        }
    }
    
    //添加佣金弹窗点击弹窗本身阻止事件冒泡 
    $("body").on("click",".t-pop",function(e){
        e.stopPropagation();
    });
    //添加佣金点击取消按钮移除弹窗
    $("body").on("click",".t-pop-footer-clear",function(e){
        $(".t-pop").remove();
    });
    $("body").on("click",".t-pop-footer-clear1",function(e){
        $(".t-pop").remove();
    });
    //添加佣金弹窗点击body 移除弹窗
    $("body").click(function(e){
        $(".t-pop").remove();
    });
    //点击确定事件
    $("body").on("click",".t-pop-footer-yes",function(){ 
        var id = $(this).attr("data-id");
        var score = $("#input_integral").val();
        var msg = $("#msg").val();
        if(score < 0){
            tipshow('添加金额必须大于0','warn');
            return;
        }
        function AddEventInput(i){
				if(isNum(score)){
		            $.ajax({
		                url:"/merchants/distribute/addCash",
		                data:{
		                	id:id,
		                	cash:score
		                },
		                type:"POST",		                
		                headers: {
		                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
		                },
		                success:function(res){
		                    if(res.status == 1){
		                        tipshow(res.info);
		                        setTimeout(function(){
		                        	location.href = "/merchants/distribute/partnerIncome?mid="+id;		                        	
		                        },500)
		                    }else{
		                       tipshow(res.info,"wram"); 
		                    }
		                },
		                error:function(){
		                    tipshow("异常","wram");
		                }
		            }); 
		            $(".t-pop").remove();
		        }else{
		            tipshow("请输入正确的佣金","wran");
		        }
//	        };
		}
		AddEventInput(score);
	});
	$('.distribution-level').on('click',function(){
		$('.grade_wraper').show();
		$('.grade').show();
	})
	//分销等级设置
	$("body").on("click",".distribution-level",function(e){
		var that = this
		$.ajax({
			url:"/merchants/distribute/getDistributeGrade",
			type:"GET",		                
			headers: {
				'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
			},
			success:function(res){
				var levlist = res.data
					 $(".t-pop").remove();
					 e.stopPropagation();//阻止事件冒泡 
					 var id = $(that).data("id");
					 var div = document.createElement("div");
					 div.className ="t-pop";
					 div.style.top =$(that).offset().top - 10+"px";
					 div.style.left=$(that).offset().left - 150+"px";
					 var html ='<div class="t-dj-header">设置分销等级</div>';
					 html += '<div class="t-dj-content"><select name="" id="menu" class="form-control">'
					 	for(var i=0;i<levlist.length;i++){
							html+='<option value="'+levlist[i].id+'">'+levlist[i].title+'</option>'
						 }
					 html += '</select></div><div class="t-dj-footer"><button class="btn btn-primary  btn-xs js-submit" >确定<tton>';
					 html += '<button class="btn btn-default btn-xs t-pop-footer-clear" >取消<button></div>'
					 div.innerHTML=html;
					 $("body").append(div);
					 $('.js-submit').on('click',function(){
						var gid= $('#menu').val()
						var id = $(that).data('id');
						$.ajax({
							url:"/merchants/distribute/setMemberDistributeGrade",
							type:"post",
							data:{
								mids:id,
								gid:gid,
							},	                
							headers: {
								'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
							},
							success:function(res){
								if(res.status==1){
								tipshow(res.info,"info"); 
								$('.t-pop').hide();
								$('.operate-popup').hide();
								}
							}
						})
					 })
				
			},
			error:function(){
				tipshow("异常","wram");
			}
		}); 
	});
	// 设置顶级
	$(".set_top_level").click(function(){
		var that = this;
		var level = $(this).attr('data-level');

		$.ajax({
			url:'/merchants/distribute/setDistributeTopLevel',
			type:'post',
			data:{
				type:level==0? 1 : 0,
				mid:$(this).attr('data-id')
			},
			headers: {
				'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
			},
			success:function(res){
				console.log(res)
				if(res.status == 1){
					tipshow(res.info,"info"); 
					// $(that).attr('data-level',level==0?1:0)
					$(that).find('span').text(level==0?'取消顶级':'设为顶级')
					setTimeout(function(){
						window.location.reload()
					},1000)
					
					
				}
			}
		})
	})
	$(".set_top_level .glyphicon").hover(function(e){
		e.stopPropagation(); //阻止事件冒泡
		$(".top_level_tip").show();
	},function(){
		$(".top_level_tip").hide();
	})

})
