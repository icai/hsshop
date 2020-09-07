$(function(){
    if(!$('.member_p').is(":checked")){
        $(".coupon-icon").hide()
    }
    var classArr = ["Color010","Color020","Color030","Color040","Color050","Color060","Color070","Color080","Color081",
    "Color082","Color090","Color100","Color101","Color102"]
    var html = '';
    for(var i = 0;i < classArr.length;i ++){
        html += '<li class="'+classArr[i]+'"></li>'
    }
    $('.bgColor_cap').append(html);
    $('.controls').hover(function() {
        $('.bgColor_cap').show();
    },function() {
        $('.bgColor_cap').hide();
    });
    //选择背景颜色
    $(document).on('click','.bgColor_cap li',function(){
        $('.bgColor').attr('class','bgColor');
        $('.bgColor').addClass($(this).attr('class'));
        $('.card-region').attr('class','card-region')
        $('.card-region').addClass($(this).attr('class'));
        $('.bgColor_cap').hide();
        $('input[name="bg_color"]').val($(this).attr('class'));
    });
    // 开始时间
    $('#start_time,#end_time').datetimepicker({
        minDate: new Date(getDateTimeStamp()), //时间小于当前时间时会自动清空以有的数据
        format: 'YYYY-MM-DD',
        dayViewHeaderFormat: 'YYYY 年 MM 月',
        showClear: true,
        showClose: true,
        showTodayButton: false,
        locale: 'zh-cn',
        focusOnShow: false,
        useCurrent: false,
        tooltips: {
            today: '今天',
            clear: '清除',
            close: '关闭',
            selectMonth: '选择月',
            prevMonth: '上个月',
            nextMonth: '下一月',
            selectTime: '选择时间',
            selectYear: '选择年',
            prevYear: '上一年',
            nextYear: '下一年',
            selectDecade: '十年一组',
            prevDecade: '前十年',
            nextDecade: '后十年',
            prevCentury: '前一世纪',
            nextCentury: '后一世纪',
        },
        allowInputToggle: true,
    }); 

    //获取当前日期时间搓 
    function getDateTimeStamp(){
        var time_stamp =0;
        var date = new Date();
        var ts = date.getTime();
        var hs = date.getHours()*60*60*1000;
        var ms = date.getMinutes()*60*1000;
        var ss = date.getSeconds()*1000;
        var ms1 = date.getMilliseconds();
        return ts-hs-ms-ss-ms1;
    }

   
    $("#start_time").on("dp.change", function (e) {
        if($('input[name="date_limit"]:checked').val() == 2){
            $('.expiry-date span').html($('#start_time').val() + ' 至 ' + $('#end_time').val());
        }else if($('#start_time').val() != '' && $('#end_time').val() != ''){
            $('input[name="date_limit"]').eq(2).attr("checked","checked");
        }
        $('#end_time').data("DateTimePicker").minDate(e.date);
    });

    $("#end_time").on("dp.change", function (e) {
        if($('#start_time').val() != ''){
            $('input[name="limit_days"]').val('');
            $('input[name="date_limit"]').eq(2).attr("checked","checked");
            $('.expiry-date span').html($('#start_time').val() + ' 至 ' + $('#end_time').val());
        }
        $('#start_time').data("DateTimePicker").maxDate(e.date);
    });

    // 会员期限开始时间失焦
    $('#start_time').blur(function(){
        if($('input[name="date_limit"]:checked').val() == 2){
            $('.expiry-date span').html($('#start_time').val() + ' 至 ' + $('#end_time').val());
        }
    })
    $('input[name="date_limit"]').change(function(){
        if($('input[name="date_limit"]:checked').val() == 0){
            $('.expiry-date span').html('无限期');
            $('input[name="limit_days"]').val('');
        }
    })
    //显示隐藏会员权益
    $('input[name="member_power[]"]').click(function(){
        if($(this).is(':checked')){
            if($(this).val() == 2){
                if($('input[name="discount"]').val() != ''){
                    $('.item-name.discount').html($('input[name="discount"]').val() + '折');
                    $('.membership li').eq($(this).val() - 1).css('display','inline-block');
                }
            }else{
                $('.membership li').eq($(this).val() - 1).css('display','inline-block');
            }   
        }else{
            $('.membership li').eq($(this).val() - 1).css('display','none');
        }
    })
    // 会员折扣失焦
    $('input[name="discount"]').blur(function(){
        if($(this).val() != '' && $(this).parent().children('input').is(':checked')){
            $('.item-name.discount').html($('input[name="discount"]').val() + '折');
            $('.discount').parent().css('display','inline-block');
        }
    })
    // 会员期限日期选择框失焦
    $('input[name="limit_days"]').blur(function(){
        if($(this).val() != ''){
            $('#start_time').val('');
            $('#end_time').val('');
            $('.expiry-date span').html($(this).val()+ '天');
            $('input[name="date_limit"]').eq(1).prop("checked","checked");
        }
    })
	$('input[name="card_title"]').bind('blur',function(){
		$('.card_title').html($(this).val());
	})
	// 会员卡选择背景颜色
	$('#bg_color').click(function(){
        $('.card-region').addClass($('input[name="bg_color"]').val()).removeAttr('style');
        $(".bg_color").css("color","")
        $(".fenmian").hide();
	})
	// 改变背景颜色
	$('input[name="bg_color"]').bind('change',function(){
		if($('#bg_color').prop("checked")){
			$('.card-region').css('background-image','');
			$('.card-region').css('background',$('input[name="bg_color"]').val());
		}
	})
	// 选择图片点击
	$('#bg_image').click(function(){
	    if($(".reply_cap .imgs").css("display")=="block"){
            $('.card-region').css('background-color','');
            var imgSrc = $('.reply_cap').find('img').attr('src')
            if(imgSrc){
                bg_img()
            }
        }else{
            $(".fenmian").show();
            $(".bg_color").css("color","red")
        }
	})

    //判断选中
    if($('#bg_color').attr('checked')){
        $('.card-region').css('background',$('input[name="bg_color"]').val());

    }
    if($('#bg_image').attr('checked')){
        var url=$("#souce").val();
        $('.card-region').css('background','');
        $('.card-region').css('background-image','url('+$('.reply_cap img').attr('src')+')');
        $('.card-region').css('background-repeat','no-repeat');
        $('.card-region').css('background-size','cover');
        $('.card-region').css('background-position','50%');
    }
    //删除
    $("#btn-close").on('click',function () {
        $(this).parent().siblings('img').attr('src','')
        $('.reply_cap').hide().siblings('a').show();
        $('.card-region').addClass($('input[name="bg_color"]').val()).removeAttr('style');
    })
	// 表单验证


    // 添加优惠券
    $('.add_coupons').click(function(){
        var html = `
            <div class="form-group member_limit">
                <label for="inputPassword3" class="col-sm-3 control-label"></label>
                <div class="col-sm-9 inline padleft10">
                    <span>优惠券 开卡赠送</span>
                    &nbsp;
                    <select class="form-control width_130 coupons_select" name="coupon_type[]">`
                     + $('.coupons_select').html() +   
                    `</select>
                    <input type="text" name="coupon_num[]" class="form-control coupon_num width_40" disabled>
                    &nbsp;
                    <span>张</span>
                    <div class="close-modal">×</div>
                </div>
            </div>
        `
        $('.member_power').append(html);
    })
    //鼠标移动优惠券上面删除按钮显示
    $(document).on('mouseenter','.member_power .padleft10',function(){
        $(this).children('.close-modal').show();
    })
    //鼠标移开优惠券删除按钮隐藏
    $(document).on('mouseleave','.member_power .padleft10',function(){
        $(this).children('.close-modal').hide();
    })
    //点击删除按钮删除优惠券选项
    $(document).on('click','.close-modal',function(){
        $(this).parent().parent().remove();
    })
    // 会员卡名称失焦事件
    $('input[name="title"]').blur(function(){
        $('.member-type').html($(this).val());
    })
    // 会员期限设置添加点击
    $('.js-add-sku-atom').click(function(){
        $('.popover-link-wrap').show();
        $('.popover-link-wrap').css('top','39px')
    })
    $('.js-btn-confirm').click(function(){
        if($('.js-link-placeholder').val() != ''){
            $('.js-add-sku-atom').hide();
            $('.member_date_wrap .sku-atom span').html($('.js-link-placeholder').val());
            $('.member_date_wrap .sku-atom').css('display','inline-block');
        }
        $('.popover-link-wrap').hide();
    })
    $('.member_date_wrap .sku-atom').mouseover(function(){
        $(this).children('.close-modal').show();
    })
    $('.member_date_wrap .sku-atom').mouseout(function(){
        $(this).children('.close-modal').hide();
    })
    $('.js-remove-sku-atom').click(function(){
        $('.js-add-sku-atom').show();
    })
	 var flag = true;
     $('#cardForm').bootstrapValidator({
        fields: {
            title: {
                validators: {
                    notEmpty: {
                        message: '会员卡名称不能为空！'
                    }
                }
            },
            description: {
                validators: {
                    notEmpty: {
                        message: '使用须知不能为空！'
                    }
                }
            },
            date_limit: {
                validators: {
                    notEmpty: {
                        message: '请选择会员期限！'
                    }
                }
            },
            'coupon_type[]': {
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
            'coupon_num[]': {
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
        },
        onSuccess:function(){
            
        }
    });
    //下拉框事件
    $('.coupons_select').each(function(){
        if($(this).val() == 0){
            $(this).siblings('input[name="coupon_num[]"]').attr('disabled',true);
        } 
    });
    $(document).on('change','.coupons_select',function(){
        if($(this).val() == 0){
            $(this).siblings('input[name="coupon_num[]"]').attr('disabled',true);
        }else{
            $(this).siblings('input[name="coupon_num[]"]').attr('disabled',false);
        }
    })
    //表单验证改写
    $('.save_form').click(function(){
        if($('.member_p').is(":checked")){
            if($(".coupons_select").val() == 0){
                tipshow("请选择优惠券",'warn');
                return false
            }
             $('#cardForm').bootstrapValidator('enableFieldValidators', 'coupon_type[]',true)
                            .bootstrapValidator('enableFieldValidators', 'coupon_num[]', true);
        }
        // else{
        //     $('#cardForm').bootstrapValidator('enableFieldValidators', 'coupon_type[]',false)
        //                     .bootstrapValidator('enableFieldValidators', 'coupon_num[]', false); 
        // }
        $('#cardForm').data('bootstrapValidator').validate();
        if($('#cardForm').data('bootstrapValidator').isValid()){  
            var formData = new FormData($('form[name="cardForm"]')[0]);
             if(flag){
                flag = false;
                $('.save_form').attr('disabled','disabled');
                $.ajax({  
                     url: '/merchants/member/membercard',  
                     type: 'POST',  
                     data: formData,  
                     async: false,  
                     cache: false,
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                     dataType:'json',
                     contentType: false,
                     processData: false,
                     success: function (data) {
                        if(data.status==1){
                            tipshow(data.info);
                            setTimeout(function(){
                                window.location.href = '/merchants/member/membercard';
                            },1000)
                        }else{
                            tipshow(data.info,'warn');
                        }
                        flag = true;
                     },  
                     error: function (data) {
                         tipshow( '保存失败！','warn');
                         flag = true;
                         //alert('保存失败！');
                        // alert(data);
                     },complete:function(){
                        $('.save_form').removeAttr('disabled');
                     }
                });
            }   
        } 
        return false;
    });

    //删除会员卡
    $(".dele_form").click(function(e){
        var id = $(this).attr('data-id');  
        var t_index = layer.open({
            type: 1,
            title:"提示",
            btn:["确定","取消"],
            yes:function(){ 
                $.ajax({ 
                    url:'/merchants/member/membercard/delete',// 跳转到 action
                    data:{'id':id},
                    type:'post',
                    cache:false,
                    headers: {
                        'X-CSRF-TOKEN': $("input[name='_token']").val()
                    },
                    dataType:'json',
                    success:function (data) {
                        if (data.status == 1){
                            layer.close(t_index);
                            tipshow("删除成功");
                            setTimeout(function(){
                                location.href="/merchants/member/membercard";
                            },1000) ;
                        }else{
                            tipshow(data.info);
                        }
                    },
                    error : function() {
                        tipshow("异常");
                    }
                });
            },
            closeBtn:false, 
            move: false, //不允许拖动
            skin:"layer-tskin", //自定义layer皮肤 
            area: ['450px', '180px'], //宽高
            content: "<div style='padding: 20px 15px;'>是否要删除此会员卡？</div>"
        });
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        }); 
    });
    //禁用会员卡
    $(".disabled_form").click(function(e){
        var id = $(this).attr('data-id');
        $.ajax({ 
            url:'/merchants/member/membercard/disableCard/'+id,// 跳转到 action
            data:{},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $("input[name='_token']").val()
            },
            dataType:'json',
            success:function (data) {
                if (data.status == 1){
                    tipshow("禁用成功");
                    setTimeout(function(){
                        location.href="/merchants/member/membercard";
                    },1000) ;
                }else{
                    tipshow(data.info);
                }
            },
            error : function() {
                tipshow("异常");
            }
        });
    });
    //启用会员卡
    $(".enable_form").click(function(e){
        var id = $(this).attr('data-id');
        $.ajax({ 
            url:'/merchants/member/membercard/disableCard/'+id,// 跳转到 action
            data:{},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $("input[name='_token']").val()
            },
            dataType:'json',
            success:function (data) {
                if (data.status == 1){
                    tipshow("启用成功");
                    setTimeout(function(){
                        location.href="/merchants/member/membercard";
                    },1000) ;
                }else{
                    tipshow(data.info);
                }
            },
            error : function() {
                tipshow("异常");
            }
        });
    });
    // 弹出选择图片框
    //----------------图片模态框点击事件---------------点击选择图片
    //修改功能移至此处 2018.10-17 by 倪凯嘉
    var classifyId;
    $(document).on('click','.control-bgchartaction,.xiugai',function(){
    	var _token = $('meta[name="csrf-token"]').attr('content');
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
        $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,'_token':_token},function(data){//默认第一组
            getPicture(data);
            $('.picturePage').extendPagination({
                totalCount: data.data[0].total,
                showCount: data.data[0].last_page,
                limit: data.data[0].per_page, 
            });
        });
        $('#myModal-adv').modal('show');
    });
    
	//点击分组事件
    $(document).on('click','.js-category-item',function(){
        $('.js-category-item').removeClass('active');
        $(this).addClass('active');
        classifyId = $(this).data('id');
        var _token = $('meta[name="csrf-token"]').attr('content');
        if($(this).children('span').text() == 0){
            
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
                limit: data.data[0].per_page,
            });
        });
    });
    
    //点击分页
    $('.modal .attachment-pagination').on('click','.picturePage .pagination li a', function(event) { 
	    var page = $(this).text()//下标切换页码数
	    var _token = $('meta[name="csrf-token"]').attr('content');
	    if(!parseInt(page)&& $(this).parent().index() == 0){
	        page =  $('.picturePage .pagination .active').text();
	    }else if(!parseInt(page)&& $(this).parent().index() != 0){
	        page =  parseInt($('.picturePage .pagination .active').text());
	    }else if($(this).parents('li').hasClass('disabled')){
	        return false;
	    }
	    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,_token:_token,page:page},function(data){
	        getPicture(data); 
	    });
	});


    //点击上传图片切换
    $('.js-show-upload-view, .js_addImg').on('click',function(){
        $('#myModal-adv').modal('hide');
        getCropper(3, 710/360,function (blob, img_file) {
            cutImgUpload(blob, img_file)
        })
    });
    $('.js_prev').on('click',function(){
        modalChange('.content_second');
    });
    //内容一选择图片显示边框
    var img_index;
    $(document).on('click','.imgData .image-item',function(){
        $(this).siblings('li').children('.attachment-selected').addClass('no');
        $(this).children('.attachment-selected').removeClass('no');
        $(this).parents('.modal-content').find('.modal-footer .js-confirm').hide();
        $(this).parents('.modal-content').find('.modal-footer .ui-btn-primary').removeClass('no');
        var index = $(this).attr('data-index')
        img_index = img_data[index];
        pictureSrc = $(this).children('.image-box').attr('src');
    });

    //内容三选择图片样式点击切换
    $('#iconStyleSelect li a').on('click',function(){
        $(this).parent().children().removeClass('selected');
        $(this).addClass('selected');
    });

    /*删除*/
    $(".co_38f").on("click",function () {
        $(".reply_cap ").hide();
        $(".reply_cap .imgs").hide();
        $(".control-bgchartaction").show();
        $('.card-region').css('background-image','');
        $('.card-region').css('background',$('input[name="bg_color"]').val());
    });
    /*修改*/
    // $(".xiugai").on("click",function () {
        
    //     $('.control-bgchartaction').click();
    // });
    //点击确认后获取图片
    $('.myModal-adv .ui-btn-primary').on('click',function(){
        //获取图片尺寸信息，并做图片大小限制 2018-10-17 by 倪凯嘉
        var url=$("#souce").val();
        var img_url = url + pictureSrc
        var img_size = img_index.FileInfo.img_size.split('x')
        if((img_size[0] / img_size[1]).toFixed(2) >= 1.97 &&  (img_size[0] / img_size[1]).toFixed(2) <= 1.99) {
            $('.ctts .imgs img').attr('src',pictureSrc);
            $('.reply_cap .ctts').children().hide();
            $('.ctts .imgs').show();
            $('.send_info').hide();
            $('.editor_img .select').hide();
            $('.editor_img .message_img').show();
            $(".reply_cap").show();
            $(".control-bgchartaction").hide();
            $(".fenmian").hide();
            $(".bg_color").css("color","");
            if($('#bg_image').is(':checked')){
                bg_img()
            }else{
                $("#bg_image").on("click",function () {
                    if($(".reply_cap .imgs").css("display")=="block"){
                        $('.card-region').css('background-color','');
                        var imgSrc = $('.reply_cap').find('img').attr('src')
                        if(imgSrc){
                            bg_img()
                        }
                    }
                });
            }
            $('#myModal-adv').modal('hide');
        } else {
            console.log('--------1111')
            $('#myModal-adv').modal('hide');
            console.log(img_url,img_index.FileInfo,'----------ffffffffffff');
            getCropper(3, 710/360, function (blob, img_file) {
                cutImgUpload(blob, img_file, 1)
            }, 1, img_url, img_index.FileInfo)
        }
    });
    /**
     * @auther 邓钊
     * @param blob 裁剪后的图片资源
     * @param img_file 原始的图片资源
     * @description 图片裁剪后提交到后台
     * @update 2019-9-23
     * @return
     */
    function cutImgUpload(blob, img_file, flag) {
        var formData = new FormData();
        formData.append("file", blob)
        if(img_file && !flag){
            formData.append("type", img_file.type)
            formData.append("lastModifiedDate", img_file.lastModifiedDate)
            formData.append("size", img_file.size)
        }
        formData.append("name", img_file.name) // update by 倪凯嘉 上传图片时增加图片名称 2019-09-29
        formData.append("classifyId", classifyId)
        $.ajax({
            url: '/merchants/myfile/upfile',
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res) {
                try {
                    var data = JSON.parse(res)
                    if (data.status === 1) {
                        tipshow("图片上传成功！");
                        var pictureSrc = '/' + data.data.FileInfo.path;
                        $('.ctts .imgs img').attr('src',pictureSrc);
                        $('.reply_cap .ctts').children().hide();
                        $('.ctts .imgs').show();
                        $('.send_info').hide();
                        $('.editor_img .select').hide();
                        $('.editor_img .message_img').show();
                        $(".reply_cap").show();
                        $(".control-bgchartaction").hide();
                        $(".fenmian").hide();
                        $(".bg_color").css("color","");
                        if($('#bg_image').is(':checked')){
                            bg_img()
                        }else{
                            $("#bg_image").on("click",function () {
                                if($(".reply_cap .imgs").css("display")=="block"){
                                    $('.card-region').css('background-color','');
                                    var imgSrc = $('.reply_cap').find('img').attr('src')
                                    if(imgSrc){
                                        bg_img()
                                    }
                                }
                            });
                        }
                    } else {
                        tipshow('图片上传失败，请重新上传图片','warm');
                    }
                } catch (err) {
                    tipshow('图片上传失败，请重新上传图片','warm');
                }
            },
            error:function(){
                tipshow('图片上传失败，请重新上传图片','warm');
            }
        })
    }
    // 微信卡包同步微信
    $('input[name="isSyncWeixin"]').change(function(){
        if($(this).is(':checked')){
            $('.edit_ways').removeClass('no');
            $('.active_info').hide();
        }else{
            $('.active_info').show();
            $('.edit_ways').addClass('no');
        }
    });
})
// 数据请求成功后执行方法
var img_data; //选择图片弹框显示的图片数据
function getPicture(data){
    $('.attachment-list-region .image-list').empty();//先清空所有的元素
    var _img_item= '';
    var _imgType;
    img_data = data.data[0].data
    for ( var i = 0;i < data.data[0].data.length;i++ ){
        _imgType = data.data[0].data[i].FileInfo.type.slice(data.data[0].data[i].FileInfo.type.lastIndexOf('/')+1)
        _img_item +='<li class="image-item" data-index="'+i+'">\
            <img class="image-box" src="/'+data.data[0].data[i].FileInfo.path+'" />\
            <div class="image-meta">'+data.data[0].data[i].FileInfo.img_size+'</div>\
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
function readImageFile(event){
    var reader = new FileReader();
    reader.readAsDataURL(event.target.files[0]);
    reader.onload = function(e){
        $('.bg_image img').attr('src',this.result);
        $('.bg_image input').val(this.result);
        if($('#bg_image').prop("checked")){
            $('.card-region').css('background-image','');
            $('.card-region').css('background-image','url('+this.result+')');
        }
    }
}
//点击选择图片切换
function modalChange(obj){
    $(obj).hide();
    $('.content_first').show();
    $('.myModal-adv .modal-body').removeClass('height_auto');
    $('.myModal-adv .cap_head').hide();
    $('.myModal-adv .module-nav').show();
}

//背景图片的显示
function bg_img() {
    //符合要求的图片才能将Url给背景图
    var bgImgUrl=$('.ctts .imgs img').attr("src");
    console.log(bgImgUrl);
    var url=$("#souce").val();
    $("input[name='bg_img']").val(url+bgImgUrl);
    $('.card-region').css('background-color','');
    $('.card-region').css('background-image','url('+url+bgImgUrl+')');
    $('.card-region').css('background-repeat','no-repeat');
    $('.card-region').css('background-size','cover');
    $('.card-region').css('background-position','50%');
}
//添加图片分组
$(".btn_left").on('click', function () {
    var _token =$("meta[name='csrf-token']").attr("content");//_token值
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
//取消商品分组输入框
$(".btn_right").on('click',function () {
    $(".add_group_box").addClass('hide')
    $('.add_group_list').attr('data-id','1');
})
//显示商品分组输入框
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



