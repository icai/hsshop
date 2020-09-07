$(function(){
    init();
    $(window).resize(function(){
        init();
    });

    // 关闭服务
    $('.close_btn').click(function(){
        $('.right').addClass('hide_help');  
    });

    // 打开帮助
    $('.help_btn').click(function(){
        $('.right').removeClass('hide_help');
    });

	/* 初始化 */
	function init(){
	    /* 帮助和服务中心最小高度 */
	    var _height = getPageSize()[3];
	    $('.first_items, .second_items').css( 'height', _height );
	    $('.right').height( _height );
	}
	
	/**
	 * [getPageSize 获取文档和浏览器的宽高]
	 * @return {返回数组} [[0]->文档实际宽度,[1]->文档实际高度,[2]->浏览器宽度,[3]->浏览器高度]
	 */
	function getPageSize() {
	    var xScroll, yScroll;
	    if (window.innerHeight && window.scrollMaxY) {
	        xScroll = window.innerWidth + window.scrollMaxX;
	        yScroll = window.innerHeight + window.scrollMaxY;
	    } else {
	        if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac 
	            xScroll = document.body.scrollWidth;
	            yScroll = document.body.scrollHeight;
	        } else {    // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari 
	            xScroll = document.body.offsetWidth;
	            yScroll = document.body.offsetHeight;
	        }
	    }
	    var windowWidth, windowHeight;
	    if (self.innerHeight) {             // all except Explorer 
	        if (document.documentElement.clientWidth) {
	            windowWidth = document.documentElement.clientWidth;
	        } else {
	            windowWidth = self.innerWidth;
	        }
	        windowHeight = self.innerHeight;
	    } else {
	        if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode 
	            windowWidth = document.documentElement.clientWidth;
	            windowHeight = document.documentElement.clientHeight;
	        } else {
	            if (document.body) {        // other Explorers 
	                windowWidth = document.body.clientWidth;
	                windowHeight = document.body.clientHeight;
	            }
	        }
	    } 
	    // for small pages with total height less then height of the viewport 
	    if (yScroll < windowHeight) {
	        pageHeight = windowHeight;
	    } else {
	        pageHeight = yScroll;
	    } 
	    // for small pages with total width less then width of the viewport 
	    if (xScroll < windowWidth) {
	        pageWidth = xScroll;
	    } else {
	        pageWidth = windowWidth;
	    }
	    arrayPageSize = new Array( pageWidth, pageHeight, windowWidth, windowHeight );
	    return arrayPageSize;
	}

	function check(value){
	    var reg=/^[1-9]\d*$|^0$/;   // 注意：故意限制了 0321 这种格式，如不需要，直接reg=/^\d+$/;
	    if(reg.test(value)==true){
	        return true;
	    }else{
	        return false;
	    }
	}
});
	/**
    显示删除prover
    that删除按钮对象例如：$('.delete')必填
    position 值为1代表左删除，2为右删除，默认左删除
    offsetHeight偏移高度（选填）
    offsetWidth偏移宽度（选填）
    successCallback 点击确定回吊函数
    info 提示信息
    boolean 标识是否是确认框还是弹框 true->确认框 false->仅仅是弹框,默认是确认框
**/
function showDelProver(that,successCallback,info,bool,position,offsetHeight,offsetWidth){
    var o = arguments[4] ? arguments[4] : 1;
    var a = arguments[5] ? arguments[5] : 6;
    var b = arguments[6] ? arguments[6] : 5;
    var c = arguments[2] ? arguments[2] : "你确定要删除吗？";
        o = o == 1 ? 'left' : 'right'; 
    var bool=arguments[3];
    if(bool!=='false'){         // 默认 -》确认框       
        bool=true;
    }else{                     // 弹框
        bool=false;
    }
    var id = 'hsgf'+new Date().getTime();       // 创建id
    var html = '<div id="'+id+'" class="popover del_popover '+ o +'"><div class="arrow"></div>';
        html += '<div class="popover-content"> <div class="inline_block">'+ c +'</div>';
        if( bool ){
            html += '<button class="btn btn-primary sure_btn">确定</button>';
            html += '<button class="btn btn-default cancel_btn">取消</button>';
        }
        html += '</div></div>';
    $('body').append(html);
    $('#'+id).show().siblings('.del_popover').remove();
    if(o == 'left'){
        $('.del_popover').css('top',that.offset().top-$('.del_popover').height()/2+a);
        $('.del_popover').css('left',that.offset().left-$('.del_popover').width()-b);
    }else{
        $('.del_popover').css('top',that.offset().top-$('.del_popover').height()/2+a);
        $('.del_popover').css('left',that.offset().left+that.outerWidth()-b);
    }
    $('.sure_btn').unbind('click').bind('click',function(){
        successCallback();
        $('.del_popover').hide();
    })
    $('.cancel_btn').unbind('click').bind('click',function(){
        $('.del_popover').hide();
    })
}
	// 公共函数
	function center(obj){
	    var window_height = window.screen.availHeight/2;
	    var height = obj.height();
	    obj.css('margin-top',window_height - height/2 -30 );
	}
	function showModel(obj,obj2){
	    obj.show();
	    $('.modal-backdrop').show();
	    center(obj2);
	}
	function hideModel(obj){
	    obj.hide();
	    $('.modal-backdrop').hide();
	}
	// 获取时间
	/**
	    type 1向前去时间，2，向后取时间
	    day时间天数
	    format时间格式
	**/
	function fun_time(type,day,format){
	    var a = arguments[0] ? arguments[0] : 2;
	    var b = arguments[1] ? arguments[1] : 7;
	    var c = arguments[2] ? arguments[2] : "-";
	    var date = {};
	    var date1 = new Date();
	    date.now = date1.getFullYear()+c+(date1.getMonth()+1)+c+date1.getDate()+' '+ date1.toLocaleTimeString().substring(2);
	    var date2 = new Date(date1);
	    if(a==1){
	        date2.setDate(date1.getDate()-b);
	    }else if(a==2){
	        date2.setDate(date1.getDate()+b);
	    }
	    var times = date2.getFullYear()+c+(date2.getMonth()+1)+c+date2.getDate()+' '+ date1.toLocaleTimeString().substring(2);
	    date.sevnDay = times;
	    return date;
	}
	/**
	    单图上传图片转换成base64格式函数
	    event 事件对象   
	    imagewrap img标签（结构必须是三层结构ul li img）
	**/
	function readImageFile(event,imagewrap){
	    var reader = new FileReader(); 
	    reader.readAsDataURL(event.target.files[0]); 
	    reader.onload = function(e){ 
	        $(imagewrap).parent().parent().show();
	        $(imagewrap).attr('src',this.result);
	    } 
	}
	/**
	    百度富文本初始化
	    wrap初始化的元素可以是ID列如：eidtor
	**/
	function initUeditor(wrap){
	    var ue = UE.getEditor(wrap,{
	        toolbars: [
	               [
	                'undo', //撤销
	                'redo', //重做
	                'bold', //加粗
	                'indent', //首行缩进
	                'snapscreen', //截图
	                'italic', //斜体
	                'underline', //下划线
	                'strikethrough', //删除线
	                'subscript', //下标
	                'fontborder', //字符边框
	                'superscript', //上标
	                'formatmatch', //格式刷
	                'source', //源代码
	                'pasteplain', //纯文本粘贴模式
	                'selectall', //全选
	                'print', //打印
	                'preview', //预览
	                'horizontal', //分隔线
	                'removeformat', //清除格式
	                'time', //时间
	                'date', //日期
	                'mergeright', //右合并单元格
	                'mergedown', //下合并单元格
	                'deleterow', //删除行
	                'deletecol', //删除列
	                'splittorows', //拆分成行
	                'splittocols', //拆分成列
	                'splittocells', //完全拆分单元格
	                'deletecaption', //删除表格标题
	                'mergecells', //合并多个单元格
	                'deletetable', //删除表格
	                'insertparagraphbeforetable', //"表格前插入行"
	                'fontfamily', //字体
	                'fontsize', //字号
	                'paragraph', //段落格式
	                'edittable', //表格属性
	                'edittd', //单元格属性
	                'link', //超链接
	                'emotion', //表情
	                'spechars', //特殊字符
	                'searchreplace', //查询替
	                'justifyleft', //居左对齐
	                'justifyright', //居右对齐
	                'justifycenter', //居中对齐
	                'justifyjustify', //两端对齐
	                'forecolor', //字体颜色
	                'backcolor', //背景色
	                'rowspacingtop', //段前距
	                'rowspacingbottom', //段后距
	                'imagenone', //默认
	                'imagecenter', //居中
	                'lineheight', //行间距
	                'edittip ', //编辑提示
	                'customstyle', //自定义标题
	                'autotypeset', //自动排版
	                'background', //背景
	                'inserttable', //插入表格
	                'drafts', // 从草稿箱加载
	            ]
	        ],
	        initialFrameHeight:150,//设置编辑器高度
	    });
	    return ue;
	}
	function bindEventEditor(ue){
	    ue.addListener("selectionchange", function () {
	        var content = ue.getContent();
	        $('.editing').children('editor-text').html(content);
	        $('.editing').css('border','2px dashed rgba(255,0,0,0.5)');
	    });
	}
	/**
	    返回近多少天的日期
	    day 天数
	    format 返回的时间格式默认"-"
	**/
	function getdate(day,format){
	    var a = arguments[0] ? arguments[0] : 7;
	    var b = arguments[1] ? arguments[1] : "-";
	    var today = new Date();
	    var obj={
	            end_date:'',
	            start_date:'',
	        };
	    today.setHours(0);
	    today.setMinutes(0);
	    today.setSeconds(0);
	    today.setMilliseconds(0);
	    today = Date.parse(today);
	    // 昨天
	    var oneweek = 1000*60*60*24*a;
	    yesterday = new Date(today-1);
	    oneweek = new Date(yesterday-oneweek+1);
	    obj.end_date = formatDate(yesterday,b);
	    obj.start_date = formatDate(oneweek,b);
	    return obj;
	}
	/**
	    格式化时间的函数
	    now 要格式化的时间 
	    format 返回的时间格式
	**/
	function formatDate(now,format) { 
	    var year=now.getFullYear(); 
	    var month=now.getMonth()+1; 
	    var date=now.getDate(); 
	    var hour=now.getHours(); 
	    var minute=now.getMinutes(); 
	    var second=now.getSeconds();
	    if(month<10){
	        month = '0'+month;
	    }
	    if(date<10){
	        date = '0'+date;
	    }
	    if(minute == '0'){
	        minute = '00';
	    }
	    if(second =='0'){
	        second = '00';
	    }
	    if(hour =='0'){
	        hour = '00';
	    }
	    return year+format+month+format+date+" "+hour+":"+minute+":"+second;      
	};
	
	/**
        html 提示信息;
        bgcolor:提示背景颜色;值为 info，warn
        time:提示显示时间默认2秒;
    **/
    function tipshow(html,bgcolor,time){
        var a = arguments[2] ? arguments[2] : 2000;
        var tipHtml = '<div class="info_tip">'+ html +'</div>';
        $('body').append(tipHtml);
        if(bgcolor == "info"){
            $('.info_tip').css('background-color','#45b182');
        }else if(bgcolor == "warn"){
            $('.info_tip').css('background-color','#ff4343');
        }
        $('.info_tip').show(100);
        setTimeout(function(){
            $('.info_tip').remove();
        },a);
    }
    

///*阻止事件冒泡*/
function clearEventBubble(evt) {
     if (evt.stopPropagation) {
          evt.stopPropagation();   // 支持谷歌、火狐
     } else {
          evt.cancelBubble = true;// 支持IE
     }
     if (evt.preventDefault) {
          evt.preventDefault();//  阻止后面将要执行的浏览器默认动作.
     } else {
          evt.returnValue = false;
     }
}

//模态框居中设置
$(document).on('click',"[data-toggle='modal']",function(){
	var _target = $(this).attr('data-target');
	t=setTimeout(function () {
		var _modal = $(_target).find(".modal-dialog")
		_modal.css({'margin-top': parseInt(($(window).height() - _modal.height())/3)})
	},0)
});