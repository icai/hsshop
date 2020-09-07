var picTextType =false;//false 默认单条图文

//模态框居中控制
$('.modal').on('shown.bs.modal', function (e) { 
  	// 关键代码，如没将modal设置为 block，则$modala_dialog.height() 为零 
  	$(this).css('display', 'block'); 
  	var modalHeight=$(window).height() / 2 - $(this).find('.modal-dialog').height() / 2; 
  	if(modalHeight < 0){
  		modalHeight = 0;
  	}
  	$(this).find('.modal-dialog').css({ 
    	'margin-top': modalHeight 
 	}); 
});
$('body').on('click','.pop',function(event){
	$('.pop').not($(this)).removeClass('active');
	$(this).toggleClass('active');
	$('.popover').hide();
	$('.'+$(this).data('toggle')).css({
		'top':$(this).offset().top - $('.'+$(this).data('toggle')).height()/2 +7,
		'left':$(this).offset().left - $('.'+$(this).data('toggle')).width() - 10,
	});
	$('.'+$(this).data('toggle')).show();
});
//给Body加一个Click监听事件
$('body').on('click', function(event) {
	var target = $(event.target);
	if (!target.hasClass('active')
			&& target.parents('.popover').length === 0
	        && target.parents('.active').length === 0) {
	        //弹窗触发列不关闭，否则显示后隐藏
	    popoverHidden();
	}
});
function popoverHidden(){//使用此方法必须用此方法隐藏popover
	$('.popover').hide();
	$('.pop').removeClass('active');
}
//------------自动回复模块-------------
//表情包方法
var imgStr= ['::)','::~','::B','::|','::<','::$','::X','::Z','::’(','::-|','::@','::P','::D','::O','::(',
			'::-b','::Q','::T',':,@P',':,@-D','::d',':,@o','::g',':|-)','::!','::L','::>','::,@',':,@F','::-S',
			':?',':,@x',':,@@','::8',':,@!',':xx',':bye',':wipe',':dig',':&-(',':B-)',':<@',':@>','::-O',':>-|',
			':P-(','::’|',':X-)','::*',':@x',':8*',':hug',':moon',':sun',':bome',':!!!',':pd',':pig',':<W>',':coffee',
			':eat',':heart',':strong',':weak',':share',':v',':@)',':jj',':ok',':no',':rose',':fade',':showlove',':love',':<L>'
];
//字符转化为表情
function replace_em(str) {
	for(var i = 0;i < imgStr.length;i ++){
		str = str.replace('/'+imgStr[i], '<img src="/'+'mctsource/images/arclist/'+(i+1)+'.gif" border="0" />');
	}
	return str;
};
//表情转化字符
function replace_img(str){
    var reg = new RegExp('<img src="\/mctsource\/images\/arclist\/([0-9]+)\.gif" border="0">','g')
    str=str.replace(reg,function(m,p1){
        return '/'+imgStr[parseInt(p1)-1];
    }); 
    return str;
};
function emotion(className,idName){
	$(function() {
		$(className).qqFace({
			id: 'facebox',
			assign: idName,
			path: domain_url+'mctsource/images/arclist/' //表情存放的路径
		});
		
	});
};
//-------------各种弹框触发事件开始-------------
//回复弹框nav标题栏点击更改内容写入区
$(document).on('click','[data-select="sele"]',function(){
	$('.reply_cap .ctts').children().hide();
	$('.ctts .reply_cap_text').show();
});

