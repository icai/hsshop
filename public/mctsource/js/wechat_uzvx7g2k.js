var _token =document.getElementsByTagName('meta')[4].content;//token值
$('body').click(function(event){
    var target = $(event.target);//触发的事件
    if(!target.hasClass('js_ad') && !target.hasClass('rule_add_cap') 
        && target.parents('.rule_add_cap').length === 0 ){
        $('.rule_add_cap').hide();
    }

})
//新建自动回复
$('.handle_title>button').click(function(){
	$('.handle_title>.new_cap').show();
});
$('.new_cap .btn-default').on('click',function(){
	$(this).parent('.new_cap').hide();
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
    types= 0;
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
    $('.rule_add_cap .cap_keywords input').val(replace_img($(this).children('span:eq(0)').html()));//修改关键词
    $('.rule_add_cap .cap_rule label').each(function() {
        if($(this).text() == that.children('span:eq(1)').text()){
            $(this).children().prop('checked',true);
        }
    });
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
        type    : 2
    }
    var _this = $(this);return false;
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
        
        if ( data.url ) {
            window.location.href = data.url;
        }
        return false;
    });
});
$(document).on('click','.qqFace img',function(){
	$('.reply_cap_text').focus();
});
$(document).on('click','.btn_group .btn-default',function(){//添加关键词取消
	$('.rule_add_cap').hide();//隐藏添加关键词弹框
    $(this).parent().siblings('.cap_keywords').find('input').val('');//清空弹框关键词
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
    _this.prop('disabled',true);
    $.post('/merchants/wechat/replyKeywordAdd/2',data,function(data){
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
        _this.prop('disabled',false);
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
			+'<li data-select="sele"><span class="emt" >表情</span></li>'
			+'<li class="link_li" data-select="sele"><span>插入链接</span><div class="link"><input type="text" value="http://"/><button class="btn btn-primary">确定</button></div></li>'
//			+'<li><span>音乐</span></li>'
			+'<li class="js_news"><span class="js_showModel" >选择图文</span></li>'
			+'<li>'
				+'<div class="dropdown">'
				   +'<span id="dropdownMenu1" data-toggle="dropdown">'
				   	 	+'其他'
				    	+'<span class="caret"style="color: #000;"></span>'
				 	+'</span>'
				  	+'<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">'
				    	+'<li class="js_product" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)">商品及分类</a></li>'
				    	+'<li class="js_smallPage" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)">微页面及分类</a></li>'
				    	+'<li class="homepage js_shop" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)">店铺主页</a></li>'
				    	+'<li class="homepage js_members" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)">会员主页</a></li>'
//				   		+'<li class="homepage js_kefu" role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)">客服</a></li>'
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

var edit_reply_id;//编辑回复的标识
$(document).on('click','.reply_list .replay_edit',function(){
    $(this).before(reply);
    edit_reply_id = $(this).parents('.reply').data('id');//获取当前回复的标识
    if($(this).parents('.reply').find('.reply_text').length>0){//判断类型显示数据   本文
        $('.reply_cap .ctts').children().hide();
        $('.ctts .reply_cap_text').show();
        $(this).siblings('.reply_cap').find('textarea').val(replace_img($(this).parents('.reply').find('.reply_text').html()));
    }else if($(this).parents('.reply').children('img').length>0){  //图片
        $('.ctts .imgs img').attr('src',$(this).parents('.reply').children('img').attr('src'));
        $('.reply_cap .ctts').children().hide();
        $('.ctts .imgs').show();
    }else{              
        $('.reply_cap .ctts').children().hide();
        $('.ctts .add_news').show();
        $('.ctts .img_text a').attr('href',$(this).parents('.reply').find('a:first').attr('href'));
        $('.ctts .img_text a').text($(this).parents('.reply').find('a:first').text());
    }
    $(function() {
        emotion('.emt','reply_cap_text');//表情包方法
    });
});

$(document).on('click','.reply_cap .close_circle',function(){
	$(this).parent().remove();
});
//删除回复
$(document).on('click','.replay_delete',function(){
    /*type 1 关键词自动回复  2 关注后自动回复  3  每周回复
    *id 该回复的唯一标识
    *rule_id 规则唯一标识
    */
    var _that = $(this);
    var id = $(this).parents('li').data('id');
    var rule_id = $(this).parents('.rule_right').data('rule_id');
    var data = {
        id: id,
        type: 2,
        rule_id: rule_id,
        _token:_token
    };
    $.post('/merchants/wechat/replyContentDel',data,function(data){
        console.log(data)
       if(data.status == 1){
            if (_that.parents('.reply_list').children().length == 2) {
                _that.parents('.reply_list').children('.right_info').show();
            }
            _that.parents('.reply').remove();
        }
        tipshow(data.info,'info');
    })
});
// 页面加载直接切换表情
$('.words .value').each(function(){
    $(this).html(replace_em($(this).text()));//表情切换
});
$('.reply_list .reply_text').each(function(){
    $(this).html(replace_em($(this).text()));//表情切换
});
/*
*参数从左到右 自动回复对象的标识 表情图片等类型 发送的内容 成功后需要添加的内容 当前函数上下文
*             商品或图文唯一标识  其他中的类型
* type: 1 文本 2 图片 3语音 4 音乐 5 图文 6 其他
* content_type: 1商品 2微页面 3店铺主页 4 会员主页
* id   编辑时传 id 
 */
function replyAjax(rule_id,type,configs,that,id,content_id,content_type){
    var data;
    switch (type)
    {
        case 1://文本
            data = {
                rule_id: rule_id,//改自动回复唯一标识
                type: type,//判断表情图片图文等
                content_content: configs,//文本内容
                id: id,//编辑时传id
                _token: _token
            }
        break;
        case 2://图片
            data = {
                rule_id: rule_id,//改自动回复唯一标识
                type: type,
                content_url: configs,//普通图片
                content_media_id: '',//通过素材管理接口上床多媒体文件 得到的id
                id: id,//编辑时传id
                _token: _token
            }
        break;
        case 3://图文
            data = {
                rule_id: rule_id,//改自动回复唯一标识
                type: type,
                content_type: content_type,//1 微信图文 2 高级图文
                content_id: content_id,//图文标识
                content_title: configs,//图文title
                id: id,//编辑时传id
                _token: _token
            }
        break;
        case 4://语音
            data = {
                rule_id: rule_id,//改自动回复唯一标识
                type: type,
                content_url: configs,
                content_media_id: '',
                id: id,//编辑时传id
                _token: _token
            }
        break;
        case 5://音乐
            data = {
                rule_id: rule_id,//改自动回复唯一标识
                type: type,
                content_title: configs,
                content_desc: configs,
                content_img: configs,
                content_normal: configs,
                content_hd: configs,
                content_media_id: configs,
                id: id,//编辑时传id
                _token: _token
            }
        break;
        case 6://其他
            data = {
                rule_id: rule_id,//改自动回复唯一标识
                type: type,
                content_id: content_id,//产品id
                content_title: configs,//产品title
                content_type: content_type,//其他类型
                id: id,//编辑时传id
                _token: _token
            }
        break;
    }
    $.post('/merchants/wechat/replyContentAdd/2',data,function(data){
        if(data.status == 1){
            configs = replace_em(configs);
            if(id == ''){//id为空  表示添加
                switch (type)
                {
                    case 1://文本
                        var add_text = '<li class="reply" data-id = '+data.data+'>'
                                +'<span class="reply_type">文本</span><span class="reply_text">'+configs+'</span>'
                                +'<div class="reply_opts">'
                                    +'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
                                    +'<span>-</span>'
                                    +'<a class="replay_delete" href="javascript:void(0)">删除</a>'
                                +'</div>'
                            +'</li>';
                    break;
                    case 2://图片
                        var add_text = '<li class="reply" data-id = '+data.data+'>'
                                +'<img class="images" src="'+$('.ctts .imgs img').attr('src')+'"/>'
                                +'<div class="reply_opts">'
                                    +'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
                                    +'<span>-</span>'
                                    +'<a class="replay_delete" href="javascript:void(0)">删除</a>'
                                +'</div>'
                            +'</li>';
                    break;
                    case 3://图文
                        var add_text = '<li class="reply" data-id = '+data.data+'>'
                                +'<div class="img_text">'
                                    +'<span class="green">图文</span>'
                                    +'<a class="co_blue" href="'+$('.ctts .img_text a').attr('href')+'">'+$('.ctts .img_text a').text()+'</a>'
                                +'</div>'
                                +'<div class="reply_opts">'
                                    +'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
                                    +'<span>-</span>'
                                    +'<a class="replay_delete" href="javascript:void(0)">删除</a>'
                                +'</div>'
                            +'</li>';
                    break;
                    case 4://语音
                        console.log('此功能后续开发');
                    break;
                    case 5://音乐
                        console.log('此功能后续开发');
                    break;
                    case 6://其他
                        var text= '图文';
                        switch (content_type)
                        {
                            case 1://商店
                               text = '商品';
                            break;
                            case 2://商店分类
                               text = '商店分组';
                            break;
                            case 3://微页面
                                text = '微页面';
                            break;
                            case 4://微页面分类
                                text = '微页面分类';
                            break;
                            case 5://店铺
                                text = '店铺主页';
                            break;
                            case 6://主页
                                text = '会员主页';
                            break;
                        }
                        var add_text = '<li class="reply" data-id = '+data.data+'>'
                                +'<div class="img_text">'
                                    +'<span class="green">'+text+'</span>'
                                    +'<a class="co_blue" href="'+$('.ctts .img_text a').attr('href')+'">'+$('.ctts .img_text a').text()+'</a>'
                                +'</div>'
                                +'<div class="reply_opts">'
                                    +'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
                                    +'<span>-</span>'
                                    +'<a class="replay_delete" href="javascript:void(0)">删除</a>'
                                +'</div>'
                            +'</li>';
                    break;
                }
                if (that.parents('.rule_add_reply').siblings('.reply_list').find('.reply').length == 0) {
                    console.log(1)
                    // that.parents('.rule_add_reply').siblings('.reply_list').show();
                    that.parents('.rule_add_reply').siblings('.reply_list').find('.right_info').hide();
                }
                that.parents('.rule_add_reply').siblings('.reply_list').append(add_text);
            }else{
                switch (type)
                {
                    case 1://文本
                       var add_text ='<span class="reply_type">文本</span><span class="reply_text">'+configs+'</span>'
                            +'<div class="reply_opts">'
                                +'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
                                +'<span>-</span>'
                                +'<a class="replay_delete" href="javascript:void(0)">删除</a>'
                            +'</div>';
                    break;
                    case 2://图片
                        var add_text = '<img class="images" src="'+$('.ctts .imgs img').attr('src')+'"/>'
                            +'<div class="reply_opts">'
                                +'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
                                +'<span>-</span>'
                                +'<a class="replay_delete" href="javascript:void(0)">删除</a>'
                            +'</div>';
                    break;
                    case 3://图文
                        var add_text = '<div class="img_text">'
                                +'<span class="green">图文</span>'
                                +'<a class="co_blue" href="'+$('.ctts .img_text a').attr('href')+'">'+$('.ctts .img_text a').text()+'</a>'
                            +'</div>'
                            +'<div class="reply_opts">'
                                +'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
                                +'<span>-</span>'
                                +'<a class="replay_delete" href="javascript:void(0)">删除</a>'
                            +'</div>';
                    break;
                    case 4://语音
                        console.log('此功能后续开发');
                    break;
                    case 5://音乐
                        console.log('此功能后续开发');
                    break;
                    case 6://其他
                        var text= '图文';
                        switch (content_type)
                        {
                             case 1://商店
                               text = '商品';
                            break;
                            case 2://商店分类
                               text = '商店分组';
                            break;
                            case 3://微页面
                                text = '微页面';
                            break;
                            case 4://微页面分类
                                text = '微页面分类';
                            break;
                            case 5://店铺
                                text = '店铺主页';
                            break;
                            case 6://主页
                                text = '会员主页';
                            break;
                        }
                         var add_text = '<div class="img_text">'
                                +'<span class="green">'+text+'</span>'
                                +'<a class="co_blue" href="'+$('.ctts .img_text a').attr('href')+'">'+$('.ctts .img_text a').text()+'</a>'
                            +'</div>'
                            +'<div class="reply_opts">'
                                +'<a class="replay_edit" href="javascript:void(0)">编辑</a>'
                                +'<span>-</span>'
                                +'<a class="replay_delete" href="javascript:void(0)">删除</a>'
                            +'</div>';
                    break;
                }
                edit_reply.html(add_text);
            }
            tipshow(data.info,'info');
            that.parents('.reply_cap').remove();
        }else{
            tipshow(data.info,'warn');
            return false;
        }
        that.prop('disabled',false);
    },'json');
}
var edit_reply;//编辑选中回复
// 点击回复框确定按钮 实现添加回复
$(document).on('click','.reply_cap_btn .btn-primary',function(){
    var _that = $(this);
    _that.prop('disabled',true);
    edit_reply = _that.parents('.reply');
    //  判断要添加的是文本图片还是图文
    var rule_id = _that.parents('.rule_right').data('rule_id');//自动回复的对象id
    if($('.ctts textarea').css('display') != 'none'){
        var reply_text =replace_em($(this).parent().siblings('.ctts').children('textarea').val()) ;
        var config = $(this).parent().siblings('.ctts').children('textarea').val()//解析qq表情之前的数据
        replyAjax(rule_id,1,config,_that,edit_reply_id);//进行交互
        
    }else{
        var content_id = $('.ctts .add_news .item').data('id');
        config = $('.ctts .img_text>a').text();
       if( picOrOther ){
            replyAjax(rule_id,3,config,_that,edit_reply_id,content_id,cType);//图文交互
       }else{
        	replyAjax(rule_id,6,config,_that,edit_reply_id,content_id,cType);
       }
       
    }
    
});
//添加回复框
$(document).on('click','.rule_add_reply >a',function(){
    edit_reply_id = ''; //回复时清空id
    $(this).before(reply);
    $(function() {
        emotion('.emt','reply_cap_text');//表情包方法
    });
});
//字数控制
function fontCount(){
	$('.reply_cap_btn span').text('还能输入'+(300 - $('.reply_cap_text').val().length)+'个字');
}
$(document).on('input','textarea',function(){
	fontCount();//字数统计
});

// //插入链接
$(document).on('click','.link_li span',function(){
	$(this).siblings('.link').show();
});
$(document).on('click','.link_li .btn-primary',function(){
	// var str = /^http:\/\//;
    var value = $(this).siblings('input').val();
    // if (!str.test(value)) {
    //     value = 'http://'+value;
    // } 
    if(cap_text!=''){ 
        var str = $(this).parents('.reply_cap_nav').siblings('.ctts').children('textarea').val();
        
        $(this).parents('.reply_cap_nav').siblings('.ctts').children('textarea').val(str.replace(cap_text,'<a class="co_blue" href="'+value+'" target="_blank">'+cap_text+'</a>'))
        console.log(str)
        cap_text=''
    }else{
        $(this).parents('.reply_cap_nav').siblings('.ctts').children('textarea').val($(this).parents('.reply_cap_nav').siblings('.ctts').children('textarea').val()+value);
    }
    $(this).parent().hide();
    $('.reply_cap_text').focus();
    fontCount();
});
// 新增文本域选中添加链接功能
var cap_text ='';
$(document).on('select','.reply_cap_text',function(){
    cap_text = window.getSelection().toString();//获取文本域选中的文本
})