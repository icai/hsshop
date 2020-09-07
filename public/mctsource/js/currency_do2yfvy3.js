$(function() {
    //表单验证
    $("#save").click(function(){

    //当前的form通过ajax方式提交（用到jQuery.Form文件）
        $.post($('#shopConfigForm').attr('action'), $('form').serialize(), function (data) {
            if (data.status == 1) {
                tipshow(data.info);
                /* 后台验证通过 */
                if (data.url) {
                    /* 后台返回跳转地址则跳转页面 */
                    window.location.href = data.url;
                } else {
                    /* 后台没有返回跳转地址 */
                    // to do somethings
                }
            } else {
                tipshow(data.info, 'warn');
                /* 后台验证不通过 */
                $('input[type="submit"]').prop('disabled', false);
                // to do somethings
            }
        }, 'json');
     });

	function show_hide(clickEle, judgeEle, SH_Ele){
		$(clickEle).click(function(){
			$(judgeEle).prop("checked") ? $(SH_Ele).show() : $(SH_Ele).hide();
		})
	}
	
	//微信页面标题设置开关；
	show_hide("#weixinTitle", "#weixinTitle", ".weixinTitleImg");

	//购物车图标的显示隐藏设置；
	show_hide("#shopCarImg", "#shopCarImg", ".showCarImgs");
	
	//经营状态选择隐藏设置；
	$(".openStore").hide();
	show_hide(".business", "#openSell", ".businessTime");
	show_hide(".business", "#closeSell", ".openStore");

	//设置自动开业的隐藏设置；
	$(".openTime").hide();
	show_hide(".autoOpen", "#setAutoOpen", ".openTime");
	
	//设置营业时间的隐藏设置；
	$(".businessRangTime").hide();
	show_hide(".business_time", "#selfSet", ".businessRangTime")
	
	//店铺底部logo的选择设置；
	$("#imgUpLoad, #prompt, .logoPreview").hide();
	show_hide(".logoSet", "#selfLogeSet", "#imgUpLoad, #prompt, .logoPreview")
	
	
	//鼠标移动到内容上，《i》的字体颜色加深；
	$(".form-group").hover(function(){
		$(this).find("i").css("color","#595959")
	},function(){
		$(this).find("i").css("color","rgb(156,156,156)")
	})
	
	//日期、时间选择
	$('#datetimepicker').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss'});
	$('#datetimepicker1').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss'});
    $('#datetimepicker2').datetimepicker({
    	format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: false 				//必须要设置的
    });
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    });
	

	
	//查看上传图片示例弹出层
	$("#lookExample").click(function(){
		layer.open({
			title: ['自定义LOGO示例', 'font-size:18px;'],
			area: ['400px', '600px'],
			skin: 'demo-class',
  			type: 1,
		  	//skin: 'layui-layer-demo', //样式类名
		  	closeBtn: 1, 
		  	anim: 2,
		  	//shadeClose: true, //开启遮罩关闭
		  	content: '<img src="http://www.huihaokeji.cn/mctsource/images/logoExample.png" style="margin:13px auto"/>',
		  	btn: "我知道了"
		});
	})
	
	// logo 上传
	options ={
		thumbBox: '.thumbBox',
		spinner: '.spinner',
		imgSrc: imgUrl + 'images/default.png',
		img :  '',
		flag: false
	}

	cropper = $('.imageBox').cropbox(options);			// 初始化默认图片
	$('#upload_file').on('change', function(){				// 上传图片按钮
		var reader = new FileReader();
		reader.onload = function(e) {
			options.imgSrc = e.target.result;
			cropper = $('.imageBox').cropbox(options);
		}
		options.flag = true;
		reader.readAsDataURL(this.files[0]);
		this.files = [];
		
	});

	// 只有上传图片后才能预览效果
	$('.thumbBox').on('mousemove', function(){ 
		if( options.flag ){
			setPreview();
		}
	});
	
	// 放大
	$('#btnZoomIn').on('click', function(){
		cropper.zoomIn();
	});
	// 减小
	$('#btnZoomOut').on('click', function(){
		cropper.zoomOut();
	});

	// 上传开始
	$('#btnCrop').click(function(){
		$(".logoPreview").html("");;
		$(".logoPreview").append("<img src='"+options.img+"' width='100' height='40' />")
	});
});

/**
 * [setPreview 设置预览效果函数]
 */
function setPreview(){
	options.img = cropper.getDataURL();
	var _html ='';
	_html += '<div class="preview_img">';
	_html += '<img src="'+options.img+'" width="180" height="60" />';
	_html += '<p class="size_info">尺寸：180px*60px</p>';
	_html += '<img src="'+options.img+'" width="240" height="80" />';
	_html += '<p class="size_info">尺寸：240px*80px</p>';
	_html += '</div>';
	$('.cropped').empty().append( _html );					// 清空并存入预览图
}
	
	