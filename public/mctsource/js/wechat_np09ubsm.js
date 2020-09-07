var id =window.location.href.slice(window.location.href.lastIndexOf("/")+1)||'';
id = parseInt(id)?id:'';
var _token =document.getElementsByTagName('meta')[4].content;//token值
page_type = 1;
//标题input失焦左侧标题改变
$('#title').on('blur',function(){
	if($(this).val().length > 0){
		$('.title').text($(this).val());
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
//文本域失焦内容添加在左侧封面内容
$('.digest').on('blur',function(){
	if ($(this).val().length > 0) {
		$('.cover_content').text($(this).val());
		$('.cover_content').show();
	} else{
		$('.cover_content').text($(this).val());
		$('.cover_content').hide();
	}
	$('.js_textarea').remove();
});
//文本域限制字数
$('.digest').on('keydown',function(e){
	if($(this).val().length >= 10 && $('.js_textarea').length == 0) {
		$('.digest').after('<p class="js_textarea" style="color:#b94a48;">字数不能超过120个</p>');
	}
});
//封面事件未当前时间
var mydate = new Date();
$('.time').text(mydate.getFullYear()+'-'+(mydate.getMonth()+1)+'-'+mydate.getDate());
//模态框点击切换
//刮刮乐
// $('.modal .group1').on('click',function(){
// 	$('.group1').removeClass('list_active');
// 	$(this).addClass('list_active');
// 	$('.group2').hide();
// 	$('.group2').eq($(this).index()).css({
// 		'display':'inline-block',
// 		'border':0	
// 	});
// });

//模态框点击选取后页面显示外链
// $('.modal-dialog tbody .btn-default').on('click',function(){
// 	$('.outer_link').css("display",'inline-block');
// 	$('.outer_link').text($(this).parents('tr').find('a').text());
// 	$('.outer_link').attr('href',$(this).parents('tr').find('a').attr('href'));
// 	$(this).parents('.modal').modal('hide');
// 	$('#menu1').text('修改');
// 	$('.js_linkAdress').remove();
// });
//下拉菜单列表点击获取链接显示在页面
$('.custom').on('click',function(){
	var top = $(this).parent().offset().top -40;
	var left = $(this).parent().offset().left - $('.linkTo_cap').width()/2 -$(this).parent().width()/2;
	$('.linkTo_cap').css({
		'display':'block',
		'top':top,
		'left':left
	});
});
//自定义外链的模态框下的按钮事件
// $('.linkTo_cap .btn-primary').on('click',function(){
// 	$('.outer_link').css("display",'inline-block');
// 	var str = /^http:\/\//;
// 	var value = $(this).siblings('input').val();
// 	if (str.test(value)) {
// 		$('.outer_link').text(value);
// 		$('.outer_link').attr('href',value);
		
// 	} else{
// 		value = 'http://'+value;
// 		$('.outer_link').text(value);
// 		$('.outer_link').attr('href',value);
// 	}
// 	$(this).parent().hide();
// 	$('#menu1').text('修改');
// 	$('.js_linkAdress').remove();
// });
// $('.linkTo_cap .btn-default').on('click',function(){
// 	$(this).parent().hide();
// });
//--------图标库模态框显示---------
//----------------图片模态框点击事件---------------

//点击确认后获取图片
$('.myModal-adv .ui-btn-primary').on('click',function(){
	//2018.10.18 图片尺寸限制 by 倪凯嘉
	var imgWidth=pictureSize.split("x")[0];//增加图片尺寸信息 from wechat_base.js
	var imgHeight=pictureSize.split("x")[1]; 
	if(imgWidth<800 || imgWidth/imgHeight<1.6 || imgWidth/imgHeight>2){
		tipshow('图片尺寸不符合，请重新上传图片','warm');	
	}else{
		$("input[name='share_img']").val(pictureSrc);
		$('.share_img').attr('src',pictureSrc);
		$(".share_img").attr("src",pictureSrc).parent().removeClass('hide');
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
		// $('.img_small').attr('src',pictureSrc);
		// $('.img_small').css('display','inline-block');
		$('.cover_img').show();
		$('.cover_img img').attr('src',pictureSrc);
		$('.js_imgInfo').remove();
		//$('.js_img').text('重新选择');
		$('.reply_cap .ctts').children().hide();
		$('.ctts .imgs').show();
		$('.send_info').hide();
		$('.editor_img .select').hide();
		$('.editor_img .message_img').show();
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
});
//点击删除图片
$(".delete").click(function(){
	$("input[name='share_img']").val("");
	$(".share_img").attr("src","").parent().addClass('hide');
	$('.cover_img img').attr("src","");  
	$(".js-add-picture").html("+添加图片").addClass("add-goods").removeClass("add-goods2");
});
//--------点击提交------------
$('.submit_btn .btn').on('click',function(){
	$(this).prop('disabled',true);
	if ($('.share_img').attr('src') == '' && $('.js_imgInfo').length == 0) {
		$('.add_img').after('<p class="js_imgInfo" style="color:#b94a48;">图片不能为空</p>');
	}
	if($('#title').val().length == 0 && $('.js_info').length == 0){
		$('#title').after('<p class="js_info" style="color:#b94a48;">标题不能为空</p>');
	}
	if ($('#menu1').text().length > 2 && $('.js_linkAdress').length == 0) {
		$('#menu1').after('<p class="js_linkAdress" style="color:#b94a48;">请设置链接地址</p>');
	}
	if($('.js_editor').length > 0 || $('.js_imgInfo').length > 0 || $('.js_info').length > 0){
		$(this).prop('disabled',false);
		return false;
	}
	var id = $('.cover').data('id');
	var title = $('#title').val();
	var cover = $('.share_img').attr('src');
	var author = $('#author').val()||'';
	var digest = $('.digest').val();
	var show_cover_pic = '';
	var content = '';
	var content_source_url = $('.outer_link').attr('href');
	var content_source_title = $('.outer_link').text();
	materialAjax(id,1,title,cover,author,show_cover_pic,content_source_url,content_source_title,digest,content,$(this),3);
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