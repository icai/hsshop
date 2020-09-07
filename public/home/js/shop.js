$(function(){
	//全局定义总页数和当前页数
	//页面加载
	// var industry_id = $('.type_list ul li').eq(0).attr("data-id")
	// var totalPage = 0, nowPage = 1;
	// //进入页面获取数据
	// getMemberInfo(1,1)
	// //点击筛选, 点击首页
	// $(".firstPage").click(function(){
	// 	nowPage = 1;
	// 	pageSeach(nowPage);
	// });
	// $(document).keydown(function (e) {
	//     if (e.keyCode==13) {
	//     	nowPage = 1;
	// 		pageSeach(nowPage);
	//     }
	// });
	// //点击尾页
	// $(".lastPage").click(function(){
	// 	nowPage = totalPage;
	// 	pageSeach(nowPage);
	// })
	// //点击上一页
	// $(".prevPage").click(function(){
	// 	console.log(11111)
	// 	if(nowPage > 1) {
	// 		nowPage--;
	// 		pageSeach(nowPage);
	// 	}
	// })
	// //点击下一页
	// $(".nextPage").click(function(){
	// 	if(nowPage < totalPage) {
	// 		nowPage++;
	// 		pageSeach(nowPage);
	// 	}
	// });
	
	// //分页数据
	// function pageSeach(page){
	// 	console.log(page)
	// 	industry_id = industry_id;
 	// 	getMemberInfo(industry_id,page)
	// }
	
	// function getMemberInfo(industry_id,page){	
	// 	$.ajax({
	// 		type:"post",
	// 		url:"/home/index/shopApi",
	// 		data:{
    // 			industry:industry_id,
    // 			page:page
    // 		},
    // 		headers: {
	//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	//         },
	//         success:function(res){
	// 			console.log(res)
	//         	if(res.status == 1){
	//         		$('.zi-ul2').html('');
	//         		$("#pageInfo span").html("");
	//         		if(res.data.caseList.data.length>0){
	// 		        	for(var i = 0;i < res.data.caseList.data.length;i++){
	// 		        		var _html ='<div class="zi-ol1" data-id="'+res.data.caseList.data[i].id+'"><div class="zi-imga">';
	// 		        		_html += '<a href="/home/index/caseDetails?id='+res.data.caseList.data[i].id+'" class="zi-op4">'
	// 		                _html += '<img class="lazy" width="226px" height="157px" src='+url+res.data.caseList.data[i].logo+'>';
	// 	                 	_html += '</a></div><div class="zi-olp clear">';
	// 	                 	if(res.data.caseList.data[i].code){
	// 	                 		_html += '<div class="z_code"><img class="lazy" width="133px" height="133px" src='+url+res.data.caseList.data[i].code+'></div>';		                 		
	// 	                 	}
	// 		                _html += '<p class="zi-op2">'+res.data.caseList.data[i].name+'</p>';  
	// 	                    _html += '<p class="zi-op3">'+res.data.caseList.data[i].intruduce+'</p>';
	// 	                    _html += '<a href="/home/index/caseDetails?id='+res.data.caseList.data[i].id+'" class="zi-op4">查看详情</a>';
	// 	                    _html += '</div></div>';
	// 	                    $('.zi-ul2').append(_html);
	// 		        	}	        			
	//         		}else{
	//         			var div = '<div class="zx_null">暂无数据</div>';	
	//         			$('.zi-ul2').html(div)
	//         		}
	//         		$("#pageInfo span").prepend('总条数：'+res.data.caseList.last_page+' &nbsp;&nbsp; 当前页码'+res.data.caseList.current_page+'/'+res.data.caseList.last_page);
	// 				//赋值总页数和当前页
	// 				totalPage = res.data.caseList.last_page;
	//         	}
	//         },
	//         error:function(){
	//         	alert(11)
	//         }
	// 	});
	// };
	
	// 案例类型切换
	$(".cases_nav ul li").click(function(){
		$(".cases_nav ul li").removeClass("active")
		$(this).addClass("active")
	})

	//案例分类切换
	$(".type_list ul li").click(function(){
		$(".type_list ul li").removeClass("active")
		$(this).addClass("active")
	})



	//鼠标滑过案例显示二维码
	$('.cases_list ul').on('mouseenter', 'li', function(){
		$(this).children('.code').addClass('code-show');
	}).on('mouseleave', 'li', function(){
		$(this).children('.code').removeClass('code-show');
	});
	
	//鼠标点击分页上样式
	$(".page-content div").click(function(){
		$(".page-content div").removeClass("active")
		$(".page ul li:not(.ellipsis)").removeClass("active")
		$(this).addClass("active")
	})
	$(".page ul li:not(.ellipsis)").click(function(){
		$(".page-content div").removeClass("active")
		$(".page ul li:not(.ellipsis)").removeClass("active")
		$(this).addClass("active")
	})















    
//  鼠标经过显示数据
    $('.z_claul_li').mouseover(function(){
    	$('.zi-ul2').html('');
		$("#pageInfo span").html("");
    	$('.z_claul_li').removeClass('anli_hover');
    	var id = $(this).data('id')
    	$(this).addClass('anli_hover');
    	industry_id = $(this).data('id');
    	$.ajax({
    		type:"POST",
    		url:"/home/index/shopApi",
    		data:{
    			industry:id
    		},
    		headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
	        success:function(res){
	        	if(res.status == 1){
	        		if(res.data.caseList.data.length>0){
			        	for(var i = 0;i < res.data.caseList.data.length;i++){
			        		var _html ='<div class="zi-ol1" data-id="'+res.data.caseList.data[i].id+'"><div class="zi-imga">';
			        		_html += '<a href="/home/index/caseDetails?id='+res.data.caseList.data[i].id+'" class="zi-op4">'
			                _html += '<img class="lazy" width="226px" height="157px" src='+url+res.data.caseList.data[i].logo+'>';
		                 	_html += '</a></div><div class="zi-olp clear">';
		                 	if(res.data.caseList.data[i].code){
		                 		_html += '<div class="z_code"><img class="lazy" width="133px" height="133px" src='+url+res.data.caseList.data[i].code+'></div>';		                 		
		                 	}
			                _html += '<p class="zi-op2">'+res.data.caseList.data[i].name+'</p>';  
		                    _html += '<p class="zi-op3">'+res.data.caseList.data[i].intruduce+'</p>';
		                    _html += '<a href="/home/index/caseDetails?id='+res.data.caseList.data[i].id+'" class="zi-op4">查看详情</a>';
		                    _html += '</div></div>';
		                    $('.zi-ul2').append(_html);
			        	}	        			
	        		}else{
	        			var div = '<div class="zx_null">暂无数据</div>';	
	        			$('.zi-ul2').html(div)
	        		}
	        		$("#pageInfo span").prepend('总条数：'+res.data.caseList.last_page+' &nbsp;&nbsp; 当前页码'+res.data.caseList.current_page+'/'+res.data.caseList.last_page);
					//赋值总页数和当前页
					totalPage = res.data.caseList.last_page;
					nowPage = 1;//每次切换标签清空page信息
	        	}
	        },
	        error:function(){
	        	alert('数据访问错误')
	        }
    	});
    })

    //显示二维码
    $('body').on('mouseenter','.zi-ol1',function(){
    	var id = $(this).data('id');
    	var that = $(this)
		that.find('.z_code').css('display','block');	
    });
    $('body').on('mouseleave','.zi-ol1',function(even){
    	$(this).find('.z_code').css('display','none');
    })
		//案例展示会搜云新零售系统分类增加下拉框
		$(".show-more").click(function(){
			$(".show-more").hide();
			$(".close-more").show()
			$(".more_case_box").show()
		})
		$(".close-more").click(function(){
			$(".close-more").hide();
			$(".show-more").show();
			$(".more_case_box").hide()
		})
});


