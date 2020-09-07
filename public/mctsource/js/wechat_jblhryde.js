var id =window.location.href.slice(window.location.href.lastIndexOf("/")+1)||'';
id = parseInt(id)?id:'';
var _token =document.getElementsByTagName('meta')[4].content;//token值
page_type = 1;
picTextType = true;//true为多条图文
var list_index = 0;//确定要添加的数据在哪个封面 默认为第一个封面
//标题input失焦左侧标题改变
$('#title').on('blur',function(){
	if($(this).val().length > 0){
		$('.list:eq('+list_index+') .title').text($(this).val());
	}
	if ($(this).val().length == 0 && $('.js_info').length == 0) {
		$(this).after('<p class="js_info" style="color:#b94a48;">标题不能为空</p>')
	}else{
		$('.js_info').remove();
	}
	if($(this).val().length > 64){
		$(this).after('<p class="js_info info64" style="color:#b94a48;">标题长度不能超过64个字</p>')
	}else{
		$('.info64').remove();
	}
});
//模态框点击切换
//刮刮乐
$('.modal .group1').on('click',function(){
	$('.group1').removeClass('list_active');
	$(this).addClass('list_active');
	$('.group2').hide();
	$('.group2').eq($(this).index()).css({
		'display':'inline-block',
		'border': 0	
	});
});
//模态框点击选取后页面显示外链
$('.modal-dialog tbody .btn-default').on('click',function(){
	$('.outer_link').css("display",'inline-block');
	$('.outer_link').text($(this).parents('tr').find('a').text());
	$('.outer_link').attr('href',$(this).parents('tr').find('a').attr('href'));
	$(this).parents('.modal').modal('hide');
	$('#menu1').text('修改');
	$('.js_linkAdress').remove();
	$('.cover_link').text($(this).parents('tr').find('a').text());
	$('.cover_link').attr('href',$(this).parents('tr').find('a').attr('href'));
});
//下拉菜单列表点击获取链接显示在页面
$('.custom').on('click',function(){
	var top = $(this).parent().offset().top -40;
	var left = $(this).parent().offset().left - $('.linkTo_cap').width()/2 - $(this).width()/2;
	$('.linkTo_cap').css({
		'display':'block',
		'top':top,
		'left':left
	});
});
//自定义外链的模态框下的按钮事件
$('.linkTo_cap .btn-primary').on('click',function(){
	$('.outer_link').css("display",'inline-block');
	var str = /^http:\/\//;
	var value = $(this).siblings('input').val();
	if (str.test(value)) {
		$('.outer_link').text(value);
		$('.outer_link').attr('href',value);
		
	} else{
		value = 'http://'+value;
		$('.outer_link').text(value);
		$('.outer_link').attr('href',value);
	}
	$(this).parent().hide();
	$('#menu1').text('修改');
	$('.js_linkAdress').remove();
	$('.cover_link').text(value);
	$('.cover_link').attr('href',value);
});
$('.linkTo_cap .btn-default').on('click',function(){
	$(this).parent().hide();
});
//----------封面改变事件------------
function getContent(that){
	$('.app_right').css('margin-top',(that.offset().top-210));
	list_index = that.index();
	//点击切换后右侧内容显示左侧封面显示内容
	if(that.find('.title').text() == '标题'){
		$('#title').val('');
	}else{
		$('#title').val(that.find('.title').text());
	}
	//右侧图片显示左侧图片
	//updata by 倪凯嘉 2018-10-19 
	if(that.find('.cover_img img').attr('src') == ''){
		$('.share_img_box').addClass("hide");
		$('.js_img').text('+添加图片').removeClass("add-goods2").addClass("add-goods");
		$('.share_img').attr('src',"");
	}else{
		$('.js_img').text('修改图片').removeClass("add-goods").addClass("add-goods2");
		$('.share_img_box').removeClass("hide");
		$('.share_img').attr('src',that.find('.cover_img img').attr('src'));
	}
	//设置连接还原
	if(that.find('.cover_href').text().length == 0){
		$('.outer_link').hide();
	    $('.outer_link').text('');
	    $('.outer_link').attr('href','');
	    $('#menu1').text('设置链接到的页面');
	}else{
		$('.outer_link').css("display",'inline-block');
        $('.outer_link').text(that.find('.cover_href').text());
        $('.outer_link').attr('href',that.find('.cover_href').attr('href'));
        $('#menu1').text('修改');
	}
}
//点击封面切换封面并且调整右侧定位
$(document).on('click','.left_content .list',function(){
	$('.js_info, .js_imgInfo, .js_linkAdress').remove();//消除提示
	getContent($(this));
	$('.left_content .list').find(".hover .delete_cap").hide();
	$('.left_content .list').children(".opts").removeClass('hover');
	$(this).children(".opts").addClass('hover');
});
//点击新增创建新的封面
$('.app_left .left_bottom').on('click',function(){
	$('.js_info, .js_imgInfo, .js_linkAdress').remove();//消除提示
	var new_cover = '<div class="cover_list list" data-id="0">'
						+'<div class="coverListBody">'
							+'<span class="title">标题</span>'
							+'<div class="cover_img"><span>缩略图</span><img src=""/></div>'
						+'</div>'
						+'<a class="cover_href" href="javascripr:void(0);" style="display: none;"></a>'
						+'<div class="opts">'
							+'<span class="editor">编辑</span>'
							+'<span class="delete">删除</span>'
							+'<div class="delete_cap">'
								+'<span>确定删除</span>'
								+'<button class="btn btn-primary">确定</button>'
								+'<button class="btn btn-default">取消</button>'
							+'</div>'
						+'</div>'
					+'</div>';
//控制最多加八个内容
	if ($('.left_content .list').length <= 10) {
		$(this).before(new_cover);
	}else{
		tipshow('最多添加添加十个标题','warn');
	}
	$('.app_right').css('margin-top',($('.left_content .list:last').offset().top-210));//设置右侧添加内容的高度
	list_index = $('.left_content .list:last').index();//设置数据所添加封面的下标
//	新增右面后右侧的编译文本全部为空
	$('#title').val('');
	$('.js_img').text('+添加图片');
	$('.share_img_box').addClass("hide");
	$('.outer_link').hide();
	$('#menu1').text('设置链接到的页面');
});
//--------删除模态框点击事件----------
$(document).on('click','.opts .delete',function(e){
	$('.left_content .list').find(".hover .delete_cap").hide();
	$('.left_content .list').children(".opts").removeClass('hover');
	$(this).parent().addClass('hover');
	$(this).siblings('.delete_cap').show();
	e.stopPropagation();
});
$(document).on('click','.delete_cap .btn-default',function(){
	$(this).parent().hide();
});
$(document).on('click','.delete_cap .btn-primary',function(e){
	$(this).parents('.cover_list').remove();
	$('.app_right').css('margin-top',($('.left_content .list:last').offset().top-210));
	list_index = $('.left_content .list:last').index();
	e.stopPropagation();
});
//--------图标库模态框显示---------
//----------------图片模态框点击事件---------------

