var _token =document.getElementsByTagName('meta')[4].content;//token值
$('.menu_main').each(function(){
	$(this).children('.menu_content').height($(this).siblings('.menu_title').height());
})
var page_type = 2;//2代表自定义才但模块
//按钮启用
$('.handle_title .btn1 button').on('click',function(){
	if ($('.handle_title .btn1 button').hasClass('active')) {
		$('.handle_title .btn1 button').removeClass('active');
		
	} else{
		$('.handle_title .btn1 button').addClass('active');
	}
});
//拼团交互 拼团type13
$(".group").click(function(e){
	e.stopPropagation();
	menu_num = $(this).parents('.menu').index();
	ctl_linkTo = $(this).parents('.menu').find('.zx').index();
	if ($(this).parents('.menu').find('.second ul li').length == 0) {
		ctl_linkTo = -1;
	}
	var that = $(this);
	if(ctl_linkTo == -1){//一级菜单修改内容
        data = {
            'id': $('.menu').eq(menu_num).data('id'),
            'value': that.data('href'),
            'content':{
                'content_id':0,
                'content_title': "拼团",
                'type': 13
            }
        };
    }else{//二级菜单修改内容
        data = {
            'id': $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid'),
            'parent_id': $('.menu').eq(menu_num).data('id'),
            'value': that.data('href'),
            'content':{
                'content_id':0,
                'content_title':  "拼团",
                'type': 13
            }
        };
    }
    console.log(data)
	$.ajax({
        type : "get", 
        url : "/merchants/wechat/save_menu", //跨域请求的URL
        dataType : "json",
        data:data,
        //成功获取跨域服务器上的json数据后,会动态执行这个callback函数
        success : function(data){ 
            if(data.status == 1){
                setTimeout(function(){
                    window.location.reload();
                },500);
                tipshow(data.info,'info');   
            }else{
                tipshow(data.info,'warn');   
            }
            
        }
    });
});


//记录富文本内容要添加的link-to的位置
var menu_num;
var ctl_linkTo;
var customType = 0;//当前活动判断类型
//自定义链接系列弹起弹框
$(document).on('click','.custom',function(){
	customType = $(this).data("type");//当前活动判断值
	$(this).parent().siblings('.linkTo_cap').css({
		'display':'block',
		'top':30,
		'left': -130
	});
});
//自定义外链的模态框下的按钮事件
$('.linkTo_cap .btn-primary').click(function(){
	// var str = /^http:\/\//;
	var value = $(this).siblings('input').val();
	if(!value){
		tipshow("请填写外链信息","warn");
		return false;
	}
	//小程序默认配置
	var type = customType;
	switch(type){
		case 10://自定义外链
			var content_title = value;
		break;
	}
	menu_num = $(this).parents('.menu').index();
	ctl_linkTo = $(this).parents('.menu').find('.zx').index();
	if ($(this).parents('.menu').find('.second ul li').length == 0) {
		ctl_linkTo = -1;
	}
	if(ctl_linkTo == -1){//一级菜单修改内容
        data = {
            'id': $('.menu').eq(menu_num).data('id'),
            'value': value,
            'content':{
                'content_id':0,
                'content_title': content_title,
                'type': type
            }
        };
    }else{//二级菜单修改内容
        data = {
            'id': $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid'),
            'parent_id': $('.menu').eq(menu_num).data('id'),
            'value': value,
            'content':{
                'content_id':0,
                'content_title': content_title,
                'type': type
            }
        };
    }
    $.ajax({
        type : "get", 
        url : "/merchants/wechat/save_menu", //跨域请求的URL
        dataType : "json",
        data:data,
        //成功获取跨域服务器上的json数据后,会动态执行这个callback函数
        success : function(data){ 
            if(data.status == 1){
                setTimeout(function(){
                    window.location.reload();
                },500);
                tipshow(data.info,'info');   
            }else{
                tipshow(data.info,'warn');   
            }
            
        }
    });
	$(this).parent().hide();
});
$('.linkTo_cap .btn-default').click(function(){
	$(this).parent().hide();
});


$('.set_first').on('click',function(){
	//	控制只能添加3个一级菜单
	if($('.menu').length < 3){
			$.ajax({
			    type : "get", 
			    url : "/merchants/wechat/save_menu", 
			    dataType : "json",
			    success : function(data){
			    	console.log(data)
					if(data.status == 1 ){
						setTimeout(function(){
							window.location.reload();
						},500);
						tipshow(data.info,'info');
					}else{
						tipshow(data.info,'warn');	
					}
			    }
			});
	}else{
		tipshow('最多添加添加三个一级菜单','warn');	
		return false;
	}
	if ($('.set_menu').length == 0) {
		$('.set_right').show();
		$('.set_info').hide();
	}

});