//选择弹框若是没内容显示info
$(document).on('click','.js_showModel',function(){
//	判断当前模态框有几个tbody大于一就隐藏info
	if($(''+$(this).attr('data-target')+' tbody').length > 1){
		$(''+$(this).attr('data-target')+' .tabel_info').hide();
	}
    menu_num = $(this).parents('.menu').index();
    ctl_linkTo = $(this).parents('.menu').find('.zx').index();
    if ($(this).parents('.menu').find('.second ul li').length == 0) {
        ctl_linkTo = -1;
    }
});
//模态框点击切换
//刮刮乐
$('.modal .group1').on('click',function(){
	var _index = $(this).index('.modal .group1');
	$('.group1').removeClass('list_active');
	$(this).addClass('list_active');
	$('.group2').hide();
	$('.group2').eq($(this).index()).css({
		'display':'inline-block',
		'border':0	
	});
	$(this).parents('.modal').find('tbody').hide();//隐藏所有的tbody
	$(this).parents('.modal').find('tbody:eq('+_index+')').show();//显示当前的
	$(this).parents('.modal').find('.modal-footer').hide();//隐藏所有的footer
	$(this).parents('.modal').find('.modal-footer:eq('+_index+')').show();//显示当前的
});
var _picText = 11;//默认高级图文  type值
var _product = 1;//默认已上架商品
var _small = 3;//默认微页面
//切换模态框导航事件
$('.js_item').on('click',function(){
	$('.js_manage').css({
		'display':'inline-block',
		'border':'0'
	});
	$('.js_link').hide();
	$(this).siblings().removeClass('list_active');
	$(this).addClass('list_active');
	switch ($(this).parents('.modal').attr('id'))
    {
        case 'myModal':
			pictAjax(10);//调用图文素材交互方法 10微信图文  传递类型
			_picText = 10;
        break;
        case 'myModal2':
           	proAjax(2);//调用商品交互方法 1已上架商品  传递类型
			_product = 2;
			cType = 2;
            pt_type = 2;
        break;
        case 'myModal1'://微页面
           	smallAjax(4);//调用微页面交互方法 3微页面  传递类型
			_small = 4;
			cType = 4;
            pt_type = 4;
        break;
    }
});
$('.js_small').on('click',function(){
	$('.js_link').css({
		'display':'inline-block',
		'border':'0'
	});
	$('.js_manage').hide();
	$(this).siblings().removeClass('list_active');
	$(this).addClass('list_active');
	switch ($(this).parents('.modal').attr('id'))
    {
        case 'myModal'://图文
			pictAjax(11);//调用图文素材交互方法 11高级图文  传递类型
			_picText = 11;
        break;
        case 'myModal2'://商品
           	proAjax(1);//调用商品交互方法 2商品分组  传递类型
			_product = 1;
        break;
        case 'myModal1'://微页面
           	smallAjax(3);//调用微页面交互方法 4微页面分组  传递类型
			_small = 3;
        break;
        
    }
});
// 商品搜索
$('#myModal2 thead .btn').click(function() {
	var _val = $(this).siblings('input').val();//搜索框的值
    if(_product == 1){
    	proAjax(1,_val);//搜索商品
    }else{
    	proAjax(2,_val);//搜索商品分组
    }
});
// 图文搜索
$('#myModal thead .btn').click(function() {
	var _val = $(this).siblings('input').val();//搜索框的值
    if(_picText == 10){
    	pictAjax(10,_val);//搜索微信图文
    }else{
    	pictAjax(11,_val);//搜索高级图文
    }
});
//----------------图片模态框点击事件---------------
$(document).on('click','.js_img',function(){
    $.get('/merchants/myfile/getClassify',function(data){
        $('.category-list').empty();
        classifyId = data.data[0].id;//默认分组
        var _group = '';
        for( var i = 0;i < data.data.length;i++ ){
            if (i == 0){
                _group += '<li class="js-category-item active" data-id="'+data.data[i].id+'">'+data.data[i].name+'\
                            <span>'+data.data[i].number+'</span>\
                        </li>';
            }else{
                _group += '<li class="js-category-item" data-id="'+data.data[i].id+'">'+data.data[i].name+'\
                            <span>'+data.data[i].number+'</span>\
                        </li>';
            }
        }
        if(i == data.data.length){
            $('.category-list').append(_group);
        }
    });
    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,_token:_token},function(data){//默认第一组
        getPicture(data);
        $('.picturePage').extendPagination({
            totalCount: data.data[0].total,
            showCount: data.data[0].last_page,
            limit: data.data[0].per_page,
            
        });
    });
    $('#myModal-adv').modal('show');
});
// 数据请求成功后执行方法
function getPicture(data){
    $('.attachment-list-region .image-list').empty();//先清空所有的元素
    var _img_item= '';
    var _imgType;
    var _img_size;
    for ( var i = 0;i < data.data[0].data.length;i++ ){
        _img_size=data.data[0].data[i].FileInfo.img_size;//获取图片尺寸信息
        _imgType = data.data[0].data[i].FileInfo.type.slice(data.data[0].data[i].FileInfo.type.lastIndexOf('/')+1)
        _img_item +='<li class="image-item">\
            <img class="image-box" data-size="'+_img_size+'" src="/'+data.data[0].data[i].FileInfo.path+'" />\
            <div class="image-meta"></div>\
            <div class="image-title">'+data.data[0].data[i].FileInfo.name+'.'+_imgType+'</div>\
            <div class="attachment-selected no">\
                <i class="icon-ok icon-white"></i>\
            </div>\
        </li>';
    }
    if(i == data.data[0].data.length){
        $('.attachment-list-region .image-list').append(_img_item);
    }
}
$('.modal .attachment-pagination').on('click','.picturePage .pagination li a', function(event) {
    var page = $(this).text()//下标切换页码数
    if(!parseInt(page) && $(this).parent().index() == 0){
        page =  $('.picturePage .pagination .active').text();
    }else if(!parseInt(page) && $(this).parent().index() != 0){
        page =  parseInt($('.picturePage .pagination .active').text());
    }else if($(this).parents('li').hasClass('disabled')){
        return false;
    }
    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,_token:_token,page:page},function(data){
        getPicture(data);
    });
});
$(document).on('click','.js-category-item',function(){
    $('.js-category-item').removeClass('active');
    $(this).addClass('active');
    classifyId = $(this).data('id');
    if($(this).children('span').text() == 0){console.log(1)
        $('.imgData').hide();
        $('#layerContent_right').show();
    }else{
        $('.imgData').show();
        $('#layerContent_right').hide();
    }
    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,'_token':_token},function(data){//默认第一组
        getPicture(data);
        $('.picturePage').extendPagination({
            totalCount: data.data[0].total,
            showCount: data.data[0].last_page,
            limit: data.data[0].per_page
        });
    });
});


