var _token =document.getElementsByTagName('meta')[4].content;//token值
// 页面加载直接切换表情
$('.words .value').each(function(){
    $(this).html(replace_em($(this).text()));//表情切换
});
$('.reply_list .reply_text').each(function(){
    $(this).html(replace_em($(this).text()));//表情切换
});
//新建自动回复
$('.handle_title>button').click(function(){
	$('.handle_title>.new_cap').show();
});
//添加规则
var dateArr = ['周一','周二','周三','周四','周五','周六','周日'];
$('.handle_title .btn-primary').on('click',function(){
    var rule_text = $(this).siblings('input').val();
    for (var i =0; i < $('.handle_content .rule_meta .name').length; i++) {
        if(rule_text == $('.handle_content .rule_meta .name').eq(i).text()){
            tipshow('关键词[未命名规则]重复','warn');
            $(this).parent('.new_cap').hide();
            return false;
        }
    }
    var rule_text = $(this).siblings('input').val();
	var num = $('.handle_content').length;
	var rule_li = '';
	for (var i = 0;i<7;i ++) {
		rule_li+='<li class="reply">'
			+'<span class="reply_type">文本</span><span class="reply_text">'+dateArr[i]+'</span>'
			+'<div class="reply_opts">'
				+'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
				+'<span>-</span>'
				+'<a class="replay_delete" href="javascript:void(0)">清空</a>'
			+'</div>'
		+'</li>';
	}
    var rule;
    function add_reply(id){
        rule='<div class="handle_content">'
       				+'<div class="rule_meta">'
       					+'<h5><span class="num">'+id+')</span><span class="name">'+rule_text+'</span>'
       						+'<div class="rule_opts">'
           						+'<a class="rule_edit" href="javascript:void(0)">'
           							+'编辑'
           					+'	</a>'
           					+'	<span>-</span>'
           						+'<a class="rule_delete" href="javascript:void(0)">删除</a>'
           					+'</div>'
           					+'<!--弹框开始-->'
               			+'	<div class="new_cap">'
               					+'<input type="text" name="" id="" value="为命名规则" />'
               					+'<button class="btn btn-primary" data-id="'+id+'">确定</button>'
               				+'	<button class="btn btn-default">取消</button>'
               			+'	</div>'
               				+'<!--弹框结束-->'
       					+'</h5>'
       				+'</div>'
       				+'<div class="rule_body clearfix">'
       					+'<div class="line"></div>'
       					+'<div class="rule_left">'
       						+'<div class="rule_keywords">'
       							+'关键词：'
       						+'</div>'
       						+'<div class="left_info">'
       							+'还没有任何关键字！'
       						+'</div>'
       						+'<div class="keywords_list">'
       							
       						+'</div>'
       						+'<div class="rule_add_keywords">'
       							+'<a class="co_38f js_ad" href="javascript:void(0)" data-rule_id="'+id+'">'
       								+'+添加关键词'
       							+'</a>'
       						+'</div>'
       					+'</div>'
       					+'<div class="rule_right" data-rule_id="'+id+'">'
       						+'<!--规则右边开始-->'
       						+'<div class="rule_reply">'
       							+'自动回复：<span>按周期发送</span>'
       						+'</div>'
       						+'<ol class="reply_list">'
       						+rule_li
       						+'</ol>'
       					+'</div>'
       				+'</div>'
       			+'</div>';
    }
    var that = $(this);
    var _input = $(this).siblings('input').val();//修改规则value值
    var _id = $(this).data('id');
    var data = {
        name: _input,
        id: _id,
        _token: _token
    };
    $.post('/merchants/wechat/replyRuleAdd/3', data, function(data) {
        if(data.status == 1){
            tipshow(data.info,'info');
            add_reply(data.data);
            if ($('.handle_content').length == 0) {
                $('.no_result').hide();
            }
            $('.handle_title').after(rule);
            that.parent('.new_cap').hide();
        }else{
            tipshow(data.info,'warn');
        }
        that.parent('.new_cap').hide();
    });
    
});
//为命名规则编辑
$(document).on('click','.rule_meta .rule_edit',function(){
    var rule_name = $(this).parent().siblings('.name').text();
    $(this).parent('.rule_opts').siblings('.new_cap').show();
    $(this).parent().siblings('.new_cap').children('input').val(rule_name);
});
$(document).on('click','.rule_meta .btn-primary',function(){
    var that = $(this);
    var _input = $(this).siblings('input').val();//修改规则value值
    var _id = $(this).data('id');
    var data = {
        name: _input,
        id: _id,
        _token: _token
    };
    $.post('/merchants/wechat/replyRuleAdd/3', data, function(data) {
        if(data.status == 1){
            tipshow(data.info,'info');
            that.parent().siblings('.name').text(that.siblings('input').val());//成功后更改
        }else{
            tipshow(data.info,'warn');
        }
        that.parent('.new_cap').hide();
    });
});
$('.new_cap .btn-default').on('click',function(){
	$(this).parent('.new_cap').hide();
});
//删除关键词
$(document).on('click','.keywords .close_circle',function(){
    var id = $(this).data('id');//id 的值;
    rule_index = $(this).parents('.handle_content').index('.handle_content');//自动回复规则下标
    var rule_id = $('.handle_content:eq('+rule_index+') .rule_add_keywords .js_ad').data('rule_id');//rule_id 的值
    var data = {
        id      : id,
        rule_id : rule_id,
        _token  : _token,
        type    : 3
    }
    var _this = $(this);
    $.post('/merchants/wechat/replyKeywordDel',data,function(data){
        if ( data.status == 1 ) {
            tipshow(data.info,'info');
            if(_this.parents('.rule_body').find('.keywords').length == 1){
                _this.parents('.keywords_list').children('.left_info').show();
            }
            _this.parent().remove();
            
        } else {
            tipshow(data.info,'warn');
        }
        $('.rule_add_cap').hide();//隐藏添加关键词弹框   
        
        return false;
    });
});
//未命名规则删除
$(document).on('click','.rule_delete',function(){
    var that = $(this);
    var _input = $(this).siblings('input').val();//修改规则value值
    var _id = $(this).data('id');
    var data = {
        type: 3,
        id: _id,
        _token: _token
    }
    $.post('/merchants/wechat/replyRuleDel', data, function(data) {
        if(data.status == 1){
            tipshow(data.info,'info');
            that.parents('.handle_content').remove();
            if ($('.handle_content').length == 0) {
                $('.no_result').show();
            }
            $('.page span').text('共 '+$('.handle_content').length+' 条，每页 3 条');
        }else{
            tipshow(data.info,'warn');
        }
        that.parent('.new_cap').hide();
    });;
});
//添加关键词
function rule_cap(that){//添加关键词弹框定位 
    $('.rule_add_cap').show();//显示添加关键词弹框
    $('.rule_add_cap').css({
        'left': that.offset().left- $('.rule_add_cap').width()/2 + that.width()/2 - 20,
        'top': that.offset().top + 35
    })
}
var types;//0是添加 1是编辑
var rule_index; //自动回复规则下标
$(document).on('click','.js_ad',function(){
    $('.rule_add_cap #saytext').val('');//清空弹框内容
    keywords_id = '';
    types = 0;
    rule_cap($(this));//关键词弹框显示
    //qq表情包
    emotion('.emotion','saytext');//表情包方法
    rule_index = $(this).parents('.handle_content').index('.handle_content');//自动回复规则下标
});
//编辑关键词
var keywords_index;//获得关键词下标
var keywords_id;//关键词唯一标识
$(document).on('click','.keywords .words',function(){
    //qq表情包
    emotion('.emotion','saytext');//表情包方法
    keywords_index = $(this).parent().index();
    keywords_id = $(this).siblings('.close_circle').data('id');
    types= 1;//表示编辑
    rule_index = $(this).parents('.handle_content').index('.handle_content');//自动回复规则下标
    rule_cap($(this));//关键词弹框显示
    var that = $(this);
    $('.rule_add_cap .cap_keywords input').val($(this).children('span:eq(0)').html());//修改关键词
    $('.rule_add_cap .cap_rule label').each(function() {
        if($(this).text() == that.children('span:eq(1)').text()){
            $(this).children().prop('checked',true);
        }
    });
});
$(document).on('click','.qqFace img',function(){
	$('.reply_cap_text').focus();
});
$(document).on('click','.btn_group .btn-default',function(){
	$(this).parents('.rule_add_cap').hide();
});
// 点击确定添加关键词
$(document).on('click','.btn_group .btn-primary',function(){
    var rule_id = $('.handle_content:eq('+rule_index+') .rule_add_keywords .js_ad').data('rule_id');//rule_id 的值
    var type = $(this).parent().siblings('.cap_rule').find('input:checked').val();//判断选中的是全匹配还是模糊
    var cap_keywords = $(this).parent().siblings('.cap_keywords').find('input').val();//输入的关键词
    var data = {
        id: keywords_id,
        rule_id : rule_id,
        keyword : cap_keywords,
        type    : type,
        _token  : _token
    }
    var _this = $(this);
    $.post('/merchants/wechat/replyKeywordAdd/3',data,function(data){
        if ( data.status == 1 ) {
            tipshow(data.info,'info');
            var add;
            if (_this.parent().siblings('.cap_rule').find('input').eq(0).prop('checked')) {
                add = '全匹配';
            } else{
                add = '模糊';
            }
            var str = _this.parent().siblings('.cap_keywords').find('input').val();
            var value = replace_em(str);
            if(types == 0){//types 0  添加 types 1  编辑
                if (_this.parents('.rule_add_keywords').siblings('.keywords_list').find('.keywords').length == 0) {
                    _this.parents('.rule_add_keywords').siblings('.left_info').hide();
                    _this.parents('.rule_add_keywords').siblings('.keywords_list').show();
                }
                var keywords = '<div class="keywords">'
                                +'<a class="close_circle" href="javascript:void(0)" data-id="'+data.data+'">x</a>'
                                +'<div class="words"><span class="value">'+value+'</span><span class="add">'+add+'</span></div>'
                            +'</div>';
                $('.handle_content:eq('+rule_index+')').find('.keywords_list').append(keywords);
                $('.handle_content:eq('+rule_index+')').find('.keywords_list .left_info').hide();
            }else{
                $('.handle_content:eq('+rule_index+') .keywords:eq('+keywords_index+') span:eq(0)').html(value);//编辑
                $('.handle_content:eq('+rule_index+') .keywords:eq('+keywords_index+') span:eq(1)').text(add);//编辑
            }
            _this.parent().siblings('.cap_keywords').find('input').val('');//清空弹框关键词
        } else {
            tipshow(data.info,'warn');
        }
        $('.rule_add_cap').hide();//隐藏添加关键词弹框   
        
        if ( data.url ) {
            window.location.href = data.url;
        }
        return false;
    });
});
//编辑回复框
var reply = '<div class="reply_cap">'
			+'<a class="close_circle" href="javascript:void(0)">x</a>'
			+'<div class="reply_ctl_cap">'
			+'<ul class="reply_cap_nav">'
			+'<li data-select="sele"><span class="emt">表情</span></li>'
			+'<li class="link_li" data-select="sele"><span>插入链接</span><div class="link"><input type="text" placeholder="http://"/><button class="btn btn-primary">确定</button></div></li>'
			+'<li><span class="js_showModel" data-toggle="modal" data-target="#myModal">选择图文</span></li>'
			+'<li>'
				+'<div class="dropdown">'
				   +'<span id="dropdownMenu1" data-toggle="dropdown">'
				   	 	+'其他'
				    	+'<span class="caret"style="color: #000;"></span>'
				 	+'</span>'
				  	+'<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">'
				    	+'<li role="presentation" data-toggle="modal" data-target="#myModal2"><a role="menuitem" tabindex="-1" href="javascript:void(0)">商品及分类</a></li>'
				    	+'<li role="presentation" data-toggle="modal" data-target="#myModal1"><a role="menuitem" tabindex="-1" href="javascript:void(0)">微页面及分类</a></li>'
				    	+'<li class="homepage" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)">店铺主页</a></li>'
				    	+'<li class="homepage" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)">会员主页</a></li>'
				    +'</ul>'
				+'</div>'
			+'</li>'
		+'</ul>'
		+'<div class="ctts">'
			+'<textarea id="reply_cap_text" class="reply_cap_text" rows="4" cols="50"></textarea>'
			+'<div class="add_news">'
				+'<div class="item">'
					+'<div class="img_text">'
						+'<span class="green">图文</span>'
						+'<a class="co_blue" href="javascript:void(0);">123456</a>'
					+'</div>'
					+'<div class="read_all clearfix">'
						+'<span>阅读全文</span>'
						+'<span class="pull-right">></span>'
					+'</div>'
				+'</div>'
			+'</div>'
		+'</div>'
		+'<div class="reply_cap_btn">'
			+'<button class="btn btn-primary">确定</button>'
			+'<span>还能输入300个字</span>'
		+'</div>'
	+'</div>'
