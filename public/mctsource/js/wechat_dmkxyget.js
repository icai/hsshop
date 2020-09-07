var id =window.location.href.slice(window.location.href.lastIndexOf("/")+1)||'';
id = parseInt(id)?id:'';
var _token =document.getElementsByTagName('meta')[4].content;//token值
page_type = 1;
//实例化编译器
var ue = UE.getEditor('editor', {
    toolbars: [
            ['bold', //加粗
            'italic', //斜体
            'underline', //下划线
            'strikethrough', //删除线
            'forecolor', //字体颜色
            'backcolor', //背景色
            'justifyleft', //居左对齐
            'justifycenter', //居中对齐
            'justifyright', //居右对齐
            'insertunorderedlist', //无序列表
            'insertorderedlist', //有序列表
            'blockquote', //引用
            ],
            [
            'emotion', //表情
            // 'insertimage', //单图上传
            // 'simpleupload', //单图上传
            'insertimage',
            'insertvideo', //视频
            'link', //超链接
            'removeformat', //清除格式
            'rowspacingtop', //段前距
            'rowspacingbottom', //段后距
            'lineheight', //行间距
            'paragraph', //段落格式
            'fontsize', //字号
            ],
            [
            'inserttable', //插入表格
            'deletetable', //删除表格
            'insertparagraphbeforetable', //"表格前插入行"
            'insertrow', //前插入行
            'deleterow', //删除行
            'insertcol', //前插入列
            'deletecol', //删除列
            'mergecells', //合并多个单元格
            'mergeright', //右合并单元格
            'mergedown', //下合并单元格
            'splittocells', //完全拆分单元格
            'splittorows', //拆分成行
            'splittocols', //拆分成列
            ]
        ],
	zIndex:2,
    wordCount:false, 
    elementPathEnabled:false,
    maximumWords:200,
    enableAutoSave: false,
    autoHeightEnabled: true,
    autoFloatEnabled: true
});
ue.addListener( 'selectionchange', function( editor ) {
    $('.js_editor').remove();
});
//初始化ue内容
var ue = UE.getEditor('editor');
ue.ready(function() { 
	ue.setContent(ueditorContent); 
});
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
	$('.cover_content').text($(this).val());
	$('.js_textarea').remove();
});
//文本域限制字数
$('.digest').on('keydown',function(e){
	if($(this).val().length >= 10 && $('.js_textarea').length == 0) {
		$('.digest').after('<p class="js_textarea" style="color:#b94a48;">字数不能超过120个</p>');
	}
});
//阅读全文点击后封面内容显示出来
$('.full_text').on('click',function(){
	$('.cover_content').show();
});
//封面事件未当前时间
var mydate = new Date();
$('.time').text(mydate.getFullYear()+'-'+(mydate.getMonth()+1)+'-'+mydate.getDate());

//模态框点击选取后页面显示外链
$('.modal-dialog tbody .btn-default').on('click',function(){
	$('.dropup_link').val($(this).parents('tr').find('a').text());//添加在input 用于请求
	$('.outer_link').css("display",'inline-block');
	$('.outer_link').text($(this).parents('tr').find('a').text());
	$('.outer_link').attr('href',$(this).parents('tr').find('a').attr('href'));
	$(this).parents('.modal').modal('hide');
	$('#menu1').text('修改');
});
//下拉菜单列表点击获取链接显示在页面
$('.custom').on('click',function(){
	var top = $(this).offset().top + 15;
	var left = $(this).offset().left - $('.linkTo_cap').width();
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
// 	$('.dropup_link').val(value);//添加在input 用于请求
// });
// $('.linkTo_cap .btn-default').on('click',function(){
// 	$(this).parent().hide();
// });
//--------图标库模态框显示---------
//----------------图片模态框点击事件---------------

//点击确认后获取图片
$('.myModal-adv .ui-btn-primary').on('click',function(){
	//2018.10.18 图片尺寸限制 by 倪凯嘉
	console.log(pictureSrc)
	console.log(pictureSize);//增加图片尺寸信息 from wechat_base.js
	var imgWidth=pictureSize.split("x")[0];
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
		$('.cover_img span').text('');
		//$('.img_small').attr('src',pictureSrc);
		//$('.img_small').css('display','inline-block');
		$('.cover_img img').attr("src",pictureSrc);
		console.log(pictureSrc)
		$('.js_imgInfo').remove();
		//$('.js_img').text("修改");
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
//营销活动
//营销活动弹框
$(document).on("click",".js_active",function(){
    activeModel.open({});
    cType = 7;
    picOrOther = false;//表示非图文
})
//点击提交
$('.submit_btn .btn').on('click',function(){
	$(this).prop('disabled',true);
	if (UE.getEditor('editor').getContent().length == 0 && $('.js_editor').length == 0) {
		$('#editor').after('<p class="js_editor" style="color:#b94a48;">内容不能为空</p>');
	}
	if ($('.img_small').attr('src') == '' && $('.js_imgInfo').length == 0) {
		$('.add_img').after('<p class="js_imgInfo" style="color:#b94a48;">图片不能为空</p>');
	}
	if($('#title').val().length == 0 && $('.js_info').length == 0){
		$('#title').after('<p class="js_info" style="color:#b94a48;">标题不能为空</p>')
	}
	if ($('#menu1').text().length > 2 && $('.js_linkAdress').length == 0) {
		$('#menu1').after('<p class="js_linkAdress" style="color:#b94a48;">请设置链接地址</p>');
	}
	if($('.js_editor').length > 0 || $('.js_imgInfo').length > 0 || $('.js_info').length > 0 || $('.js_linkAdress').length > 0){
		$(this).prop('disabled',false);
		return false;
	}
	var id = $('.cover').data('id');
	var title = $('#title').val();
	var cover =  $('.share_img').attr('src');
	var author = $('#author').val();
	var digest = $('.digest').val();
	var show_cover_pic = $('.show_cover_pic').prop('checked')?1:0;
	var content = UE.getEditor('editor').getContent();
	var content_source_url = $('.outer_link').attr('href');
	var content_source_title = $('.outer_link').text();
	materialAjax(id,1,title,cover,author,show_cover_pic,content_source_url,content_source_title,digest,content,$(this),1);

});
// 营销活动
//营销活动弹框
$("body").on('click','.js_active',function(){
    activeModel.open({});
    cType = 7;
    picOrOther = false;//表示非图文
})
// //图片模态框添加图片分组事件 2018-10-18
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