var pictureSrc;
//点击图片标题切换
$('.category-list li').on('click',function(){
	$('.category-list li').removeClass('active');
	$(this).addClass('active');
	$('.attachment-list-region').hide();
	$('.attachment-list-region').eq($(this).index()).show();
});
//点击上传图片切换
$('.js-show-upload-view, .js_addImg').on('click',function(){
	$('.content_first').hide();
	$('.content_second').show();
	$('.myModal-adv .modal-body').addClass('height_auto');
	$('.myModal-adv .module-nav').hide();
	$('.myModal-adv .cap_head').show();
	myupload();
});
$('.js-show-upload-view, .js_newImg').on('click',function(){
	$('.content_first').hide();
	$('.content_second').show();
	$('.myModal-adv .modal-body').addClass('height_auto');
	myupload();
});
//点击选择图片切换
function modalChange(obj){
	$(obj).hide();
	$('.content_first').show();
	$('.myModal-adv .modal-body').removeClass('height_auto');
	$('.myModal-adv .cap_head').hide();
	$('.myModal-adv .module-nav').show();
}
$('.js_prev').on('click',function(){
	modalChange('.content_second');
});
//点击我的图片切换
$('.js-modal-tab').on('click',function(){
	modalChange('.content_third');
});
//点击图片库切换到图片库
$('.asian').click(function(){
	$('.content_first').hide();
	$('.content_third').show();
	$('.myModal-adv .modal-body').addClass('height_auto');
});

//点击x后关闭模态框
$('.cap_head span').on('click',function(){
	$(this).parents('.cap').hide();
});
//点击删除 隐藏添加的图片元素
$(document).on('click','.imgs span',function(){
	$(this).parent().hide();
});
//内容一选择图片显示边框
$(document).on('click','.imgData .image-item',function(){
	$(this).siblings('li').children('.attachment-selected').addClass('no');
	$(this).children('.attachment-selected').removeClass('no');
	$(this).parents('.modal-content').find('.modal-footer .js-confirm').hide();
	$(this).parents('.modal-content').find('.modal-footer .ui-btn-primary').removeClass('no');
	pictureSrc = $(this).children('.image-box').attr('src');
	pictureSize = $(this).children('.image-box').attr('data-size');
});
//内容三选择图片样式点击切换
$('#iconStyleSelect li a').on('click',function(){
	$(this).parent().children().removeClass('selected');
	$(this).addClass('selected');
});

