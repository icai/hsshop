$(function(){
	/*小程序微页面请求*/
	listHttp({page:1});
	function listHttp(obj){
		var data={};
		if(obj.page){
			data.page = obj.page;
		}
		if(obj.title){
			data.title = obj.title;
		}
		$.get("/merchants/xcx/micropage/select",data,function(res){
			if(res.errCode == 0){
				$(".search_input").val("");
				$(".table .item").remove();
				for(var i = 0;i < res.data.length;i ++){

                    var html= '<tr data-id="'+res.data[i].id+'" class="item">'
                                if(res.data[i].is_home != 1){
                                    html += '<td><input type="checkbox" class="shop" value="' + res.data[i].id + '" ></td>';
                                } else {
                                    html += '<td><input disabled type="checkbox" class="shop" value="' + res.data[i].id + '" ></td>';
                                }
					           html+='<td>'+res.data[i].title+'</td>'
					           html+='<td>'+res.data[i].create_time+'</td>'
					            html+='<td class="opt_wrap">'
					                html+='<a class="copy_list" href="javascript:void(0);">'
					                    html+='<span class="blue_38f">复制</span>'
					                html+='</a>'
					                html+='<a href="/merchants/marketing/liteAddPage?id='+res.data[i].id+'">'
					                    html+='<span class="blue_38f">编辑</span>'
					                html+='</a>'
					                html+='<a class="set_homepage" href="javascript:void(0);">'
					                	if(res.data[i].is_home == 1){
					                  	html+='<span style="color:#000;">店铺主页</span>'
					                	}else{
					                  	html+='<span class="blue_38f">设为主页</span>'
					                	}
					                html+='</a>'
                    				html+='<a class="spread" data-id="'+res.data[i].id+'" data-title="'+res.data[i].title+'" data-groupNum="" data-price="" data-img1="" data-img2="" data-url="pages/micropage/index/index?id='+res.data[i].id+'" href="javascript:void(0);">推广</a>'
					            html+='</td>'
					        html+='</tr>'
					$(".table").append(html);
                    $('.get_xcxewm').off('click').on('click',function () {
                        var id = $(this).parents('.item').attr("data-id");
                        var imgSrc =  $("#img_xcxm").attr("src")
						if(imgSrc){
                            $("#img_xcxm").attr("src",'')
						}
                        hstool.load();
                        $(".xcx-mask").removeClass('hide');
                        $.ajax({
                            url:'/merchants/xcx/code',
                            type:'post',
                            data:{
                                "path":"pages/micropage/index/index?id="+id
                            },
                            dataType:'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success:function (res) {
                                if(res.errCode==0){
                                    $("#img_xcxm").attr("src",'data:image/png;base64,'+res.data);
                                    $("#down_xcxm").attr("href",$("#img_xcxm").attr("src")).attr("download","xcxm.png");
                                    var href = "pages/micropage/index/index?id="+id
                                    $("#path_xcxm").attr('data-url',href)
                                }else{
                                    tipshow(res.errMsg,'warn');
                                }
                                hstool.closeLoad();
                            },
                        });
                    })
				}
				//切换页码不执行此方法
				if(!obj.type){
					$('.page').extendPagination({
				        totalCount: res.total,//数据总数
                        // showCount: data.data[0].last_page,//展示页数
				        limit: res.pageSize,//每页展示条数
				        callback: function (curr, limit, totalCount) {
				            var page = $(this).text()//下标切换页码数
				            if(!parseInt(page)&& $(this).parent().index() == 0){
				                page =  $('.page .pagination .active').text();
				            }else if(!parseInt(page)&& $(this).parent().index() != 0){
				                page =  parseInt($('.page .pagination .active').text());
				            }else if($(this).parents('li').hasClass('disabled')){
				                return false;
				            }
				            listHttp({
				            	type: 1,
				            	page: page,
				            	title: obj.title?obj.title:''
				            });
				        }
				    });
				}
			}
		});
	}

	//小程序链接点击事件
	$("body").on("click","#path_xcxm",function(e){
		e.stopPropagation(); //阻止事件冒泡
        var _this = $(this);
        var _url = $(this).attr("data-url");             // 要复制的连接
        var html ='<div class="input-group" >';
        html +='<input type="text" class="link_copy form-control" value="'+_url+'" disabled >';
        html +='<a class="copy_btn input-group-addon">复制</a>';
        html +='</div>';
        showDelProver(_this,function(){},html,'false',2);             // 跟随效果 
        $(".del_popover").css("z-index",19891017);
	}); 
    // 复制链接
    $('body').on('click','.copy_btn',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('.link_copy');
        copyToClipboard( obj );
        tipshow('复制成功','info');
        $(this).parents('.del_popover').remove();
    });
     
	//获取小程序码
	function getCode(){
        var imgSrc = $("#img_xcxm").attr("src")
        if(imgSrc){
            $("#img_xcxm").attr("src",'')
        }
        var href = $("#path_xcxm").parent('dd').attr("data-url")
        $("#path_xcxm").attr("data-url",href)
		hstool.load();
		$.ajax({
			url:'/merchants/xcx/code',
			type:'post',
			data:{},
			dataType:'json',
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
	        success:function (res) {
	        	if(res.errCode==0){ 
	        		$("#img_xcxm").attr("src",'data:image/png;base64,'+res.data); 
	        		$("#down_xcxm").attr("href",$("#img_xcxm").attr("src")).attr("download","xcxm.png");
	        	}else{
	        		tipshow(res.errMsg,'warn');
	        	}
	        	hstool.closeLoad();
	        },
		});
	}
	//显示小程序码弹窗
	$(".btn-small-program").click(function(e){ 
		getCode();
		$(".xcx-mask").removeClass('hide');
	});   

	$(".xcx-mask").click(function(e){
		var _con = $('.xcx-wrap');   // 设置目标区域
	  	if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
		     $(".xcx-mask").addClass('hide');
	  	}
	});
    $(".xcx-wrap-close").click(function(e){
        $(".xcx-mask").addClass('hide');
    });
	//搜索列表
	$(".search_input").keypress(function(event) {
		if(event.keyCode == 13){
			listHttp({
            	page: 1,
            	title: $(".search_input").val()
            });
		}
	});
	// 复制链接跟随效果
    $('body').on('click','.link_btn',function(e){
        e.stopPropagation();//组织事件冒泡
        var _this = $(this);
        var _url = $(this).data('url');             // 要复制的连接
        var html ='<div class="input-group">';
        html +='<input type="text" class="link_copy form-control" value="'+_url+'" disabled >';
        html +='<a class="copy_btn input-group-addon">复制</a>';
        html +='</div>';
        showDelProver(_this,function(){},html,'false');             // 跟随效果
    });
    // 复制链接
    $('body').on('click','.copy_btn',function(e){
        e.stopPropagation();//组织事件冒泡
        var obj = $(this).siblings('.link_copy');
        copyToClipboard( obj );
        tipshow('复制成功','info');
        $(this).parents('.del_popover').remove();
    });
    $('#all_shop').click(function(){
        if (this.checked){
            $('.shop').not(':disabled').prop('checked',true);
        } else {
            $('.shop').prop('checked',false);
        }
    })
    $('body').on('click','.shop',function(){
        if (this.checked) {
            if ($('.shop').not(':disabled').length == $('.shop:checked').length){
                $('#all_shop').prop('checked',true);
            } else {
                $('#all_shop').prop('checked',false);
            }
        } else {
            $('#all_shop').prop('checked',false);
        }
    })
    // 删除列表
    $('.del_list').on('click',function(e){
        e.stopPropagation();
        var _this = this;
        var checkbox = $('.shop:checked');
        if (checkbox.length>0){
            var ids = [];
            for (var i=0;i<checkbox.length;i++){
                ids.push(+checkbox[i].value);
            }
            showDelProver($(_this),function(){
                $.ajax({
                	type:"post",
                	url:'/merchants/xcx/micropage/batchDelete',
                	data:{ids:ids,_token:$('meta[name="csrf-token"]').attr('content')},
                	dataType:'json',
                	success: function(msg){
                		if(msg.errCode==0){
                            tipshow('删除成功！');
                            var page = $('.pagination li.active a').data('page');
                			listHttp({
				            	type: 1,
				            	page: page,
				            	title: $(".search_input").val()
				            });
                		}else{
                            tipshow('删除失败！','warn');
                        }
                	},
                	error:function(msg){
                		tipshow('删除失败！','warn');
                	}
                });
            },'你确定要删除吗？',true,2,9,-6)
        } else {
            tipshow('请先选择要删除的数据','warn');
        }
        
    });
    //设为主页
    $("body").on("click",'.set_homepage .blue_38f',function(){
    	var id = $(this).parents("tr").data("id");
    	$.ajax({
			type:"post",
			url:'/merchants/xcx/micropage/updateHome',
			data:{id:id,_token:$('meta[name="csrf-token"]').attr('content')},
			dataType:'json',
			success: function(msg){
				if(msg.errCode==0){
					tipshow('设置主页成功！');
					listHttp({page:1});
				}else{
                    tipshow('设置主页失败！','warn');
                }
			},
			error:function(msg){
				tipshow('设置主页失败！','warn');
			}
		});	
    });
    //复制小程序微页面
    $("body").on("click",".copy_list",function(){
    	var id = $(this).parents("tr").data("id");
    	$.ajax({
			type:"post",
			url:'/merchants/xcx/micropage/copy',
			data:{id:id,_token:$('meta[name="csrf-token"]').attr('content')},
			dataType:'json',
			success: function(msg){
				if(msg.errCode==0){
					tipshow('复制成功！');
					listHttp({page:1});
				}else{
                    tipshow('复制失败！','warn');
                }
			},
			error:function(msg){
				tipshow('复制失败！','warn');
			}
		});	
    });




    /**
     * 微页面推广
     * @author 张永辉 2018年6月28日
     */
     $("body").on("click",".spread",function(e){
       $('.spread-popup').remove(); //关闭前面打开的弹窗
        // e.stopPropagation();
        var obj={};
        obj.path1 = $(this).attr("data-img1");
        obj.url = $(this).attr("data-url");
        obj.title = $(this).attr("data-title");
        obj.left = $(this).position().left;
        obj.path2 = '';
        var id = $(this).data('id');
        $.ajaxSettings.async = false;
        $.get("/merchants/shareEvent/getMinAppQRCode?from=litePage&id="+id, function(result){
            if (result.status == 1){
                obj.path2 = result.data;
            }
        });


        var html = getSpreadPopup(obj);
        $(this).parent().append(html);
    });
    $("body").on("click",".copy_url",function(){
        var text = $(this).attr("data-url");
        var obj ={
            val:text,
            text:function(){
                return this.val;
            }
        }
        copyToClipboard(obj);
        tipshow("复制成功");
    });
    //推广弹窗点击元素以外的地方关闭
    $("body").click(function(e){
        var el = $('.spread-popup');   // 设置目标区域
        var el1 = $(".spread-popup").parent().find(".spread"); //触发按钮
        if((!el.is(e.target) && el.has(e.target).length === 0) && !el1.is(e.target)){
            el.remove();
        }
    });

    //推广弹窗
    function getSpreadPopup(obj){
        var left = obj.left -250;
        var html = '<div class="spread-popup" style="left:'+left+'px;">';
        html+='<div class="spread-popup-content">';
        html+='<div class="spread-popup-img">';
        html+='<div class="spread-popup-sub-content">';
        html+='<p>'+obj.title+'</p>';
        if (obj.path2){
            html+='   <div class="qrcode"><img src="data:image/png;base64,'+obj.path2+'" /></div>';
        }
        html+='<div class="text-center mb-10"><a data-url="'+obj.url+'" class="copy_url" href="javascript:;">复制活动链接</a></div>';
        html+='</div>';
        return html;
    }
	/*
	 * add by 韩瑜 
	 * date 2018-9-20
	 * 新建微页面点击
	 */
    $('#add_page').click(function(){
        $.get("/merchants/store/getTemplateMarket?type=2&source=1",function(res){
            var data = res.data;
            var html = "";
            if(data.length != 0){
                for(var i = 0; i < data.length; i++){
                    html += '<li><div class="img-wrap template-state-2">';
                    html += '<img class="template-screenshot" src="' + SOURCE_URL + data[i].screenshot + '">';
                    html += '<div class="template-cover"><div class="template-action-container">';
                    html += '<a href="' + data[i].url + '" class="zent-btn zent-btn-success js-select-template" style="width: 88px;">' + "使用模板" + '</a>';
                    html += '</div></div></div><p class="template-title">';    
                    html += '<span>' + data[i].title + '</span></p></li>';
                }
            }
            $(".widget-feature-template-list").empty().append(html);
            setTimeout(function(){
                $('.widget-feature-template').show();
                $('.modal-backdrop').show();
            },200)
        })
    })
	// 微页面模板弹窗关闭点击
    $('.close').click(function(){
        $('.widget-feature-template').hide();
        $('.modal-backdrop').hide();
    })
    //end
    function getTemplateData(templateType){
        $.get("/merchants/store/getTemplateMarket?type=2&template_type="+templateType,function(res){
            var data = res.data;
            var html = "";
            if(data.length != 0){
                for(var i = 0; i < data.length; i++){
                    html += '<li><div class="img-wrap template-state-2">';
                    html += '<img class="template-screenshot" src="' + SOURCE_URL + data[i].screenshot + '">';
                    html += '<div class="template-cover"><div class="template-action-container">';
                    html += '<a href="' + data[i].url + '" class="zent-btn zent-btn-success js-select-template" style="width: 88px;">' + "使用模板" + '</a>';
                    html += '</div></div></div><p class="template-title">';    
                    html += '<span>' + data[i].title + '</span></p></li>';
                } 
            }
            $(".widget-feature-template-list").html(html);
        })
    }
    $('.js-filter-wraper').on('click','.js-filter',function(){
        var type = $(this).data('type');
        $(this).parent().addClass('active').siblings('.active').removeClass('active');
        getTemplateData(type);
    });
});