//点击确认后获取图片
$('.myModal-adv .ui-btn-primary').on('click',function(){
	//2018.10.19 图片尺寸限制 by 倪凯嘉
	var imgWidth=pictureSize.split("x")[0];//增加图片尺寸信息 from wechat_base.js
	var imgHeight=pictureSize.split("x")[1]; 
	if(imgWidth<800 || imgWidth/imgHeight<1.6 || imgWidth/imgHeight>2){
		tipshow('部分图片尺寸不符合，请重新上传图片','warm');	
	}else{
		$("input[name='share_img']").val(pictureSrc);
		$('.share_img').attr('src',pictureSrc);
		$(".share_img_box").removeClass("hide");
		$(".js-add-picture").html("修改图片").removeClass("add-goods").addClass("add-goods2");
		if($('.content_second').css('display') != 'none'){
			var add_img =  '<li class="image-item">'
								+'<img class="image-box" src="'+pictureSrc+'"/>'
								+'<div class="image-meta">1920*1200</div>'
								+'<div class="image-title">01.png</div>'
								+'<div class="attachment-selected no">'
									+'<i class="icon-ok icon-white"></i>'
							+ '</div>'
							+'</li>';
			$('.attachment-list-region:eq('+$('.category-list .active').index()+') .image-list').prepend(add_img);
			
		}
		//$('.img_small').attr('src',pictureSrc);
		//$('.img_small').css('display','inline-block');
		$('.list:eq('+list_index+') .cover_img span').text('');
		$('.list:eq('+list_index+') .cover_img img').attr('src',pictureSrc);
		//$('.js_img').text('重新选择');
		$('.js_imgInfo').remove();		
		$('.reply_cap .ctts').children().hide();
		$('.ctts .imgs').show();
		$('.send_info').hide();
		$('.editor_img .select').hide();
		$('.editor_img .message_img').show();
		$(this).parents('.cap').hide();
	}
	//重置上传样式
	$('.myModal-adv').modal('hide');
	$('.attachment-selected').addClass('no');
	$('.modal-footer .js-confirm').show();
	$('.modal-footer .ui-btn-primary').addClass('no');
	if($('.content_second').css('display') != 'none'){
		$('.content_first').show();
		$('.content_second').hide();
		closeUploader();
	};
})
//点击删除图片 2018-10-19
$(".delete").click(function(){
	$("input[name='share_img']").val("");
	$(".share_img").attr("src","").parent().addClass('hide');
	$('.list:eq('+list_index+') .cover_img img').attr('src',"")
	$(".js-add-picture").html("+添加图片").addClass('add-goods').removeClass("add-goods2");
});