//内容三选择图片显示边框
$("#iconImgSelect li").click(function(){
	for (var i=0; i<$("#iconImgSelect li").length; i++) {
		$("#iconImgSelect li:eq("+i+") .attachment-selected").addClass('no');
	}
	$(this).children('.attachment-selected').removeClass('no');
	$(this).parents('.modal-content').find('.modal-footer .js-confirm').hide();
	$(this).parents('.modal-content').find('.modal-footer .ui-btn-primary').removeClass('no');
    pictureSrc = $(this).children('img').attr('src');
    pictureSize = $(this).children('.image-box').attr('data-size');
});
var page_type =0;//0 为自动回复模块  1为图文素材模块 2代表自定义菜单模块
//-----------图文等模态框点击选取添加回复-----------
$(document).on('click','.modal .modal-body tbody .btn-default',function(){
    var _this = $(this);
    switch (page_type)
    {
        case 0:
            var content_id = $(this).parents('tr').data('id');
            $('.ctts .item').data('id',content_id);//将产品图文id赋值
            $('.reply_cap .ctts').children().hide();
            $('.ctts .add_news').show();
            $('.ctts .img_text a').attr('href',$(this).parents('tr').find('a:first').attr('href'));
            $('.ctts .img_text a').text($(this).parents('tr').find('a:first').text());
        break;
        case 1:
            $('.outer_link').css("display",'inline-block');
            $('.outer_link').text($(this).parents('tr').find('a:first').text());
            $('.outer_link').attr('href',$(this).parents('tr').find('a:first').attr('href'));
            $('#menu1').text('修改');
            if(picTextType){//多条图文
                $('.left_content .list:eq('+list_index+') .cover_href').attr('href',$(this).parents('tr').find('a:first').attr('href'));
                $('.left_content .list:eq('+list_index+') .cover_href').text($(this).parents('tr').find('a:first').text());
            }
        break;
        case 2:
            var text= '图文';
            switch (cType)
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
//              case 7://客服
//                  text = '客服';
//              break;
                case 14://营销活动
                    text = '营销活动';
                break;
            }
            var data;
            if(ctl_linkTo == -1){//一级菜单修改内容
                data = {
                    'id': $('.menu').eq(menu_num).data('id'),
                    'value':_this.parents('tr').find('a:first').attr('href'),
                    'content':{
                        'content_id':_this.parents('tr').data('id'),
                        'content_title': _this.parents('tr').find('a:first').text(),
                        'type': pt_type
                    }
                };
                if(cType == 14){
                    data.activityType = $("#activeModal .list_active").data("activity");
                }
            }else{//二级菜单修改内容
                data = {
                    'id': $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid'),
                    'parent_id': $('.menu').eq(menu_num).data('id'),
                    'value':_this.parents('tr').find('a:first').attr('href'),
                    'content':{
                        'content_id':_this.parents('tr').data('id'),
                        'content_title': _this.parents('tr').find('a:first').text(),
                        'type': pt_type
                    }
                };

                if(cType == 14){
                    data.activityType = $("#activeModal .list_active").data("activity");
                }
            }
            $.ajax({
                type : "get", 
                url : "/merchants/wechat/save_menu", 
                dataType : "json",
                data: data,
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
        break;
    }
    $('.js_linkAdress').remove();           
    $('.modal').modal('hide');
});
var picOrOther= true;//true为图文  false为其他
var kefu_type= '';//客服
var cType = 0;//其他中类型  1 商品 2 微页面  3 店铺主页 4 会员主页 5 客服
//点击下路框下的主页后获取他的链接和内容
/*
*点击链接向后台发送数据
 */
/*
*新增签到动能
*签到type 11 
*/
$(document).on('click','.homepage',function(){
    menu_num = $(this).parents('.menu').index();
    ctl_linkTo = $(this).parents('.menu').find('.zx').index();
    if ($(this).parents('.menu').find('.second ul li').length == 0) {
        ctl_linkTo = -1;
    }
	var _that = $(this);
	if($(this).hasClass('js_shop')){
		//店铺主页交互
		var data = {
	       type: 5,//参数类型
	       wid: $('#wid').val(),//页面标志
	       page:1,
	    };
	    picOrOther = false;
	   	cType = 5;
        pt_type = 5;
	}else if($(this).hasClass('shop_cat')||$(this).hasClass('sign')){//购物车和签到交互
        if($(this).hasClass('shop_cat')){
            var content_title = '购物车';
            cType = 9;
            pt_type = 9;
        }else{
            var content_title = '签到';
            cType = 11;
            pt_type = 11;
        }
        picOrOther = false;
        if(ctl_linkTo == -1){//一级菜单修改内容
            data = {
                'id': $('.menu').eq(menu_num).data('id'),
                'value':_that.data('href'),
                'content':{
                    'content_id':0,
                    'content_title': content_title,
                    'type': cType
                }
            };
        }else{//二级菜单修改内容
            data = {
                'id': $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid'),
                'parent_id': $('.menu').eq(menu_num).data('id'),
                'value':_that.data('href'),
                'content':{
                    'content_id':0,
                    'content_title': content_title,
                    'type': cType
                }
            };
        }
        $.ajax({
            type : "get", //jquey是不支持post方式跨域的
            // async:false,
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
        return false;
    }else if($(this).hasClass('js_kefu')){
		//客服主页交互
		var data = {
	       type: 15,//参数类型
	       wid: $('#wid').val(),//页面标志
	       page:1,
	    };
	    picOrOther = false;
	    kefu_type = 15;
	   	cType = 5;
        pt_type = 5;
	}else{
		//会员主页交互
		var data = {
	       type: 6,//参数类型
	       wid: $('#wid').val(),//页面标志
	       page:1,
	    };
	    picOrOther = false;
	    cType = 6;
        pt_type = 6;
	}

    $.get('/merchants/linkTo/get',data,function(data){
        console.log(data.data.id);
        if(!data.data.id){//没有进行设置
            tipshow('请设置后再进行','info');
            return false;
        }
        if(data.status == 1){
            switch (page_type)
            {
                case 0:
                    $('.ctts .add_news .item').data('id',data.data.id);//成功后为元素添加唯一标识
                    $('.reply_cap .ctts').children().hide();
                    $('.ctts .add_news').show();
                    $('.ctts .img_text a').attr('href',data.data.url);
                    $('.ctts .img_text a').text(data.data.page_title?data.data.page_title:data.data.home_name||"会员主页");
                break;
                case 1:
                    $('.outer_link').css("display",'inline-block');
                    $('.outer_link').text(data.data.page_title?data.data.page_title:data.data.home_name||"会员主页");
                    $('.outer_link').attr('href',data.data.url);
                    $('#menu1').text('修改');
                    if(picTextType){//多条图文
                        $('.left_content .list:eq('+list_index+') .cover_href').attr('href',data.data.url);
                        $('.left_content .list:eq('+list_index+') .cover_href').text(data.data.page_title?data.data.page_title:data.data.home_name);
                    }
                break;
                case 2:
                    var text= '图文';
                    switch (cType)
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
//                      case 7://客服
//                          text = '客服';
//                      break;
                    }
                    var data;
                    if(ctl_linkTo == -1){//一级菜单修改内容
                        data = {
                            'id': $('.menu').eq(menu_num).data('id'),
                            'value':data.data.url,
                            'content':{
                                'content_id':data.data.id,
                                'content_title': data.data.page_title?data.data.page_title:data.data.home_name,
                                'type': cType
                            }
                        };
                    }else{//二级菜单修改内容
                        data = {
                            'id': $('.menu:eq('+menu_num+') .second ul li:eq('+ctl_linkTo+')').data('linkid'),
                            'parent_id': $('.menu').eq(menu_num).data('id'),
                            'value':data.data.url,
                            'content':{
                                'content_id':data.data.id,
                                'content_title': data.data.page_title?data.data.page_title:data.data.home_name,
                                'type': cType
                            }
                        };
                    }
                    $.ajax({
                        type : "get", //jquey是不支持post方式跨域的
                        // async:false,
                        url : "/merchants/wechat/save_menu", //跨域请求的URL
                        dataType : "json",
                        data:data,
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
                break;
            }
            $('.js_linkAdress').remove();   
        }
    });
    // return false;
});

//商品交互
$(document).on('click','.js_product',function(){
    menu_num = $(this).parents('.menu').index();
    ctl_linkTo = $(this).parents('.menu').find('.zx').index();
    if ($(this).parents('.menu').find('.second ul li').length == 0) {
        ctl_linkTo = -1;
    }
    proAjax(_product);//调用商品交互方法   
    that_modal = $('#myModal2');//modal对象赋值
    cType = 1;
    pt_type = 1;
    picOrOther = false;
});
// 微页面交互
$(document).on('click','.js_smallPage',function(){
    menu_num = $(this).parents('.menu').index();
    ctl_linkTo = $(this).parents('.menu').find('.zx').index();
    if ($(this).parents('.menu').find('.second ul li').length == 0) {
        ctl_linkTo = -1;
    }
    smallAjax(_small);//调用微页面交互方法   
    that_modal = $('#myModal1');//modal对象赋值
    cType = 3;
    pt_type = 3;
    picOrOther = false;
});
//添加回复操作
//图文素材交互
var pt_type;
var that_modal;//要展示的modal对象
function pictAjax(types,title){//类型 搜索值
    types == 10? cType = 1: cType = 2;//1 标识微信图文  2 高级图文
    types == 10? pt_type = 7: pt_type = 8;//7 标识微信图文  8 高级图文
    var title = arguments[1]?arguments[1]:'';
    modelDataGet(types,success,title);//type11高级图文 10微信图文 suc成功后执行的方法
    function success(data){
        $('.myModalPage').extendPagination({
            totalCount: data.data[0].total,
            showCount: data.data[0].last_page,
            limit: data.data[0].per_page,
            callback: function (curr, limit, totalCount) {
                $('.modal .modal-footer').off().on('click', '.myModalPage .pagination li a', function(event) {
                    var page = $(this).text()//下标切换页码数
                    if(!parseInt(page)&& $(this).parent().index() == 0){
                        page =  $('.myModalPage .pagination .active').text();
                    }else if(!parseInt(page)&& $(this).parent().index() != 0){
                        page =  parseInt($('.myModalPage .pagination .active').text());
                    }else if($(this).parents('li').hasClass('disabled')){
                        return false;
                    }
                    modelDataGet(types,successBase,title,page);
                });
            }
        });
        function successBase(data){//交互成功后执行的基础方法 用于分页
            var data = data.data[0].data;
            $('#myModal tbody').empty();//先清空模态框原数据
            for (var i = 0; i < data.length; i++) {
                var _child = '';
                if(data[i].type == 1){
                    _child+='<div class="read_all clearfix">\
                        <a class="jump" href="'+data[i].url+'" target="_blank">\
                        <span>阅读全文</span>\
                        <span class="pull-right">></span>\
                        </a>\
                        </div>';
                }else{
                    for (var j = 0; j < data[i]._child.length; j++) {//每个高级图文的子图文
                        _child+='<div class="read_all clearfix" data-id="'+data[i]._child[j].id+'">\
                                <span class="green">图文</span>\
                                <a class="co_blue" href="'+data[i]._child[j].href+'" target="_blank">'+data[i].title+'</a>\
                                </div>';
                    }
                }
                var addData =  '<tr data-id="'+data[i].id+'">\
                                <td>\
                                <div class="title_content">\
                                <div class="img_text">\
                                <span class="green">图文</span>\
                                <a class="co_blue" href="'+data[i].url+'" target="_blank">'+data[i].title+'</a>\
                                </div>'+
                                _child
                                +'</div>\
                                </td>\
                                <td>'+data[i].created_at+'</td>\
                                <td><button class="btn btn-default">选取</button></td>\
                                </tr>';
                $('#myModal tbody').append(addData);
            }
        };
        successBase(data);
    }
}
//点击选择图文默认显示高级图文
$(document).on('click','.js_showModel',function(){
   pictAjax(_picText);//调用图文素材交互方法   传递类型   变量来自wechat_base
   that_modal = $('#myModal');//modal对象赋值
   picOrOther = true;//当前选择图文
});
//商品模态框交互方法
function proAjax(types,title){
    var title = arguments[1]?arguments[1]:'';
    modelDataGet(types,success,title);
    function success(data){
        $('.myModal2Page').extendPagination({
            totalCount: data.data[0].total,
            showCount: data.data[0].last_page,
            limit: data.data[0].pre_page,
            callback: function (curr, limit, totalCount) {
                var page = $(this).text()//下标切换页码数
                if(!parseInt(page)&& $(this).parent().index() == 0){
                    page =  $('.myModal2Page .pagination .active').text();
                }else if(!parseInt(page)&& $(this).parent().index() != 0){
                    page =  parseInt($('.myModal2Page .pagination .active').text());
                }else if($(this).parents('li').hasClass('disabled')){
                    return false;
                }
                modelDataGet(types,successBase,title,page);
            }
        });
        function successBase(data){//交互成功后执行的基础方法 用于分页
            var data = data.data[0].data;
            $('#myModal2 tbody').empty();//先清空模态框原数据
            for (var i = 0; i < data.length; i++) {
                var _img = data[i].img?'<img src="'+imgUrl+data[i].img+'" />':'';//判断是否有图片
                var addData =   '<tr data-id="'+data[i].id+'">\
                                <td>'+
                                _img
                                +'<a class="co_38f" href="'+data[i].url+'" target="_blank">'+data[i].title+'</a>\
                                </td>\
                                <td>'+data[i].created_at+'</td>\
                                <td><button class="btn btn-default">选取</button></td>\
                                </tr>';
                $('#myModal2 tbody').append(addData);
            }
        };
        successBase(data);//立即调用
    }
}

// 微页面交互方法
function smallAjax(types,title){
    var title = arguments[1]?arguments[1]:'';
    modelDataGet(types,success,title);
    function success(data){
        $('.myModal1Page').extendPagination({
            totalCount: data.data[0].total,//数据总数
            showCount: data.data[0].last_page,//展示页数
            limit: data.data[0].per_page,//每页展示条数
            callback: function (curr, limit, totalCount) {
                var page = $(this).text()//下标切换页码数
                if(!parseInt(page)&& $(this).parent().index() == 0){
                    page =  $('.myModal1Page .pagination .active').text();
                }else if(!parseInt(page)&& $(this).parent().index() != 0){
                    page =  parseInt($('.myModal1Page .pagination .active').text());
                }else if($(this).parents('li').hasClass('disabled')){
                    return false;
                }
                modelDataGet(types,successBase,title,page);
            }
        });
        function successBase(data){//交互成功后执行的基础方法 用于分页
            var data = data.data[0].data;
            $('#myModal1 tbody').empty();//先清空模态框原数据
            for (var i = 0; i < data.length; i++) {
                var title = types == 3?data[i].page_title:data[i].title;
                var addData =  '<tr data-id="'+data[i].id+'">\
                                <td>\
                                <a class="co_38f" href="'+data[i].url+'" target="_blank">'+title+'</a>\
                                </td>\
                                <td>'+data[i].created_at+'</td>\
                                <td><button class="btn btn-default">选取</button></td>\
                                </tr>';
                $('#myModal1 tbody').append(addData);
            }
        };
        successBase(data);
    }
}


// 模态框交互总方法
function modelDataGet(type,func,title,page){
    var title = arguments[2]?arguments[2]:'';
    var page = arguments[3]?arguments[3]:1;
    var data = {
       type: type,//参数类型
       wid: $('#wid').val(),//页面标志
       page: page,
       title: title //搜索内容
    }
    $.get('/merchants/linkTo/get',data,function(data){
	        console.log(data)
    	if(data.status == 1){
	        func(data);
	        that_modal.modal('show');
    	}
    });
}

//-------------各种弹框触发事件结束-------------

//----------点击创建图片--------------
/*
*微信图文 高级图文交互
*id  编辑时所需图文id
*title 标题
*cover 封面图
*author 作者
*show_cover_pic 是否显示封面  0 false 1 true
*content_source_url  图文信息的原文地址 即 点击 阅读原文  后的url
*content_source_title 图文消息的原文地址标题
*digest 图文信息的摘要 仅单条图文
*content 图文信息的具体内容  富文本内容
*that 触发的元素 阻止表单多次提交
*urlType 提交的表单地址
*/
function materialAjax(id,type,title,cover,author,show_cover_pic,content_source_url,content_source_title,digest,content,that,urlType){
    var data = {
        id: id,
        type: type,
        title: title,
        cover: cover,
        author: author,
        show_cover_pic: show_cover_pic,
        content_source_url: content_source_url,
        content_source_title: content_source_title,
        _token: _token,
        digest: digest,
        content: content,
    }
    if(urlType == 1){
        var url = '/merchants/wechat/materialWechatSingle';
    }else if(urlType == 2){
        var url = '/merchants/wechat/materialWechatMulti';
    }else if(urlType == 3){
        var url = '/merchants/wechat/materialAdvancedSingle';
    }else if(urlType == 4){
        var url = '/merchants/wechat/materialAdvancedMulti';
    }
    $.post(url,data,function(data){
        if(data.status == 1){
            tipshow(data.info,'info');
            setTimeout(function(){
                window.location.href = data.url;
            },500);
        }else{
            tipshow(data.info,'warn');
        }
        that.prop('disabled',false);
    });
}
/*
*营销活动弹框 （数据的获取）
*@author huoguanghui
*@自动回复@自定义菜单
*/

(function(win){
    function ActiveModel(){
    	
        this.token = $("meta[name='csrf-token']").attr("content");
        
        //打开弹框方法
        this.open = function(obj){
            cType = 14;//给后台的类型值
            pt_type = 14;
            this.requestList(obj);
            this.switchNav();
            this.search();
            this.pagination();
            $("#activeModal").modal("show");
        };
        //请求列表的方法
        this.requestList = function(obj){
            var page = obj.page ? obj.page : "";
            var search = obj.search ? obj.search : "";
            var type = obj.type ? obj.type : "";//判断类型（切换份页 type1） 
            var data = {
                "_token": this.token,
                "page": page,
                "keyword": search
            }
            //判断打开的活动 类型
            switch ($("#activeModal .list_active").data("type"))
            {
                case "egg":
                    var url = "/merchants/marketing/egg/index?size=6";
                    var href = domain_url+'shop/activity/egg/index/';
                break;
                case "wheel":
                    var url = "/merchants/marketing/wheelList?pagesize=6";
                    var href = domain_url+'shop/activity/wheel/';
                break;
            }
            $.post(url,data,function(res){
                if(res.status == 1&&res.data[0].data.length!=0){
                    $("#activeModal .activeItem").remove();//添加之前先初始化数据
                    $("#activeModal .table_info").addClass("hide");//隐藏数据提示
                    if(!type){
                        $('#activeModal .myModalPage').extendPagination({
                            totalCount: res.data[0].total,
                            showCount: res.data[0].last_page,
                            limit: res.data[0].pre_page
                        });
                    }
                    var data = res.data[0].data;
                    var _html="";
                    for(var i = 0;i < data.length;i ++){
                        var start_at = data[i].start_at?data[i].start_at:data[i].start_time;
                        var end_at = data[i].end_at?data[i].end_at:data[i].end_time;
                        _html+= '<tr class="activeItem" data-id="'+data[i].id+'">'+
                            '<td><a class="co_38f" target="_blank" href="'+href+data[i].wid+'/'+data[i].id+'">'+data[i].title+'</a></td>'+
                            '<td class="tc">'+start_at+'</td>'+
                            '<td class="tc">'+end_at+'</td>'+
                            '<td class="tc">'+
                                '<button class="btn btn-default">选取</button>'+
                            '</td>'+
                        '</tr> ';
                    }
                    $("#activeModal tbody").append(_html);
                }else if(res.data[0].data.length==0){
                    $("#activeModal .activeItem").remove();//添加之前先初始化数据
                    $("#activeModal .table_info").removeClass("hide");//显示数据提示
                }
            })
            
        }
        //分页方法
        this.pagination = function(){
            var that = this;
            $(document).on('click','#activeModal .myModalPage li', function(event) {
                var page = $(this).text()//下标切换页码数
                if(!parseInt(page)&& $(this).parent().index() == 0){
                    page =  $('#activeModal .myModalPage .active').text();
                }else if(!parseInt(page)&& $(this).parent().index() != 0){
                    page =  parseInt($('#activeModal .myModalPage .active').text());
                }else if($(this).parents('li').hasClass('disabled')){
                    return false;
                }
                that.requestList({
                    "page": page,
                    "type": 1
                })
            });
        };
        //切换列表
        this.switchNav = function(){
            var that = this;
            $("#activeModal .js_switch").off().on("click",function(){
                $("#activeModal .js_switch").removeClass("list_active");
                $(this).addClass("list_active");
                that.requestList({})//请求数据
            });
        };
        //搜索
        this.search = function(){
            var that = this;
            $("#activeModal thead .btn-default").off().on("click",function(){
                console.log($(this).siblings("input").val())
                that.requestList({
                    "search": $(this).siblings("input").val()
                });
                $(this).siblings("input").val("");
            });
        }
    }
    win.activeModel = new ActiveModel();
})(window);
/**
 * 列表类模态框整合
 * @abject：秒杀，活动
 */
(function(win){
    function ModalObject(){
        this.obj = {};//open方法传来的对象
        this.token = $("meta[name='csrf-token']").attr("content");
        /*打开弹框方法*/
        this.open = function(obj){
            this.obj= obj;
            cType = obj.itemType;//给后台的类型值
            pt_type = obj.itemType;
            this.requestList(obj);
            this.switchNav();
            this.search();
            $(obj.modal).modal("show");
        };
        /*请求列表方发*/
        this.requestList = function(obj){
            var that = this;
            var page = obj.page ? obj.page : "";
            var search = obj.search ? obj.search : "";
            var type = obj.type ? obj.type : "";//判断类型（切换分页 type 1） 
            var data = {
                "_token": this.token,
                "wid": that.obj.wid,
                "page": page,
            }
            /*区分个别模态框搜索字段不一致*/
            if( that.obj.http == "get"){
                data.title = search;
            }else{//活动为keyword
                data.keyword = search;
            }
            var _url = $(that.obj.modal + " .modalNav .list_active").data("requesturl");
            var _href = $(that.obj.modal + " .modalNav .list_active").data("href");
            $.ajax({
                url: _url,
                type: that.obj.http,
                data: data,
                success: function(res){
                    if(res.status == 1 && res.data[0].data.length!=0){
                        $(that.obj.modal + " .activeItem").remove();//添加之前先初始化数据
                        $(that.obj.modal + " .table_info").addClass("hide");//隐藏数据提示
                        if(!type){
                            $(that.obj.modal + ' .myModalPage').extendPagination({
                                totalCount: res.data[0].total,
                                showCount: res.data[0].last_page,
                                limit: res.data[0].per_page,
                                callback: function(){
                                    var page = $(this).text()//下标切换页码数
                                    if(!parseInt(page)&& $(this).parent().index() == 0){
                                        page =  $(that.obj.modal + ' .myModalPage .active').text();
                                    }else if(!parseInt(page)&& $(this).parent().index() != 0){
                                        page =  parseInt($(that.obj.modal + ' .myModalPage .active').text());
                                    }else if($(this).parents('li').hasClass('disabled')){
                                        return false;
                                    }
                                    that.requestList({
                                        "page": page,
                                        "type": 1,
                                        "modal": that.obj.modal,
                                        "wid": that.obj.wid
                                    })
                                }
                            });
                        }
                        var data = res.data[0].data;
                        var _html="";
                        for(var i = 0;i < data.length;i ++){
                            var start_at = data[i].start_at?data[i].start_at:data[i].start_time;
                            var end_at = data[i].end_at?data[i].end_at:data[i].end_time;
                            _html+= '<tr class="activeItem" data-id="'+data[i].id+'">'+
                                '<td><a class="co_38f" target="_blank" href="'+_href+'/'+data[i].id+'">'+data[i].title+'</a></td>'+
                                '<td class="tc">'+start_at+'</td>'+
                                '<td class="tc">'+end_at+'</td>'+
                                '<td class="tc">'+
                                    '<button class="btn btn-default">选取</button>'+
                                '</td>'+
                            '</tr> ';
                        }
                        $(that.obj.modal + " tbody").append(_html);
                    }else if(res.data[0].data.length == 0){
                        $(that.obj.modal + " .activeItem").remove();//添加之前先初始化数据
                        $(that.obj.modal + " .table_info").removeClass("hide");//显示数据提示
                    }
                }
            })
        }
        /*切换列表*/
        this.switchNav = function(){
            var that = this;
            $(that.obj.modal + " .js_switch").off().on("click",function(){
                var index = $(this).index();
                $(that.obj.modal + " .js_switch").removeClass("list_active");
                $(this).addClass("list_active");
                $(that.obj.modal + " .js_newActive").addClass("hide");
                $(that.obj.modal + " .js_newActive").eq(index).removeClass("hide");
                that.requestList({})//请求数据
            });
        };
        /*搜索*/
        this.search = function(){
            var that = this;
            $(that.obj.modal + " thead .btn-default").off().on("click",function(){
                console.log($(this).siblings("input").val())
                that.requestList({
                    "search": $(this).siblings("input").val()
                });
                $(this).siblings("input").val("");
            });
        }
    }
    win.modalObject = new ModalObject();
})(window);