//右侧回复内容链接点击后获取data-href

$(document).on('click','.main_link a',function(){
	menu_num = $(this).parents('.menu').index();
	ctl_linkTo = $(this).parents('.menu').find('.zx').index();
	if ($(this).parents('.menu').find('.second ul li').length == 0) {
		ctl_linkTo = -1;
	}
});
//menu_text点击显示二级标题
$(document).on('focusin','.menu_text',function(){
	var ul_left = $(this).outerWidth()/2 - $(this).children('ul').outerWidth()/2;
	var ul_top = - ($(this).children('ul').height() + 15);
	if ($(this).find('li').length > 0) {
		$(this).children('ul').show();
		$(this).children('ul').css({
			'left': ul_left,
			'top': ul_top,
		});
	}
	
});
$(document).on('focusout','.menu_text',function(){
	$(this).children('ul').hide();
});
//页面直接显示
// $('.menu_content .link_to').hide();
// $('.menu_content .link_to:first').show();
//添加二级菜单
$(document).on('click','.set_second',function(){
	//	控制只能添加五个二级菜单
	var _this = $(this);
	if(_this.siblings('.second').find('ul li').length < 5){
		var parent_id = _this.parents('.menu').data('id');
		$.ajax({
		    type : "get", //jquey是不支持post方式跨域的
		    // async:false,
		    url : "/merchants/wechat/save_menu", //跨域请求的URL
		    dataType : "json",
		    data:{'parent_id': parent_id},
			//成功获取跨域服务器上的json数据后,会动态执行这个callback函数
		    success : function(data){ 
		    	console.log(data)
		    	if(data.status == 1){
		    		setTimeout(function(){
						window.location.reload();
					},500);
					tipshow(data.info,'info');	
		    	}else{
		    		tipshow(data.info,'warn');	
		    	}
		        
		    }
		});
	}else{
		tipshow('最多添加添加五个二级菜单','warn');	
	}
	
});
//圆圈删除
var menuLevel = 0;//菜单级别
$('.circle_close').click(function(){
	menuLevel = 1;//一级菜单
	menu_num = $(this).parents('.menu').index();
});
//菜单标题样式 控制
$(document).on('click','.menu_title li',function(){
	$(this).parents('.menu_title').find('li').removeClass('zx');
	$(this).addClass('zx');
	$(this).parents('.menu').find('[data-id]').hide();
	var index;
	if ( $(this).parents('.aa').hasClass('first')) {
		index = -1;
	} else{
		index = $(this).index();
	}
	$(this).parents('.menu').find('[data-id="'+index+'"]').show();
});
//一级菜单点击后回复隐藏
var firstMenu = true;//当前选中一级菜单为true  二级菜单为false
$(document).on('click','.first li',function(){
	firstMenu = true;
	if ($(this).parents('.first').siblings('.second').find('li').length > 0) {
		$(this).parents('.menu').find('.reply_content').hide();
		$(this).parents('.menu').find('')
	}
});
$(document).on('click','.second li',function(){
	firstMenu = false;
	$(this).parents('.menu').find('.reply_content').show();
});
//编辑创建模态框
var editor_modal =  '<div class="editor_modal">'
						+'<input type="text" />'
						+'<button class="btn btn-primary">确定</button>'
						+'<button class="btn btn-default">取消</button>'
					+'</div>';
