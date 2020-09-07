var id =window.location.href.slice(window.location.href.lastIndexOf("/")+1)||'';
id = parseInt(id)?id:'';
var _token =document.getElementsByTagName('meta')[4].content;//token值
page_type = 1;
picTextType = true;//true为多条图文
var list_index = 0;//确定要添加的数据在哪个封面 默认为第一个封面
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
            'simpleupload', //单图上传
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
	$('.list:eq('+list_index+') .cover_editor').html(UE.getEditor('editor').getContent());
	if($('.list:eq('+list_index+') .cover_editor').html().length>0){
    	$('.js_editor').remove();
	}
});
//初始化ue内容
var ue = UE.getEditor('editor');
ue.ready(function() { 
	ue.setContent(ueditorContent); 
});
//标题input失焦左侧标题改变
$('#title').on('blur',function(){
	$('.list:eq('+list_index+') .title').text($(this).val());
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
//作者input失焦后保存内容在左侧
$('#author').on('blur',function(){
	$('.list:eq('+list_index+') .cover_author').text($(this).val());
});
//文本域失焦内容添加在左侧封面内容
$('.digest').on('blur',function(){
	$('.cover_content').text($(this).val());
});
// 封面图文选择后保存内容在左侧
$('.show_cover_pic').on('change',function(){
	if($(this).is(':checked')){//显示在页面 1
		$('.list:eq('+list_index+') .cover_checked').val('1');
	}else{
		$('.list:eq('+list_index+') .cover_checked').val('0');
	}
});
//模态框点击切换
// //自定义外链的模态框下的按钮事件
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
// });
// $('.linkTo_cap .btn-default').on('click',function(){
// 	$(this).parent().hide();
// });

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
	if(that.find('.cover_img img').attr('src') == ''){
		$('.share_img_box').addClass("hide");
		$('.js_img').text('+添加图片').removeClass("add-goods2").addClass("add-goods");//修改样式
		$('.share_img').attr('src',"");
	}else{
		$('.js_img').text('修改图片').removeClass("add-goods").addClass("add-goods2");//修改样式
		$('.share_img_box').removeClass("hide");
		$('.share_img').attr('src',that.find('.cover_img img').attr('src'));
	}
	//显示左侧保存的富文本内容
	if(that.find('.cover_editor').html() == ''){
		UE.getEditor('editor').setContent('');
	}else{
		UE.getEditor('editor').setContent(that.find('.cover_editor').html());
	}
	//显示作者
	if(that.find('.cover_author').text().length == 0){
		$('#author').val('');
	}else{
		$('#author').val(that.find('.cover_author').text());
	}
	//显示封面图片选择
	that.find('.cover_checked').val()==0?$('.show_cover_pic').prop('checked',false):$('.show_cover_pic').prop('checked',true);
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
	getContent($(this));
	$('.left_content .list').find(".hover .delete_cap").hide();
	$('.left_content .list').children(".opts").removeClass('hover');
	$(this).children(".opts").addClass('hover');
	$('.js_info, .js_imgInfo, .js_editor, .js_linkAdress').remove();//消除提示
});
//点击新增创建新的封面
$('.app_left .left_bottom').on('click',function(){
	$('.js_info, .js_imgInfo, .js_editor, .js_linkAdress').remove();//消除提示
	var new_cover = '<div class="cover_list list" data-id="0">'
						+'<div class="coverListBody">'
							+'<span class="title">标题</span>'
							+'<div class="cover_img"><span>缩略图</span><img src=""/></div>'
						+'</div>'
						+'<div class="cover_author" style="display: none;"></div>'
						+'<div class="cover_editor" style="display: none;"></div>'
						+'<input type="hidden" class="cover_checked" value="0">'
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
	$('.show_cover_pic').attr('checked',false);
	$('#title').val('');
	$('.js_img').text('+添加图片').addClass('add-goods').removeClass("add-goods2");
	$('#author').val('');
	$('.share_img_box').addClass("hide");
	$('.outer_link').hide();
    $('.outer_link').text('');
    $('.outer_link').attr('href','');
    $('#menu1').text('设置链接到的页面');
	UE.getEditor('editor').setContent('');
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
//--------删除模态框点击事件----------
$(document).on('click','.opts .delete',function(e){
	$('.left_content .list').find(".hover .delete_cap").hide();
	$('.left_content .list').children(".opts").removeClass('hover');
	$(this).siblings('.delete_cap').show();
	$(this).parent().addClass('hover');
	e.stopPropagation();
});
$(document).on('click','.delete_cap .btn-default',function(){
	$(this).parent().hide();
});
$(document).on('click','.delete_cap .btn-primary',function(e){
	$(this).parents('.cover_list').remove();
	$('.app_right').css('mardin-top',($('.left_content .list:last').offset().top-210));
	list_index = $('.left_content .list:last').index();
	e.stopPropagation();
});
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
		$('.list:eq('+list_index+') .cover_img span').text('');
		// $('.img_small').attr('src',pictureSrc);
		// $('.img_small').css('display','inline-block');
		$('.list:eq('+list_index+') .cover_img img').attr('src',pictureSrc)
		// $('.js_img').text('重新选择');
		$('.js_imgInfo').remove();
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
	$('.list:eq('+list_index+') .cover_img img').attr('src',"")
	$(".js-add-picture").html("+添加图片").addClass("add-goods").removeClass("add-goods2");
});
//点击提交
$('.submit_btn .btn').on('click',function(){
	$(this).prop('disabled',true);
	var id = [];
	var title = [];
	var cover = [];
	var author = [];
	var show_cover_pic = [];
	var content = [];
	var content_source_url = [];
	var content_source_title = [];
	$('.js_info, .js_imgInfo, .js_editor, .js_linkAdress').remove();//消除提示
	for (var i = 0;i < $('.left_content .title').length;i ++){
		if($('.left_content .title:eq('+i+')').text() == '标题' && $('.js_info').length == 0){
			$('#title').after('<p class="js_info" style="color:#b94a48;">标题不能为空</p>');
		}
		if ($('.left_content .cover_img:eq('+i+') img').attr('src') == '' && $('.js_imgInfo').length == 0) {
			$('.add_img').after('<p class="js_imgInfo" style="color:#b94a48;">图片不能为空</p>');
		}
		if ($('.left_content .cover_editor:eq('+i+')').html().length == 0 && $('.js_editor').length == 0) {
			$('#editor').after('<p class="js_editor" style="color:#b94a48;">内容不能为空</p>');
		}
		if ($('.left_content .cover_href:eq('+i+')').text() == '' && $('.js_linkAdress').length == 0) {
			$('#menu1').after('<p class="js_linkAdress" style="color:#b94a48;">请设置链接地址</p>');
		}
		if($('.js_editor').length > 0 || $('.js_imgInfo').length > 0 || $('.js_info').length > 0 || $('.js_linkAdress').length > 0){
			console.log(i)
			getContent($('.left_content .list:eq('+i+')'));
			$(this).prop('disabled',false);
			return false;
		}
		id.push($('.left_content .list:eq('+i+')').data('id')); 
		title.push($('.left_content .list:eq('+i+') .title').text());
		cover.push($('.left_content .list:eq('+i+') .cover_img img').attr('src'));
		author.push($('.left_content .list:eq('+i+') .cover_author').text()||'');
		show_cover_pic.push($('.left_content .list:eq('+i+') .cover_checked').val()||'');
		content.push($('.left_content .list:eq('+i+') .cover_editor').html());
		content_source_url.push($('.left_content .list:eq('+i+') .cover_href').attr('href'));
		content_source_title.push($('.left_content .list:eq('+i+') .cover_href').text()||'');
	}
	materialAjax(id,2,title,cover,author,show_cover_pic,content_source_url,content_source_title,'',content,$(this),2);
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