+'</div>';
$(document).on('click','.reply_list .replay_edit',function(){
	$(this).before(reply);
	$(this).siblings('.reply_cap').find('textarea').val($(this).parents('.reply').find('.reply_text').text());
	$(function() {
		emotion('.emt','reply_cap_text');
	});
});
//右侧编辑
$(document).on('click','.reply_list .reply_cap_btn .btn-primary',function(){
	if($('.ctts textarea').css('display') != 'none'){
		var reply_text =replace_em($(this).parent().siblings('.ctts').children('textarea').val()) ;
		var add_text ='<span class="reply_type">文本</span><span class="reply_text">'+reply_text+'</span>'
							+'<div class="reply_opts">'
		   						+'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
		   						+'<span>-</span>'
		   						+'<a class="replay_delete" href="javascript:void(0)">清空</a>'
		   					+'</div>';
		$(this).parents('.reply').html(add_text);
	}else{
		var add_news = '<div class="img_text">'
								+'<span class="green">图文</span>'
								+'<a class="co_blue" href="'+$('.ctts .img_text a').attr('href')+'">'+$('.ctts .img_text a').text()+'</a>'
							+'</div>'
							+'<div class="reply_opts">'
		   						+'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
		   						+'<span>-</span>'
		   						+'<a class="replay_delete" href="javascript:void(0)">清空</a>'
		   					+'</div>';
		$(this).parents('.reply').html(add_news);
	}
	tipshow('更新回复成功','info');	
});
//右侧清空
$(document).on('click','.reply_list .replay_delete',function(){
	var add_text ='<span class="reply_type">文本</span><span class="reply_text">'+dateArr[$(this).parents('li').index()]+'</span>'
							+'<div class="reply_opts">'
		   						+'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
		   						+'<span>-</span>'
		   						+'<a class="replay_delete" href="javascript:void(0)">清空</a>'
		   					+'</div>';
	$(this).parents('.reply').html(add_text);
	tipshow('更新回复成功','info');
});
$(document).on('click','.reply_cap .close_circle',function(){
	$(this).parent().remove();
});
//字数控制
function fontCount(){
	$('.reply_cap_btn span').text('还能输入'+(300-$('.reply_cap_text').val().length)+'个字');
};
$(document).on('keyup','textarea',function(){
	fontCount();//字数统计
});

//插入链接
$(document).on('click','.link_li span',function(){
	$(this).siblings('.link').show();
});
$(document).on('click','.link_li .btn-primary',function(){
	var str = /^http:\/\//;
	var value = $(this).siblings('input').val();
	if (str.test(value)) {
		$(this).parents('.reply_cap_nav').siblings('.ctts').children('textarea').val($(this).parents('.reply_cap_nav').siblings('.ctts').children('textarea').val()+value);
		
	} else{
		value = 'http://'+value;
		$(this).parents('.reply_cap_nav').siblings('.ctts').children('textarea').val($(this).parents('.reply_cap_nav').siblings('.ctts').children('textarea').val()+value);
	}
	$(this).parent().hide();
	$('.reply_cap_text').focus();
	fontCount();//字数统计
});