$(document).on('click','.opts_editor',function(e){
	if($(e.target).hasClass('opts_editor')){
		$(this).prepend(editor_modal);
		$(this).find('input').val($(this).parents('li').children('.h5').text())
	}
	return false;
});
//删除
var first_index;
var second_index;
var ul;
$(document).on('click','.opts_delete',function(){
	ul =$(this).parents('ul');
	first_index = $(this).parents('.menu').index();
	second_index = $(this).parents('li').index();
	menuLevel = 2;
});
//删除二级菜单
$('.delete_pop .sure_btn').click(function(){
	if(menuLevel == 1){//删除一级菜单
		$.ajax({
	        type : "get", //jquey是不支持post方式跨域的
	        // async:false,
	        url : "/merchants/wechat/save_menu", //跨域请求的URL
	        dataType : "json",
	        data: {'id': $('.menu').eq(menu_num).data('id'),'type':'del'},
	        //成功获取跨域服务器上的json数据后,会动态执行这个callback函数
	        success : function(data){
	            console.log(data) 
	            if(data.status == 1){
					setTimeout(function(){
							window.location.reload();
						},500);
	                tipshow(data.info,'info');   
	            }else{
	                tipshow(data.info,'warn');   
	            }
	            
	        }
	    });
	}else if(menuLevel == 2){//删除二级菜单
		var id = $('.menu:eq('+first_index+') .second ul li:eq('+second_index+')').data('linkid');
		var parent_id = $('.menu:eq('+first_index+')').data('id');
		$.get('/merchants/wechat/save_menu',{'id':id,'parent_id':parent_id,'type':'del'},function(data){
			if(data.status == 1){
				$(this).parents('li').remove();
				$('.menu_text:eq('+first_index+') ul li:eq('+second_index+')').remove();
				for (var i =0;i < ul.find('.num').length;i ++) {
					ul.find('.num').eq(i).text((i+1)+'.');
				}
				ul.parents('.menu').find('[data-id="'+second_index+'"]').remove();
				for (var i =0;i < $('.link_to').length;i ++) {
					$('.link_to').eq(i).attr('data-id',(i-1));
				}
				if (ul.parent().find('ul li').length == 0) {
					ul.parents('.menu').find('[data-id="-1"]').text('');
				}
				setTimeout(function(){
					window.location.reload();
				},500);
				tipshow(data.info,'info');
			}else{
				tipshow(data.info,'warn');
			}
		});
	}
	popoverHidden();
});
$('.delete_pop .cancel_btn').click(function(){
	popoverHidden();
});
//取消
$(document).on('click','.editor_modal .btn-default',function(e){
	$('.editor_modal').remove();
	e.stopPropagation();
});
//确定
$(document).on('click','.editor_modal .btn-primary',function(e){
	var _this = $(this);
	first_index = $(this).parents('.menu').index();
	second_index = $(this).parents('li').index();
	var parent_id = _this.parents('.menu').data('id');
	var id = $('.menu:eq('+first_index+') .second ul li:eq('+second_index+')').data('linkid');
	if (_this.parents('.first').length == 1) {
		console.log({'id': parent_id,'name': _this.siblings('input').val()})
		if(_this.siblings('input').val().length>5){
			tipshow('不能超过5个字','warn');
			return false;	
		}
		$.ajax({
		    type : "get", //jquey是不支持post方式跨域的
		    // async:false,
		    url : "/merchants/wechat/save_menu", //跨域请求的URL
		    dataType : "json",
		    data:{'id': parent_id,'name': _this.siblings('input').val(),'type':'update'},
			//成功获取跨域服务器上的json数据后,会动态执行这个callback函数
		    success : function(data){ 
		    	console.log(data)
		    	if(data.status == 1){
		    		setTimeout(function(){
						window.location.reload();
					},500);
					_this.parents('li').find('.h5').text($(this).siblings('input').val());
					// $('.menu_text:eq('+first_index+') button').text(_this.siblings('input').val());
					tipshow(data.info,'info');	
		    	}else{
		    		tipshow(data.info,'warn');	
		    	}
		        
		    }
		});
	}else{
		console.log({'id': id,'value': _this.siblings('input').val(),'parent_id': parent_id})
		if(_this.siblings('input').val().length>13){
			tipshow('不能超过13个字','warn');
			return false;	
		}
		$.ajax({
		    type : "get", //jquey是不支持post方式跨域的
		    // async:false,
		    url : "/merchants/wechat/save_menu", //跨域请求的URL
		    dataType : "json",
		    data:{'id': id,'name': _this.siblings('input').val(),'parent_id': parent_id,'type':'update'},
			//成功获取跨域服务器上的json数据后,会动态执行这个callback函数
		    success : function(data){ 
		    	console.log(data);
		    	if(data.status == 1){
		    		setTimeout(function(){
						window.location.reload();
					},500);
					_this.parents('li').find('.h5').text($(this).siblings('input').val());
					// $('.menu_text:eq('+first_index+') button').text(_this.siblings('input').val());
					tipshow(data.info,'info');	
		    	}else{
		    		tipshow(data.info,'warn');	
		    	}
		        
		    }
		});
		$('.menu_text:eq('+first_index+') ul li:eq('+second_index+')').text($(this).siblings('input').val());
	}
	$('.editor_modal').remove();
	e.stopPropagation();
});