//点击提交
$('.submit_btn .btn').on('click',function(){
	$(this).prop('disabled',true);
	var id = [];
	var title = [];
	var cover = [];
	var content_source_url = [];
	var content_source_title = [];
	$('.js_info, .js_imgInfo, .js_linkAdress').remove();//消除提示
	for (var i = 0;i < $('.left_content .title').length;i ++){
		if($('.left_content .title:eq('+i+')').text() == '标题' && $('.js_info').length == 0){
			$('#title').after('<p class="js_info" style="color:#b94a48;">标题不能为空</p>')
		}
		if ($('.cover_img:eq('+i+') img').attr('src') == '' && $('.js_imgInfo').length == 0) {
			$('.js_img').after('<p class="js_imgInfo" style="color:#b94a48;">图片不能为空</p>');
		}
		if ($('.cover_href:eq('+i+')').text() == '' && $('.js_linkAdress').length == 0) {
			$('#menu1').after('<p class="js_linkAdress" style="color:#b94a48;">请设置链接地址</p>');
		}
		if($('.js_linkAdress').length > 0 || $('.js_imgInfo').length > 0 || $('.js_info').length > 0){
			getContent($('.left_content .list:eq('+i+')'));
			$(this).prop('disabled',false);
			return false;
		}
		id.push($('.left_content .list:eq('+i+')').data('id')); 
		title.push($('.left_content .list:eq('+i+') .title').text());
		cover.push($('.left_content .list:eq('+i+') .cover_img img').attr('src'));
		content_source_url.push($('.left_content .list:eq('+i+') .cover_href').attr('href'));
		content_source_title.push($('.left_content .list:eq('+i+') .cover_href').text()||'');
	}
	materialAjax(id,2,title,cover,'','',content_source_url,content_source_title,'','',$(this),4);
});
//图片模态框添加图片分组事件 2018-10-18
$(".btn_left").on('click', function () {
    var name = $('.add_group_input').val();
    if(!name){
        return false
    }
    $.ajax({
        url:'/merchants/myfile/addClassify',
        type: 'POST',
        data:{
            name:name,
            _token:_token,
        },
        success:function (data) {
            console.log(data);
            if(data.status == 1){
                var _group = '<li class="js-category-item" data-id="'+data.data.id+'">'+data.data.name+'\
                            <span>0</span>\
                        </li>';
                $('.category-list').append(_group);
                $(".add_group_box").addClass('hide')
            }
        }
    })
})
$(".btn_right").on('click',function () {
    $(".add_group_box").addClass('hide')
    $('.add_group_list').attr('data-id','1');
})
$(".add_group_list").on('click',function () {
    var id = $(this).attr('data-id');
    if(id == 1){
        $(this).attr('data-id','2');
        $(".add_group_box").removeClass('hide')
    }else {
        $(this).attr('data-id','1');
        $(".add_group_box").addClass('hide')
    }
})