//实例化编译器
var ue = UE.getEditor('editor', {
    toolbars: [
        [/*'emotion',*/'link']
    ],
    wordCount:false, 
    elementPathEnabled:false,
    maximumWords:200,
    enableAutoSave: false,
    autoHeightEnabled: true,
    autoFloatEnabled: true
});
//回复内容控制
$(document).on('click','.ctl_editor',function(){
	menu_num = $(this).parents('.menu').index();
	ctl_linkTo = $(this).parents('.menu').find('.zx').index();
	if ($(this).parents('.menu').find('.second ul li').length == 0) {
		ctl_linkTo = -1;
	}
	var editor_left = $(this).offset().left - $('#editor').width()/2 - 20;
	var editor_top = $(this).offset().top - 8;
	$('#editor').css({
		'left': editor_left,
		'top' : editor_top,
	});
	$('#editor').toggle();
});
$(document).on('click','#editor .btn-primary',function(){
	var _this = $(this);
	var parent_id =$('.menu').eq(menu_num).data('id');
	var id = $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid');
	if(ctl_linkTo == -1){
		var data = {
			'id': parent_id,
			'content': UE.getEditor('editor').getContent()
		}
	}else{
		var data = {
			'id': id,
			'content': UE.getEditor('editor').getContent(),
			'parent_id': parent_id
		}
	}
	$.ajax({
	    type : "get", //jquey是不支持post方式跨域的
	    // async:false,
	    url : "/merchants/wechat/save_menu", //跨域请求的URL
	    dataType : "json",
	    data: data,
		//成功获取跨域服务器上的json数据后,会动态执行这个callback函数
	    success : function(data){ 
	    	console.log(data)
	    	if(data.status == 1){
	    		setTimeout(function(){
					window.location.reload();
				},500);
				// $(this).parents('#editor').hide();
				// $('.menu:eq('+menu_num+')').find('[data-id="'+ctl_linkTo+'"]').html(UE.getEditor('editor').getContent());
				tipshow(data.info,'info');	
	    	}else{
	    		tipshow(data.info,'warn');	
	    	}
	        
	    }
	});
});
$(document).on('click','#editor .btn-default',function(){
	$(this).parents('#editor').hide();
});

$(document).on('click','.btn_group .btn-primary',function(){
	$('.mask').removeClass('no');
	$.get('/merchants/wechat/create_menu',function(data){
		if(data.status == 1){
			tipshow(data.info,'info');
		}else{
			tipshow(data.info,'warn');
		}
		$('.mask').addClass('no');
	});
});
$('body').click(function(event){
	var target = $(event.target);
	if (target.hasClass('linkTo_cap') || target.hasClass('shop_cat') 
		|| target.parents('.linkTo_cap').length !== 0) {
	console.log(target.parents('.linkTo_cap').length !== 0)
		return false;
	}
	$('.linkTo_cap').hide();
})
$(".js_modal").click(function(){
	menu_num = $(this).parents('.menu').index();//一级菜单下标
	ctl_linkTo = $(this).parents('.menu').find('.zx').index();//二级菜单下标
	if ($(this).parents('.menu').find('.second ul li').length == 0) {//设置项为一级菜单
        ctl_linkTo = -1;
    }
	var _type = $(this).data("type"); 
	var _modal = $(this).data("modal"); 
	var _http = $(this).data("http") ? $(this).data("http") : "get";//默认get请求 
	modalObject.open({
		"itemType": _type,
		"modal": _modal,
		"wid": wid,
		"http": _http
	})
});
/**
 * 小程序模态框
 */
$(".js_xcx").click(function(){
	menu_num = $(this).parents('.menu').index();//一级菜单下标
	ctl_linkTo = $(this).parents('.menu').find('.zx').index();//二级菜单下标
	if ($(this).parents('.menu').find('.second ul li').length == 0) {//设置项为一级菜单
        ctl_linkTo = -1;
    }
    console.log($(".xcx_madel").width())
    $(".xcx_madel").removeClass("hide");
    $(".xcx_madel").css({
    	top: $(this).offset().top - 20,
    	left: $(this).offset().left -$(".xcx_madel").width()/2 - 95
    });
    
})
$(".xcx_madel .btn-default").click(function(){
	$(".xcx_madel").addClass("hide");
});
$(".xcx_madel .btn-primary").click(function(){
	var xcx_http = $(".xcx_http").val();
	var xcx_appid = $(".xcx_appid").val();
	// if(!xcx_http){
	// 	tipshow("小程序链接不能为空",'warn');
	// 	return;
	// }
	if(!xcx_appid){
		tipshow("小程序appid不能为空",'warn');  
		return; 
	}
	if(ctl_linkTo == -1){//一级菜单修改内容
	    var data = {
	        'id': $('.menu').eq(menu_num).data('id'),
	        'value':xcx_http,
	        'content':{
	        	'appid':xcx_appid,
	            'content_id':0,
	            'content_title': "小程序",
	            'type': 12
	        }
	    };
	}else{//二级菜单修改内容
	    var data = {
	        'id': $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid'),
	        'parent_id': $('.menu').eq(menu_num).data('id'),
	        'value':xcx_http,
	        'content':{
	        	'appid':xcx_appid,
	            'content_id':0,
	            'content_title': "小程序",
	            'type': 12
	        }
	    };
	}
	$.ajax({
        type : "get", 
        url : "/merchants/wechat/save_menu",
        dataType : "json",
        data:data,
        success : function(data){
            if(data.status == 1){
                setTimeout(function(){
                    window.location.reload();
                },500);
                tipshow(data.info,'info');   
            }else{
                tipshow(data.info,'warn');   
            }
            
        }
    });
	$(".xcx_madel").addClass("hide");
});
/*
*其他类外链公用方法  （new）
*解决旧方法扩展差的问题
*@modal：微社区
*data @param id：二级菜单id parent_id：一级菜单id value：链接 
*/
$(".js_linkHref").click(function(){
	menu_num = $(this).parents('.menu').index();
    ctl_linkTo = $(this).parents('.menu').find('.zx').index();
    if ($(this).parents('.menu').find('.second ul li').length == 0) {
        ctl_linkTo = -1;
    }
	switch ($(this).data("type"))
	{
		case 15:
			if(ctl_linkTo == -1){//一级菜单修改内容
	            var data = {
	                'id': $('.menu').eq(menu_num).data('id'),
	                'value':$(this).data('href'),
	                'content':{
	                    'content_id':0,
	                    'content_title': "微社区",
	                    'type': 15
	                }
	            };
	        }else{//二级菜单修改内容
	            var data = {
	                'id': $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid'),
	                'parent_id': $('.menu').eq(menu_num).data('id'),
	                'value':$(this).data('href'),
	                'content':{
	                    'content_id':0,
	                    'content_title': "微社区",
	                    'type': 15
	                }
	            };
	        }
		break;
	}
	$.ajax({
        type : "get", 
        url : "/merchants/wechat/save_menu",
        dataType : "json",
        data:data,
        success : function(data){
            if(data.status == 1){
                setTimeout(function(){
                    window.location.reload();
                },500);
                tipshow(data.info,'info');   
            }else{
                tipshow(data.info,'warn');   
            }
            
        }
    });
});
/*客服*/
$(".js_service").click(function(){
	menu_num = $(this).parents('.menu').index();
    ctl_linkTo = $(this).parents('.menu').find('.zx').index();
    if ($(this).parents('.menu').find('.second ul li').length == 0) {
        ctl_linkTo = -1;
    }
	switch ($(this).data("type"))
	{
		case 18:
			if(ctl_linkTo == -1){//一级菜单修改内容
	            var data = {
	                'id': $('.menu').eq(menu_num).data('id'),
	                'value':$(this).data('href'),
	                'content':{
	                    'content_id':0,
	                    'content_title': "微信客服",
	                    'type': 18
	                }
	            };
	        }else{//二级菜单修改内容
	            var data = {
	                'id': $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid'),
	                'parent_id': $('.menu').eq(menu_num).data('id'),
	                'value':$(this).data('href'),
	                'content':{
	                    'content_id':0,
	                    'content_title': "微信客服",
	                    'type': 18
	                }
	            };
	        }
		break;
	}
	$.ajax({
        type : "get", 
        url : "/merchants/wechat/save_menu",
        dataType : "json",
        data:data,
        success : function(data){
            if(data.status == 1){
                setTimeout(function(){
                    window.location.reload();
                },500);
                tipshow(data.info,'info');   
            }else{
                tipshow(data.info,'warn');   
            }
            
        }
    });
});
