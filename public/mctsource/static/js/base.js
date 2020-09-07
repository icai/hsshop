$(function(){
    // init();
    // $(window).resize(function(){
    //     init();
    // });
    // 关闭服务
    $('.close_btn').click(function(){
        $('.right').addClass('hide_help');  
    });

    // 打开帮助
    $('.help_btn').click(function(){
        $('.right').removeClass('hide_help');
    });

     // 按钮开关
    $('.switch_items').click(function(){
        $(this).find('label').addClass('loadding');
        var _this = $(this);
        setTimeout(function(){
            _this.find('label').removeClass('loadding');
        },80);
    });
    $('.adNav .slide_images img').click(function(){
        $('#slide_ad_bg').show();
        $('#slide_ad_model').show();
    })
    $('#slide_ad_model .close_mode_bg').click(function(){
        $('#slide_ad_bg').hide();
        $('#slide_ad_model').hide();
    })
});
    

/* 初始化 */
// function init(){
//     /* 帮助和服务中心最小高度 */
//     var _height = getPageSize()[3];
//     $('.first_items, .second_items').css( 'height', _height );
//     $('.right').height( _height );
// }

/* 获取页面的高度、宽度 */
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
/**
 * [checkAll 全选、反选函数]
 * @param  {[type]} allObj    [ 全选选择器 ]
 * @param  {[type]} singleObj [ 单选选择器 ]
 * @return {[type]}           [无]
 */
function checkAll( allSelector , singleSelector ){
    // 全选&反选
    $('body').on('click',allSelector,function(){
        if( $(this).prop('checked') ){              // 全选
            $( singleSelector ).prop('checked',true);
        }else{                                      // 反选
            $( singleSelector ).prop('checked',false);
        }
    });

    // 单选
    $('body').on('click',singleSelector,function(){
        var length = $( singleSelector ).length;
        var num = 0;
        $( singleSelector ).each(function() {
            if( $(this).prop('checked') ){
                num++;
            }  
        });

        if( num == length ){                        // 判断单选点击是否已经全部选中
            $( allSelector ).prop('checked',true );           // 全选
        }else{
            $( allSelector ).prop('checked',false);       // 反选
        }
    });
}
function check(value){
    var reg=/^[1-9]\d*$|^0$/;   // 注意：故意限制了 0321 这种格式，如不需要，直接reg=/^\d+$/;
    if(reg.test(value)==true){
        return true;
    }else{
        return false;
    }
}
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
var t_showDelProver_index = 0;//全局变量 0.表示 showDelProve弹窗不存在或隐藏  1.表示 showDelProve弹窗显示状态
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
    t_showDelProver_index = 1;//弹窗显示状态
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
        t_showDelProver_index = 0;
    });
    $('.cancel_btn').unbind('click').bind('click',function(){
        $('.del_popover').hide();
        t_showDelProver_index = 0;
    }); 
}

//body点击事件
$("body").on('click',function(){
    if(t_showDelProver_index==1){
        $(".del_popover").remove();
        t_showDelProver_index = 0;  
    }
});

//复制链接框、删除按钮点击事件
$("body").on('click','.del_popover',function(e){
    e.stopPropagation();//组织事件冒泡 
});
// 隐藏删除prover
function hideDelProver(){
    $('.del_popover').hide();
}
// 公共函数
function center(obj,offset){
    var a = arguments[1] ? arguments[1] : 70;
    var window_height = window.screen.availHeight/2;
    var height = obj.height();
    obj.css('margin-top',window_height - height/2 -a);
}
function centerModel(that){
    var height = window.screen.height/2-that.height()-200;
    that.css('margin-top',height);
}
function showModel(obj,obj2,offset){
    obj.show();
    $('.modal-backdrop').show();
    // center(obj2,offset);
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
    wrap初始化的元素可以是ID可以使class
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
                'insertimage',
                // 'insertvideo',//视频
                'fullscreen'
            ]
        ],
        initialFrameHeight:350,//设置编辑器高度
        autoFloatEnabled:false,
        autoHeightEnabled: false
    });
    return ue;
}
function bindEventEditor(ue,$scope){
    var html = '<div>';
        html += '<p style="margin: 0 0 1em 0;">点此编辑『富文本』内容 ——&gt;</p>';
        html += '<p style="margin: 0 0 1em 0;">你可以对文字进行<strong>加粗</strong>、<em>斜体</em>、<span style="text-decoration: underline;">下划线</span>、<span style="text-decoration: line-through;">删除线</span>、文字<span style="color: rgb(0, 176, 240);">颜色</span>、<span style="background-color: rgb(255, 192, 0); color: rgb(255, 255, 255);">背景色</span>、以及字号<span style="font-size: 20px;">大</span><span style="font-size: 14px;">小</span>等简单排版操作。</p>';
        html += '<p style="margin: 0 0 1em 0;">还可以在这里加入表格了</p>';
        html += '<table><tbody>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">中奖客户</td><td width="104" valign="top" style="word-break: break-all;">发放奖品</td><td width="104" valign="top" style="word-break: break-all;">备注</td></tr>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">猪猪</td><td width="104" valign="top" style="word-break: break-all;">内测码</td><td width="104" valign="top" style="word-break: break-all;"><em><span style="color: rgb(255, 0, 0);">已经发放</span></em></td></tr>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">大麦</td><td width="104" valign="top" style="word-break: break-all;">积分</td><td width="104" valign="top" style="word-break: break-all;"><a href="javascript: void(0);" target="_blank">领取地址</a></td></tr>';
        html += '</tbody></table>';
        html += '<p style="text-align: left;"><span style="text-align: left;">也可在这里插入图片<strong style="color: red">(建议尺寸640*640)</strong>、并对图片加上超级链接，方便用户点击。</span></p></div>';
        
    ue.addListener("selectionchange", function () {
        var content = ue.getContent() ? ue.getContent():html;
        $('.editing').find('.custom-richtext').html(content);
        setTimeout(function(){
            $scope.editors[$scope.index]['content'] = content;
        },500)
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
    console.log(typeof(month));
    if(month<10){
        month = '0'+month;
    }
    if(date<10){
        date = '0'+date;
    }
    console.log(date);
    console.log(month);
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
}
 /**
    html 提示信息;
    bgcolor:提示背景颜色;值为 info，warn
    time:提示显示时间默认2秒;
**/
var txw_timeout = null;
function tipshow(html,bgcolor,time){ 
    if(txw_timeout){
        $('.info_tip').remove();
        clearTimeout(txw_timeout);
    }  
    var bgcolor = bgcolor || 'info';
    var a = arguments[2] ? arguments[2] : 2000; 
    var tipHtml = '<div class="info_tip">'+ html +'</div>';
    $('body').append(tipHtml);
    if(bgcolor == "info"){
        $('.info_tip').css('background-color','#45b182')
    }else if(bgcolor == "warn"){
        $('.info_tip').css('background-color','#ff1313')
    }
    // var w = $(".info_tip").width()/2;
    // if(w>450)
    //     $(".info_tip").css({"margin-left":-w+"px"});  
    $('.info_tip').show(100);  
    txw_timeout = setTimeout(function(){
        $('.info_tip').remove();
    },a); 
}

//上传成功后清空列表
function closeUploader() {   
    // 移除所有缩略图并将上传文件移出上传序列
    for (var i = 0; i < uploader.getFiles().length; i++) {
        // 将图片从上传序列移除
        uploader.removeFile(uploader.getFiles()[i]);
        //uploader.removeFile(uploader.getFiles()[i], true);
        //delete uploader.getFiles()[i];
        // 将图片从缩略图容器移除
        var $li = $('#' + uploader.getFiles()[i].id);
        $li.off().remove();
    }
    
    //  setState('pedding');
     
    // 重置文件总个数和总大小
    fileCount = 0;
    fileSize = 0;
    // 重置uploader，目前只重置了文件队列
    uploader.reset();
    // 更新状态等，重新计算文件总个数和总大小
    //  updateStatus();
}
//获取以'/'分割的url参数
function getUrl(){
    var params = window.location.pathname.split("/");
    params.splice(0,1);
    return params;
}
/**
 * [fun_time 截止时间函数]
 * @param  {[type]} day    [过去(前)、未来(后) n 天; n<0 -> 前 n 天 ; n>0,后 n 天]
 * @param  {[type]} format [日期格式]
 * @return {[type]}        [返回时间数组,数组[0]第一个开始时间戳; 数组[1]结束时间戳]
 */
function fun_time( day , format ){
    var day    = arguments[0] ? arguments[0] : 7;             // 默认未来7天
    var format = arguments[1] ? arguments[1] : "-";           // 默认格式以'-'链接
    var date = [];                                            // 用于存储时间戳
    var stamp1 = new Date();                                  // 当前时间戳
    var stamp2 = new Date( stamp1 );            
    stamp2.setDate( stamp1.getDate()+day );                   // 前/后 n 天
    var date1 = stamp1.toJSON().substr(0,10).split('-');      // 日期数组
    var date2 = stamp2.toJSON().substr(0,10).split('-');
    var time1 = date1[0]+format+date1[1]+format+date1[2]+' '+ stamp1.toTimeString().substr(0,9);     // 当前时间
    var time2 = date2[0]+format+date2[1]+format+date2[2]+' '+ stamp2.toTimeString().substr(0,9);     // 前、后n天时间
    
    if( day > 0 ){          // 后（未来） n 天
        date.push(time1);   
        date.push(time2); 
    }else{                  // 前(过去) n 天
        date.push(time2); 
        date.push(time1);  
    }
    return date;            // 返回时间戳数组（开始时间-结束时间）
}

/**
 * [copyToClipboard 复制到粘切板函数]
 * @param  {[type]} obj [ 要复制的对象 ]
 * @return {[type]}     [无]
 */
function copyToClipboard( obj ) {
    var aux = document.createElement("input");                  // 创建元素用于复制
    // 获取复制内容
    var content = obj.text() || obj.val();
    // 设置元素内容
    aux.setAttribute("value", content);
    // 将元素插入页面进行调用
    document.body.appendChild(aux);
    // 复制内容
    aux.select();
    // 将内容复制到剪贴板
    document.execCommand("copy");
    // 删除创建元素
    document.body.removeChild(aux);
}

//bom元素含有t-number样式类名只能输入数字
$("body").on("keypress",".t-number",function(e){   
    var e=e || event;
    var val = parseInt($(this).val()) || 0;
    if(e.keyCode==46){
        if(val.indexOf('.')>=0){
            return false;
        }
    }   
    return (/[\d.]/.test(String.fromCharCode(event.keyCode)));
}); 

$("body").on("keypress",".t-int-number",function(e){   
    var e=e||event;
    var val = $(this).val(); 
    return (/[\d]/.test(String.fromCharCode(event.keyCode)));
}); 


/**
** 选择商品组件 2017-07-03 txw
** selGoods.open({success:callback,href:href,selectNum:1,is_distribution:0});
** 参数 object 1.success 确认商品后的回调函数 2.href 新增商品的链接 3.is_multiple 选择数量 默认0单选 1多选 4.is_distribution 享立减活动添加参数.其余全不添加(zx)
** 依赖jQuery 需要引入 mctsource/css/common_selgoods.css 
** 说明:
** 1.success回调时会传回商品的json对象
** 2.该组件依赖jQuery和有对应组件样式
** 3.该组件依赖extendPagination分页插件
** 4.该组件可继续扩张商品多选功能。
** 5.不足之处请大家多多指教 
**/
(function(win,doc,$){
    function selGoods(){ 
    	var is_distribution; //享立减活动添加参数.其余全不添加
        this.showPage = [1]; 
        this.selGoods = [];
        this.goodsData = {}; 
        this.is_multiple = 0; //是否多选 0.单选 1.多选
        this.ids = ""; //指定的商品
        this.options = null; //记录传的参数
        this.init = function(obj){ //初始化数据
            this.showPage =[1];
            this.selGoods =[];
            this.is_multiple = obj.is_multiple || 0; 
            this.ids = obj.ids || "";
        }
        this.open = function(obj){ //打开商品选择界面  
            var _this = this;
            if(obj.postData != undefined){
                this.options = obj.postData;
            }else{
                obj.postData = {};
            }
            _this.init(obj);
            //窗口骨架
            var el = this.createEl({el:"div",id:"myModal",class:"modal"}); 
            el.style.display ="block";
            var el1 = this.createEl({el:"div",class:"modal-dialog"}); 
            var el2 = this.createEl({el:"div",class:"modal-content"}); 
            var html = '<div class="modal-header"><button type="button" class="close">&times;</button>';
            html+='<ul class="list"><li class="js_small list_active" style="padding:0;">已上架商品&#X3000;</li>';
            html+=' <li class="js_manage" style="display:block;border-right:none;padding:0 0 0 14px;"><a class="co_38f" target="_blank" href="/merchants/product/create">新建商品</a>';    
            html+='</li></ul></div>';
            el2.innerHTML = html;
            var modal_body = this.createEl({el:"div",class:"modal-body"});
            var table = this.createEl({el:"table",class:"sel-goods-table"});
            var thead = this.createEl({el:"thead"});
            html = '<tr><th class="title"><span  class="line30">标题</span>&nbsp;&nbsp;<a class="co_38f line30 refresh" href="javascript:void(0);" >刷新</a></th>';
            html+='<th class="set_time" style="line-height: 30px;">创建时间</th><th class="search"><input type="text" /><button class="btn btn-default">搜</button></th></tr>';
            thead.innerHTML = html;
            var tbody = this.createEl({el:"tbody",class:"small"});
            table.appendChild(thead);
            tbody.style.height="386px"; 
            var data = {
                type: 1,//参数类型
                wid: $('#wid').val(),//页面标志
                page: 1,
                title: '', //搜索内容
                filter_cam : obj.postData.filter_cam || ''
            }
            data = $.extend({},data, this.options);//将一个空对象做为第一个参数
            if(obj.is_distribution == 0){//享立减活动添加参数,其余都不添加
            	data.is_distribution = 0;
            	is_distribution = 0;
            }
            var json =  this.getDataJson(data); 
            var resultStr = this.addDom(1, json.data[0].data, json.title);
            tbody.innerHTML = resultStr;
            table.appendChild(tbody);
            modal_body.appendChild(table);
            var modal_footer = this.createEl({el:"div",class:"modal-footer clearfix"});
            html = '<span class="use-btn">确定使用</span>';
            modal_footer.innerHTML = html;
            var myModalPage = this.createEl({el:"div",class:"myModalPage"});
            //myModalPage.innerHTML = this.getPageStr(myModalPage);
            modal_footer.appendChild(myModalPage);
            el2.appendChild(modal_body);
            el2.appendChild(modal_footer);
            el1.appendChild(el2);
            var mark = this.createEl({el:"div",class:"mark"});
            el.appendChild(mark);
            el.appendChild(el1);
            doc.body.appendChild(el);
            this.getPageStr(json,data.wid);
            $(doc).on("click", '.js-btn-default', function () { 
                var arr_data = _this.goodsData.data[0].data[$(this).attr("data-i")];
                if ($(this).hasClass('btn-primary')) {
                    $(this).removeClass('btn-primary');
                    var rindex = 0;
                    for(var i=0;i<_this.selGoods.length;i++){
                        if(arr_data.id == _this.selGoods[i].id){
                            rindex = i;
                            break;
                        }
                    } 
                    _this.selGoods.splice(rindex,1);
                    $(this).text("选取");
                } else { 
                    if(_this.is_multiple==0){
                        $(".js-btn-default").removeClass('btn-primary').text("选取"); 
                        _this.selGoods[0]=arr_data; 
                    }else{
                        _this.selGoods.push(arr_data); 
                    }
                    $(this).addClass('btn-primary');   
                    $(this).text("取消");
                } 
                if (_this.selGoods.length>0) {
                    $(".use-btn").show();
                } else {
                    $(".use-btn").hide();
                }
            });
            // 确定选中的商品
            $(doc).on('click', '.use-btn', function () {
                _this.unloadEvent(); 
                $("#myModal").remove();
                obj.success(_this.selGoods);
            });
            $(doc).on('click','.close',function(){
                _this.unloadEvent();
                $("#myModal").remove(); 
            }); 
            // 刷新
            $(doc).on("click",".sel-goods-table .title .refresh",function(){   
                _this.setData(1,"");
                $(".sel-goods-table .search input[type='text']").val('');
            });
            // 搜索
            $(doc).on("click", ".sel-goods-table .search .btn", function () {
                var title = $('.search input').val();
                _this.setData(1,title);
            });
        }
        this.setData = function(page,title){
            var data = {
                type: 1,//参数类型
                wid: $('#wid').val(),//页面标志
                page: page,
                title: title //搜索内容
            }
            if(this.options != null){
                data = $.extend({},data, this.options);//将一个空对象做为第一个参数
            }
            var json = this.getDataJson(data);
            var resultStr = this.addDom(1, json.data[0].data, json.title);
            $('.sel-goods-table tbody').html(resultStr);
            this.getPageStr(json,data.wid);
        }
        this.createEl = function(obj){
            var el = doc.createElement(obj.el);
            if(obj.id) {el.id = obj.id;}
            if(obj.class) {el.className=obj.class;}
            return el;
        } 
        this.getDataJson = function(data){
            var result ={},that=this;   
            var url = "/merchants/linkTo/get";
            if(is_distribution == 0){//享立减商品添加参数
            	data.is_distribution = 0;
            }
            if(that.ids!=""){
                url +="?ids="+that.ids;
            }
            $.ajax({
                type:"get",
                url:url,
                data:data,
                async:false,//同步
                dataType:"json",
                success:function(json){
                    if(json.status==1){
                        that.goodsData = json;
                        result = json;
                    } 
                },
                error:function(){
                    console.log("异常");
                }
            });
            return result;
        }
        this.getPageStr = function(res,wid){
            var _this = this;
            _this.showPage=[];
            $('.myModalPage').extendPagination({
                totalCount: res.data[0].total,//数据总数
                showCount: res.data[0].last_page,//展示页数
                limit: res.data[0].per_page,//每页展示条数
                callback: function (page, limit, totalCount) { 
                    var title = $(".search input").val();
                    if(_this.options){
                        var url = '/merchants/linkTo/get?type=1&wid=' + wid + '&page=' + page + "&title=" + title + '&filter_negotiable='+_this.options.filter_negotiable;
                    	if(is_distribution == 0){//享立减活动添加参数,其余都不添加
			            	url = '/merchants/linkTo/get?type=1&wid=' + wid + '&page=' + page + "&title=" + title + '&filter_negotiable='+_this.options.filter_negotiable+'&is_distribution=0';
			            }
                    }else{
                        var url = '/merchants/linkTo/get?type=1&wid=' + wid + '&page=' + page + "&title=" + title;
                    }
                    if(_this.ids!=""){
                        url +="&ids="+_this.ids;
                    } 
                    $.get(url, function (res) {
                        if (res.status == 1) {
                            _this.goodsData = res;
                            _this.successBase(res, title);
                            if (_this.showPage.indexOf(page) == -1) {
                                _this.showPage.push(page);
                            }
                        }
                    });
                }
            });
        }
        this.addDom = function(page, data, title) { 
            var resultStr ="";
            for (var i = 0; i < data.length; i++) {
                var _img =  '<img src="' + _host +  data[i].img + '" />';//判断是否有图片
                resultStr += '<tr data-id=' + data[i].id + ' class=page' + page + '>\<td>' + _img;
                resultStr += '<a class="co_38f js-goods-title" href="' + data[i].url + '" target="_blank">' + data[i].title;
                resultStr += '</a>\</td>\<td>' + data[i].created_at;
                resultStr += '</td>\<td><button data-i="'+i+'" class="btn btn-default js-btn-default">选取</button></td>\</tr>';
            }
            return resultStr;
        }
        this.successBase = function(res, title) {//交互成功后执行的基础方法 用于分页
            var page = res.data[0].current_page;
            var current_page = res.data[0].current_page;
            var data = res.data[0].data;
            $("#myModal .small tr").addClass("hide");
            var resultStr = this.addDom(page, data, title);
            $("#myModal .small").append(resultStr);
        }
        this.unloadEvent = function(){
            $(doc).off('click', '.use-btn');
            $(doc).off('click', '.close');
            $(doc).off('click', '.sel-goods-table .title .refresh');
            $(doc).off('click', '.sel-goods-table .search .btn');
            $(doc).off('click', '.js-btn-default');
        }
    }
    win.selGoods = new selGoods();
})(window,document,jQuery); 
/*----选择商品新结束-----*/ 

/**
** 选择子分销商组件 copy选择商品组件    zx
** distribute.open({success:callback,href:href,selectNum:1});
** 参数 object 1.success 确认商品后的回调函数 2.href 新增商品的链接 3.is_multiple 选择数量 默认0单选 1多选
** 依赖jQuery 需要引入 mctsource/css/common_selgoods.css 
** 说明:
** 1.success回调时会传回商品的json对象
** 2.该组件依赖jQuery和有对应组件样式
** 3.该组件依赖extendPagination分页插件
** 4.该组件可继续扩张商品多选功能。
** 5.不足之处请大家多多指教 
**/
(function(win,doc,$){
    function distribute(){ 
        this.showPage = [1]; 
        this.distribute = [];
        this.goodsData = {}; 
        this.is_multiple = 0; //是否多选 0.单选 1.多选
        this.ids = ""; //指定的商品
        this.init = function(obj){ //初始化数据
            this.showPage =[1];
            this.distribute =[];
            this.is_multiple = obj.is_multiple || 0; 
            this.ids = obj.ids || "";
        }
        this.open = function(obj){ //打开商品选择界面  
            var _this = this;
            _this.init(obj);
            //窗口骨架
            var el = this.createEl({el:"div",id:"myModal",class:"modal"}); 
            el.style.display ="block";
            var el1 = this.createEl({el:"div",class:"modal-dialog"}); 
            var el2 = this.createEl({el:"div",class:"modal-content"}); 
            var html = '<div class="modal-header"><button type="button" class="close">&times;</button>';
            html+='<ul class="list"><li class="js_small list_active" style="padding:0;">子分销商列表</li>'; 
            html+='</ul></div>';
            el2.innerHTML = html;
            var modal_body = this.createEl({el:"div",class:"modal-body"});
            var table = this.createEl({el:"table",class:"sel-goods-table"});
            var thead = this.createEl({el:"thead"});
            html = '<tr><th class="title"><span  class="line30">标题&nbsp;&nbsp;</span><a class="co_38f line30 refresh" href="javascript:void(0);" >刷新</a></th>';
            html += '<th class="set_time" style="line-height: 30px;">手机号</th>';
            html += '<th class="set_time" style="line-height: 30px;">来源</th>';
            html += '<th class="set_time" style="line-height: 30px;">累计佣金</th>';
            html +='<th class="set_time" style="line-height: 30px;">注册时间</th><th class="search"><input type="text" /><button class="btn btn-default">搜</button></th></tr>';
            thead.innerHTML = html;
            var tbody = this.createEl({el:"tbody",class:"small"});
            table.appendChild(thead);
            tbody.style.height="386px"; 
            var data = {
                type: 1,//参数类型
                wid: $('#wid').val(),//页面标志
                page: 1,
                title: '' //搜索内容
            }
            var json =  this.getDataJson(data); 
            var resultStr = this.addDom(1, json.data[0].data, json.title);
            tbody.innerHTML = resultStr;
            table.appendChild(tbody);
            modal_body.appendChild(table);
            var modal_footer = this.createEl({el:"div",class:"modal-footer clearfix"});
            html = '<span class="use-btn">确定使用</span>';
            modal_footer.innerHTML = html;
            var myModalPage = this.createEl({el:"div",class:"myModalPage"});
            //myModalPage.innerHTML = this.getPageStr(myModalPage);
            modal_footer.appendChild(myModalPage);
            el2.appendChild(modal_body);
            el2.appendChild(modal_footer);
            el1.appendChild(el2);
            var mark = this.createEl({el:"div",class:"mark"});
            el.appendChild(mark);
            el.appendChild(el1);
            doc.body.appendChild(el);
            this.getPageStr(json,data.wid);
            $(doc).on("click", '.js-btn-default', function () { 
                var arr_data = _this.goodsData.data[0].data[$(this).attr("data-i")];
                if ($(this).hasClass('btn-primary')) {
                    $(this).removeClass('btn-primary');
                    var rindex = 0;
                    for(var i=0;i<_this.distribute.length;i++){
                        if(arr_data.id == _this.distribute[i].id){
                            rindex = i;
                            break;
                        }
                    } 
                    _this.distribute.splice(rindex,1);
                    $(this).text("选取");
                } else { 
                    if(_this.is_multiple==0){
                        $(".js-btn-default").removeClass('btn-primary').text("选取"); 
                        _this.distribute[0]=arr_data; 
                    }else{
                        _this.distribute.push(arr_data); 
                    }
                    $(this).addClass('btn-primary');   
                    $(this).text("取消");
                } 
                if (_this.distribute.length>0) {
                    $(".use-btn").show();
                } else {
                    $(".use-btn").hide();
                }
            });
            // 确定选中的商品
            $(doc).on('click', '.use-btn', function () {
                _this.unloadEvent(); 
                $("#myModal").remove();
                obj.success(_this.distribute);
            });
            $(doc).on('click','.close',function(){
                _this.unloadEvent();
                $("#myModal").remove(); 
            }); 
            // 刷新
            $(doc).on("click",".sel-goods-table .title .refresh",function(){   
                _this.setData(1,"");
                $(".sel-goods-table .search input[type='text']").val('');
            });
            // 搜索
            $(doc).on("click", ".sel-goods-table .search .btn", function () {
                var title = $('.search input').val();
                _this.setData(1,title);
            });
        }
        this.setData = function(page,title){
            var data = {
                type: 1,//参数类型
                wid: $('#wid').val(),//页面标志
                page: page,
                nickname: title //搜索内容
            }
            var json = this.getDataJson(data);
            var resultStr = this.addDom(1, json.data[0].data, json.title);
            $('.sel-goods-table tbody').html(resultStr);
            this.getPageStr(json,data.wid);
        }
        this.createEl = function(obj){
            var el = doc.createElement(obj.el);
            if(obj.id) {el.id = obj.id;}
            if(obj.class) {el.className=obj.class;}
            return el;
        } 
        this.getDataJson = function(data){
            var result ={},that=this;   
            var url = "/merchants/distribute/getDistributeMember?pageSize=5";
            if(that.ids!=""){
                url +="?ids="+that.ids;
            }
            $.ajax({
                type:"get",
                url:url,
                data:data,
                async:false,//同步
                dataType:"json",
                success:function(json){
                    if(json.status==1){
                        that.goodsData = json;
                        result = json;
                    } 
                },
                error:function(){
                    console.log("异常");
                }
            });
            return result;
        }
        this.getPageStr = function(res,wid){
            var _this = this;
            _this.showPage=[];
            $('.myModalPage').extendPagination({
                totalCount: res.data[0].total,//数据总数
                showCount: res.data[0].last_page,//展示页数
                limit: res.data[0].per_page,//每页展示条数
                callback: function (page, limit, totalCount) { 
                    var title = $(".search input").val();
                    var url = '/merchants/distribute/getDistributeMember?pageSize=5&page='+page + "&nickname=" + title;
                    if(_this.ids!=""){
                        url +="&ids="+_this.ids;
                    } 
                    $.get(url, function (res) {
                        if (res.status == 1) {
                            _this.goodsData = res;
                            _this.successBase(res, title);
                            if (_this.showPage.indexOf(page) == -1) {
                                _this.showPage.push(page);
                            }
                        }
                    });
                }
            });
        }
        this.addDom = function(page, data, title) { 
            var resultStr ="";
            for (var i = 0; i < data.length; i++) {
                var _img =  '<img src="'+data[i].headimgurl+'">';//判断是否有图片
                resultStr += '<tr data-id=' + data[i].id + ' class=page' + page + '>\<td>'+_img;
                resultStr += '<a class="co_38f js-goods-title">' + data[i].nickname + '</a></td>';
                resultStr += '<td>' + data[i].mobile + '</td>';
                resultStr += '<td>' + data[i].source + '</td>';
                resultStr += '<td>' + data[i].money + '</td>';
                resultStr += '<td>' + data[i].created_at + '</td>';
                resultStr += '<td><button data-i="'+i+'" class="btn btn-default js-btn-default">选取</button></td>\</tr>';
            }
            return resultStr;
        }
        this.successBase = function(res, title) {//交互成功后执行的基础方法 用于分页
            var page = res.data[0].current_page;
            var current_page = res.data[0].current_page;
            var data = res.data[0].data;
            $("#myModal .small tr").addClass("hide");
            var resultStr = this.addDom(page, data, title);
            $("#myModal .small").append(resultStr);
        }
        this.unloadEvent = function(){
            $(doc).off('click', '.use-btn');
            $(doc).off('click', '.close');
            $(doc).off('click', '.sel-goods-table .title .refresh');
            $(doc).off('click', '.sel-goods-table .search .btn');
            $(doc).off('click', '.js-btn-default');
        }
    }
    win.distribute = new distribute();
})(window,document,jQuery); 
/*----选择子分销商列表结束-----*/ 

/*
* 公用组件 2017-07-03 txw
* 依赖jQuery 需要引入 mctsource/static/css/base.css
*/
(function(win){
    var hstool =(function(){
        var hstool ={};
        hstool.config= {//默认配置
            type: 0, //类型 0.msg 1.tips提示框 2.选择商品
            title: '信息', //标题
            opacity: 0.7, //遮罩层透明度
            message: "",
            zIndex: 19891014, 
            time: 0, //0表示不自动关闭
            content:"",
            isMask: false, //是否添加点击遮罩层事件 
            done: null, //完成操作的回调函数 
            host: "",//域名
            area: [],//区域 参数 width,height
            skin:"default" //皮肤设定 后期扩展使用
        }
        //分页 页码
        hstool.page = 1;
        /*
        * this.addPublicEvent 数组 [{event:"click",name:".div_class"},{},{}] 
        * 说明：
        * 1.event 要移除的事件 
        * 2.name 对应的名称 
        */
        hstool.events = [];
        /*
        * 商品数据
        */
        hstool.goodsData = []; 
        /*
        * 商品分组数据
        */
        hstool.goodsGroupData = []; 
        /*
        * 分页数据
        */
        hstool.pageData = "";
        /*
        * 商品sku数据
        */
        hstool.skuData = [];
        /*
        * 商品sku数据
        */
        hstool.options = null; //接受外部传入ajax数据
        /*
        * 监听公共事件
        */
        hstool.isPublicEvent = false, //是否添加过事件监听 
        hstool.addPublicEvent = function(){
            var that = this;
            if(!that.isPublicEvent){
                //遮罩点击事件
                if(that.config.isMask){
                    $("body").on("click",".hstool-dialog-mask",function(){
                        that.close();
                    });
                    that.events.push({event:"click",name:".hstool-dialog-mask"});
                }
                //按钮x点击事件 
                $("body").on("click",".hstool-dialog-close",function(){  
                    that.close();
                });
                that.events.push({event:"click",name:".hstool-dialog-close"});
                //按钮取消点击事件
                $("body").on("click",".hstool-dialog .btn-cancel",function(){
                    that.close();
                });
                that.events.push({event:"click",name:".hstool-dialog .btn-cancel"}); 
                //按钮取消点击事件
                $("body").on("click",".hstool-dialog .btn-yes",function(){
                    if(typeof that.config.btn1 !=="undefined"){
                        that.config.btn1();
                    }
                });
                that.events.push({event:"click",name:".hstool-dialog .btn-yes"}); 
                that.isPublicEvent = true;
            }
        } 

        /*
        * 移除事件绑定(弹窗移除时)
        */
        hstool.removeEvent = function(){
            //事件暂时不移除
            // var arr = this.events;
            // for (var i =0; i < arr.length; i++) { 
            //     $("body").off(arr[i].event,arr[i].name);
            // } 
            this.config.isEvent = true;
        }
        /*
        * 初始化参数
        */
        hstool.init=function(config){
            for(var key in config){
                this.config[key] = config[key]; 
            } 
        }
        /*
        * 提示框
        */
        hstool.msg=function(msg,config){   
            this.init(config); 
            this.createMask();
            var html = this.createFrame(); 
            $("body").append(html);
            $(".hstool-dialog-content").html(msg);
            $(".hstool-dialog-footer").html('<button class="btn btn-cancel">取消</button><button class="btn btn-yes">确定</button>');
            this.setPosition();
            this.addPublicEvent(); 
        }
        /*
        * 打开层
        */
        hstool.open=function(config){
            this.init(config); 
            this.createMask(); 
            var html = this.createFrame(); 
            $("body").append(html);
            $(".hstool-dialog-content").html(this.config.content);
            if(this.config.area.length>0){
                $(".hstool-dialog-content").css({"width":this.config.area[0],"height":this.config.area[1]});
            }
            if(typeof this.config.btn !=="undefined"){
                html="";
                for(var i=0;i<this.config.btn.length;i++){
                    if(i==0)
                        html+='<button class="btn btn-yes">'+this.config.btn[i]+'</button>';
                    else if(i>0)
                        html+='<button class="btn btn-cancel">'+this.config.btn[i]+'</button>';
                }
                $(".hstool-dialog-footer").append(html);  
                if(typeof config.footAlign != "undefined")
                    $(".hstool-dialog-footer").css({"text-align":config.footAlign});
            }
            this.setPosition();
            this.addPublicEvent(); 
        } 
        /*
        * 创建框架(骨架)
        */
        hstool.createFrame =function(){ 
            var html ="";
            var config = this.config;
            html= '<div class="hstool-dialog">';
            html+='<div class="hstool-dialog-header"><a class="hstool-dialog-close">×</a><h3 class="hstool-dialog-title">'+config.title+'</h3></div>';
            html+='<div class="hstool-dialog-content">';
            html+='</div>';
            html+='<div class="hstool-dialog-footer"></div>';
            html+='</div>';
            return html;
        } 
        /*
        * 设置窗口位置及其他样式
        */
        hstool.setPosition = function(){ 
            var pos = hstool.offset();
            $(".hstool-dialog").css({"z-index":this.config.zIndex+1,"margin-left":"-"+pos.ew/2+"px","margin-top":"-"+pos.eeh/2+"px"}); 
        }
        /*
        * 创建遮罩层
        */
        hstool.createMask=function(){
            $("body").append('<div class="hstool-dialog-mask"></div>'); 
            $(".hstool-dialog-mask").css({"z-index":this.config.zIndex,"opacity":this.config.opacity});
        }
        /*
        * 移除遮罩层
        */
        hstool.removeMask=function(){
            $(".hstool-dialog-mask").remove();
        }
        /**
         * loading 动画 顶部绿条过
         * 使用 css3 animation动画
         */
        hstool.hsload = function(){
            if($("body").hasClass('hs-load')){
                $("hs-load").remove();
            }
            $("body").append('<div class="hs-load"></div>');
            setTimeout(function(){ 
                $(".hs-load").remove();
            },300);
        }   
        /*
        * loading 加载层
        */
        hstool.load = function(config){
            config = config || {};
            var that = this;
            that.init(config);  
            $("body").append('<div class="hstool-dialog-loading"></div>'); 
            $(".hstool-dialog-loading").css({"z-index":that.config.zIndex+2});
            if(that.config.time>0){
                setTimeout(function(){
                    that.closeLoad();
                },that.config.time);
            }
        }
        /*
        * 关闭加载层 
        */
        hstool.closeLoad = function(){
            $(".hstool-dialog-loading").remove();
        }
        /*
        * 计算坐标
        */
        hstool.offset=function(){
            var obj ={};
            //计算屏幕高宽
            obj.wh = $(window).height();
            obj.ww = $(window).width();
            //计算元素高宽
            obj.eh = $(".hstool-dialog").height();
            obj.ew = $(".hstool-dialog").width();
            //计算content高宽
            obj.ch = $(".hstool-dialog-content").height();
            obj.cw = $(".hstool-dialog-content").width();

            obj.eeh = obj.eh >= obj.wh-100?obj.wh-100:obj.eh; 

            return obj;
        }
        /*
        * 关闭层
        */
        hstool.close=function(){  
            this.removeEvent(); 
            this.removeMask();
            $(".hstool-dialog").remove();
        }
        /*
        * 加载样式
        */
        hstool.loadStyle=function(){}
        /*
        * 设置皮肤
        */
        hstool.setSkin=function(){}
    /*---------------选择商品开始----------------*/
        /*
        * 选择商品组件
        */
        hstool.selectGoods = function(config){ 
            var that = this;
            that.init(config); 
            that.createMask();
            if(config.postData != undefined){
                this.options = $.extend({},this.options, config.postData);//将一个空对象做为第一个参数
            }  
            var html = that.createFrame();  
            $("body").append(html);
            $(".hstool-dialog-content").css({"min-width":"500px","min-height":"200px"});
            that.setPosition();
            that.load(); 
            setTimeout(function(){
                that.getGoodsGroupInfo(); 
                that.getGoodsData('','',''); 
                html=that.getSelectGoodsStr();
                that.closeLoad();
                $(".hstool-dialog-content").append(html); 
                that.setGoodsPageInfo();
                that.setPosition();
                that.addPublicEvent();
                that.addSelectGoodsEvent();  
            },0);  
        } 
        /*
        * 选择商品组件事件监听
        */
        hstool.isSelectGoodsEvent = false;
        hstool.addSelectGoodsEvent = function(){
            var that = this;
            if(!that.isSelectGoodsEvent){
                //选择商品按钮点击事件
                $("body").on("click",".hstool-btn-select-goods",function(){
                    var index = $(this).parents("tr").attr("data-i");
                    that.close();
                    that.config.done(that.goodsData[index]); 
                });
                that.events.push({event:"click",name:".hstool-btn-select-goods"});  
                //分页:点击分页对应按钮事件
                $("body").on("click",".hstool-pagenum .pagination li a",function(){
                    var page = $(this).attr("data-page");
                    if(page=="»"){
                        that.page++;
                    }else if(page =="«"){ 
                        that.page--;
                    }else{
                        that.page = page;
                    }
                    var group_id = $(".hstool-dialog .js_group_id").val();
                    var keyword_type = $(".hstool-dialog .js_keyword_type").val();
                    var keyword = $(".hstool-dialog .js_keyword").val();
                    that.renderGoods(group_id,keyword_type,keyword);
                });
                that.events.push({event:"click",name:".hstool-pagenum .pagination li a"}); 
                //搜索：点击搜索事件
                $("body").on("click",".hstool-dialog-search-btn",function(){
                    that.page = 1;
                    var group_id = $(".hstool-dialog .js_group_id").val();
                    var keyword_type = $(".hstool-dialog .js_keyword_type").val();
                    var keyword = $(".hstool-dialog .js_keyword").val();
                    that.renderGoods(group_id,keyword_type,keyword);
                }); 
                that.events.push({event:"click",name:".hstool-dialog-search-btn"}); 

                that.isSelectGoodsEvent = true;
            }
        }
        /*
        * 获取选择商品字符串 
        */
        hstool.getSelectGoodsStr = function(){
            var html ='<div class="hstool-dialog-content-search"><select class="form-control w200 iblock js_group_id"><option value="">所有分组</option>';
            var data = this.goodsGroupData;
            for(var i=0;i<data.length;i++){
                html+='<option value="'+data[i].id+'">'+data[i].title+'</option>';
            }
            html+='</select>';
            html+='<select class="form-control w200 iblock js_keyword_type"><option value="product_title">商品标题</option><option value="product_no">商品编码</option></select>';
            html+='<input type="text" class="form-control w200 iblock js_keyword" placeholder="请输入商品名称" /><input type="button" class="btn btn-primary fs12 hstool-dialog-search-btn" style="margin-top:-2px;" value="搜索" /></div>';
            html+= '<div class="hstool-select-goods-box">'+this.getSelectGoodsStrBody()+'</div>';
            return html;
        }
        /*
        * 获取选择商品数据字符串
        */
        hstool.getSelectGoodsStrBody = function(){
            var html='<div class="hstool-select-goods"><table class="hstool-table"><thead><tr style="border-top:1px solid #ddd;"><th class="text-left" colspan="2">商品信息</th><th class="text-center cell-20">库存</th><th class="text-center cell-20" style="text-align:right;">操作</th></tr></thead>';
            html+='<tbody>';  
            data = this.goodsData;
            for(var i=0;i<data.length;i++){
                html+='<tr data-i="'+i+'"><td class="goods-img-td"><img class="goods-img" src="'+this.config.host+data[i].img+'"></td>';
                html+='<td><p class="goods-title">'+data[i].title+'</p><p class="goods-price">￥'+data[i].price+'</p></td>';
                html+='<td>'+data[i].stock+'</td><td  style="text-align:right;"><input type="button" class="btn btn-primary btn-sm hstool-btn-select-goods" value="选择商品"/></td></tr>';
            } 
            html+='</tbody></table></div>';
            // 分页
            html+='<div class="hstool-pagenum">'+this.pageData+'</div>';
            return html;
        }

        /*
        * 重新渲染商品列表及分页
        * 用于商品选择组件的搜索和分页功能
        */
        hstool.renderGoods = function(group_id,keyword_type,keyword){
            this.getGoodsData(group_id,keyword_type,keyword); 
            var html = this.getSelectGoodsStrBody();
            $(".hstool-select-goods-box").html(html); 
            this.setGoodsPageInfo();
        }
        /*
        * 处理分页信息 将a标签的href改为 javascript:; 加入data-page 属性
        */
        hstool.setGoodsPageInfo = function(){
            var that = this;
            $(".hstool-pagenum .pagination li a").each(function(){
                var page = $(this).html();
                $(this).attr("href","javascript:;");
                $(this).attr("data-page",page);
            });
        }
        /*
        * 获取商品分组信息
        */
        hstool.getGoodsGroupInfo = function(){
            var that = this;
            if(that.goodsGroupData.length==0){
                $.ajax({
                    type:'get',
                    url:'/merchants/product/getallgroup',
                    data:{},
                    async: false,
                    dataType:"json",
                    success:function(res){ 
                        if(res.status==1){
                            that.goodsGroupData = res.data;
                        }
                    },
                    error:function(){
                        console.log("获取商品数据异常");
                    }
                });
            }
        }   
        /*
        * 获取商品数据
        */
        hstool.getGoodsData = function(group_id,keyword_type,keyword){  
            var that = this;
            var data={
                group_id: group_id,//商品分组
                keyword_type: keyword_type || 'product_title',//product_title product_no
                keyword: keyword
            }
            data = $.extend({},data, this.options);//将一个空对象做为第一个参数
            $.ajax({
                type:'get',
                url:'/merchants/marketing/seckill/products?page='+that.page,
                data:data,
                async: false,
                dataType:"json",
                headers: {
                    'X-CSRF-TOKEN': this.config._token
                },
                success:function(res){
                    if(res.status==1){
                        if(res.data.length>0){
                            that.goodsData = res.data[0].data;
                            that.pageData = res.data[1];
                        }
                    }
                },
                error:function(){
                    console.log("获取商品数据异常");
                }
            }); 
        }
    /*---------------选择商品结束----------------*/


    /*---------------设置秒杀价格和库存开始----------------*/
        /*
        * 设置秒杀Sku组件
        */
        hstool.setSeckillSku = function(config){  
            var that = this;
            this.init(config); 
            this.createMask();  
            var html = this.createFrame();   
            $("body").append(html);
            $(".hstool-dialog-content").css({"min-width":"500px","min-height":"200px"});
            that.setPosition();
            this.load();
            setTimeout(function(){ 
                that.getSkuInfo(); 
                html=that.getSeckillStr();
                that.closeLoad();
                $(".hstool-dialog-content").append(html);
                that.renderSku(); 
                $(".hstool-dialog-content").css({
                    "margin-bottom":"50px",
                    "min-height":"auto",
                    "min-width":"auto"
                }); 
                that.setPosition();
                that.addPublicEvent();
                that.addSkuEvent(); 
            },200);
            
        }
        /*
        * 设置sku价格和库存组件事件监听
        */
        hstool.isSkuEvent = false;
        hstool.addSkuEvent = function(){
            var that = this;
            if(!that.isSkuEvent){
                //设置秒杀价格和库存导航li点击事件
                $("body").on("click",".hstool-ul-li",function(){
                    var index = $(this).index();
                    if(index==1){
                        that.renderSku();
                    }
                    $(this).addClass('active').siblings().removeClass('active'); 
                    $(".hstool-dialog-sku .sku-table").eq(index).removeClass("none").siblings().addClass('none');
                    $(".hstool-dialog-batch").eq(index).removeClass('none').siblings('.hstool-dialog-batch').addClass('none'); 
                    that.setPosition();
                });
                that.events.push({event:"click",name:".hstool-ul-li"});
                //选择参加秒杀规格 
                $("body").on("click",".js-add-sku",function(){
                    var index = $(this).attr("data-index");
                    that.skuData[index].checked = 1;
                    $(this).parent().addClass('none').siblings().removeClass('none');  
                });
                that.events.push({event:"click",name:".js-add-sku"}); 
                //设置秒杀价格和库存选择全部按钮监听事件
                $("body").on("click",".js-select-all",function(){
                    var index = $(this).attr("data-index");
                    var _this = this;
                    $(".sku-table").eq(index).find("input[type='checkbox']").each(function(){
                        this.checked= _this.checked;
                    });
                });
                that.events.push({event:"click",name:".js-select-all"}); 
                //设置秒杀价格和库存 批量参加按钮点击事件
                $("body").on("click",".js-batch-add",function(){
                    $(".sku-table").eq(0).find("input[type='checkbox']").each(function(key,el){
                        if(this.checked){
                            that.skuData[key].checked = 1;
                            $(".js-add-sku").eq(key).parent().addClass('none').siblings().removeClass('none');  
                        }
                    });
                });
                that.events.push({event:"click",name:".js-batch-add"});  

                //设置秒杀价格和库存 取消参加按钮点击事件
                $("body").on("click","#sku-table1 .js-remove-sku",function(){ 
                    $(this).parent().addClass('none').siblings().removeClass('none'); 
                    var index = $(this).attr("data-index");
                    that.skuData[index].checked = 0; 
                });
                that.events.push({event:"click",name:"#sku-table1 .js-remove-sku"});   

                //设置秒杀价格和库存 取消参加按钮点击事件
                $("body").on("click","#sku-table2 .js-remove-sku",function(){ 
                    var index = $(this).attr("data-index");
                    that.skuData[index].checked = 0;
                    $(this).parents("tr").remove();   
                    $(".sku-table").eq(0).find(".js-add-sku").eq(index).parent().removeClass('none').siblings().addClass('none');
                });
                that.events.push({event:"click",name:"#sku-table2 .js-remove-sku"}); 

                //设置秒杀价格和库存 批量取消按钮点击事件
                $("body").on("click",".js-batch-remove",function(){
                    $(".sku-table").eq(1).find("input[type='checkbox']").each(function(key,el){
                        var index = $(this).parents("tr").attr("data-index");
                        if(this.checked){
                            that.skuData[index].checked = 0;
                            $(this).parents("tr").remove();
                            $(".sku-table").eq(0).find(".js-add-sku").eq(index).parent().removeClass('none').siblings().addClass('none'); 
                        }
                    });
                });
                that.events.push({event:"click",name:".js-batch-remove"});

                //设置秒杀价格和库存 下一步点击事件
                $("body").on("click",".sku-btn-next",function(){
                    $(".hstool-ul-li").eq(1).trigger("click");
                });
                that.events.push({event:"click",name:".sku-btn-next"}); 
                //设置秒杀价格和库存 上一步点击事件 
                $("body").on("click",".sku-btn-back",function(){
                    $(".hstool-ul-li").eq(0).trigger("click");
                });
                that.events.push({event:"click",name:".sku-btn-back"});  
                //秒杀价格文本框内容发生变化事件 
                $("body").on("change",".js_input_sprice",function(){
                    var index = $(this).parents("tr").attr("data-index");
                    that.skuData[index].seckill_price = this.value; 
                });
                that.events.push({event:"change",name:".js_input_sprice"});  
                //秒杀库存文本框内容发生变化事件 
                $("body").on("change",".js_input_sstock_num",function(){ 
                    var index = $(this).parents("tr").attr("data-index");
                    that.skuData[index].seckill_stock_num = this.value; 
                });
                that.events.push({event:"change",name:".js_input_sstock_num"});  
                //批量设置：秒杀价格按钮点击事件
                $("body").on("click",".js_batch_price",function(){
                    $(this).parent().addClass('none');
                    $(".js_span_sprice").removeClass('none');
                });
                that.events.push({event:"click",name:".js_batch_price"}); 

                //批量设置：取消秒杀价格按钮点击事件
                $("body").on("click",".js_batch_cancel_price",function(){
                    $(this).parent().addClass('none');
                    $(".js_batch_span").removeClass('none');
                });
                that.events.push({event:"click",name:".js_batch_cancel_price"}); 

                //点击批量设置：秒杀价格保存按钮事件
                $("body").on("click",".js_batch_save_price",function(){
                    var value = $(this).siblings('input').val();
                    $(".js_input_sprice").val(value);
                    var data = that.skuData;
                    for(var i=0;i<data.length;i++){
                        if(data[i].checked){
                            data[i].seckill_price = value;
                        }
                    }
                    $(this).parent().addClass('none');
                    $(".js_batch_span").removeClass('none');
                });
                that.events.push({event:"click",name:".js_batch_save_price"});    
                

                //批量设置：秒杀库存按钮点击事件
                $("body").on("click",".js_batch_stock_num",function(){
                    $(this).parent().addClass('none');
                    $(".js_span_sstock_num").removeClass('none');
                }); 
                that.events.push({event:"click",name:".js_batch_stock_num"});  

                //批量设置：取消秒杀库存按钮点击事件
                $("body").on("click",".js_batch_cancel_stock_num",function(){
                    $(this).parent().addClass('none');
                    $(".js_batch_span").removeClass('none');
                });
                that.events.push({event:"click",name:".js_batch_cancel_stock_num"});   
                //点击批量设置：秒杀库存保存按钮事件
                $("body").on("click",".js_batch_save_stock_num",function(){
                    var value = $(this).siblings('input').val();
                    $(".js_input_sstock_num").val(value);
                    var data = that.skuData;
                    for(var i=0;i<data.length;i++){
                        if(data[i].checked){
                            data[i].seckill_stock_num = value;
                        }
                    }
                    $(this).parent().addClass('none');
                    $(".js_batch_span").removeClass('none');
                });
                that.events.push({event:"click",name:".js_batch_save_stock_num"});   
                //无规格保存事件
                $("body").on("click",".js_sku_save_sig",function(){
                    var bl = true;
                    var price = $(".js_sku_save_price").val();
                    var msg ="请输入秒杀价格";
                    var div =$(".js_sku_save_price").parent().parent();
                    if(price=="" || price=="0"){
                        div.addClass('error');
                        bl=false;
                    }else if(parseFloat(price)>parseFloat(that.config.price)){
                        div.addClass('error');
                        msg = "秒杀价格不能大于原价";
                        bl=false;
                    }else{
                        div.removeClass('error'); 
                    } 
                    div.find(".error-message").html(msg);

                    var stock = $(".js_sku_save_stock").val();
                    if(stock=="" || stock =="0"){
                        $(".js_sku_save_stock").parent().parent().addClass('error');
                        bl=false;
                    }else{
                        $(".js_sku_save_stock").parent().parent().removeClass('error');
                    }
                    if(bl){
                        that.close(); 
                        var obj = {status:1,msg:"操作成功",data:that.skuData};
                        obj.show_price = that.skuData[0].seckill_price; 
                        obj.show_stock_num = that.skuData[0].seckill_stock_num; 
                        that.config.done(obj);
                    }
                });
                that.events.push({event:"click",name:".js_sku_save_sig"});  
                //无规则秒杀价格文本框内容发生变化事件
                $("body").on("change",".js_sku_save_price",function(){
                    that.skuData[0].seckill_price = this.value; 
                });
                that.events.push({event:"change",name:".js_sku_save_price"});  
                //无规则秒杀库存文本框内容发生变化事件
                $("body").on("change",".js_sku_save_stock",function(){
                    that.skuData[0].seckill_stock_num = this.value; 
                });
                that.events.push({event:"change",name:".js_sku_save_stock"});  
                //多规格点击保存事件 
                $("body").on("click",".sku-btn-submit",function(){ 
                    var data = that.skuData, 
                        show_price = "", //显示价格 show_price = max==min? min : max-min; 
                        max=0, //最大价格
                        min=0, //最小价格 
                        show_stock_num = 0, //显示库存 所有规格库存累加的结果
                        k=0,  //第几个参与秒杀的sku
                        is_valid = true; //保存是否通过
                        arr = [];  //参与秒杀的 所有 sku 信息
                    for(var i=0;i<data.length;i++){
                        if(data[i].checked){
                            //add by 邓钊 2018-7-12  秒杀库存为空时提示
                            if(!data[i].seckill_stock_num){
                                tipshow('秒杀库存不能为空',"warn");
                                return false
                            }
                            if(!data[i].seckill_price){
                                tipshow('秒杀价格不能为空',"warn");
                                return false
                            }
                            //end
                            var bl = that.validSeckillPrice(k,data[i].seckill_price,data[i].price);
                            if(bl){
                                if(min==0){
                                    min = data[i].seckill_price;
                                    max = data[i].seckill_price;
                                }else{
                                    if(data[i].seckill_price<min)
                                        min = data[i].seckill_price;
                                    if(data[i].seckill_price>max)
                                        max = data[i].seckill_price;
                                } 
                            }else{
                                is_valid = false;
                            }
                            bl = that.validSeckillStock(k,data[i].seckill_stock_num,data[i].stock_num);
                            if(bl){
                                show_stock_num +=parseFloat(data[i].seckill_stock_num);
                            }else{//验证不通过 
                                is_valid = false;
                            }
                            arr.push(data[i]);
                            k++;
                        }
                    }
                    if(!is_valid){ 
                        return false; 
                    }else{
                        var obj = {status:1,msg:"操作成功",data:arr};
                        obj.show_price = max==min? min : min+"~"+max; 
                        obj.show_stock_num = show_stock_num; 
                        that.config.done(obj);
                        that.close();
                    }
                });
                that.events.push({event:"click",name:".sku-btn-submit"});  

                that.isSkuEvent = true;
            }
        }
        /*
        * 是否为多规格 默认false 不是
        */
        hstool.isSku = false;
        /*
        * 获取秒杀价格和库存
        */
        hstool.getSeckillStr = function(){
            var html ='';
            var data = this.skuData;
            if(this.isSku){
                html ='<ul class="hstool-ul clearfix"><li class="hstool-ul-li active">选择商品规格</li><li class="hstool-ul-li ">设置价格和库存</li></ul>';
                html +='<div class="hstool-dialog-sku"><table class="hstool-table sku-table" id="sku-table1"><thead><tr><th class="checkbox"></th><th class="cell-46">规格</th><th class="cell-18">单价（元）</th><th class="cell-18">现有库存</th><th class="cell-18"style="text-align:center;min-width:140px;">操作</th></tr></thead>';
                html +='<tbody>';
                for(var i=0;i<data.length;i++){
                    html +='<tr><td><input type="checkbox" class="js-check-toggle"></td>'; 
                    html +='<td class="sku-comb"><span>'+data[i].k1+'：'+data[i].v1+'</span>';
                    if(data[i].k2){
                        html +='<span>，'+data[i].k2+'：'+data[i].v2+'</span>';
                    }
                    if(data[i].k3){
                        html +='<span>，'+data[i].k3+'：'+data[i].v3+'</span>';
                    }
                    html +='</td>';
                    html +='<td class="sku-meta"><p class="sku-price">'+data[i].price+'</p></td><td><p>'+data[i].stock_num+'</p></td>';
                    html +='<td class="text-center sku-opt"style="text-align:center;">';
                    if(data[i].checked){
                        html +='<div class="none"><a href="javascript:;" data-index="'+i+'" class="btn btn-primary btn-sm js-add-sku">参加秒杀</a></div>';
                        html +='<div><p>已参加</p><a href="javascript:;" data-index="'+i+'" class="btn btn-danger btn-sm js-remove-sku none">取消参加</a></div>'; 
                    }
                    else{
                        html +='<div><a href="javascript:;" data-index="'+i+'" class="btn btn-primary btn-sm js-add-sku">参加秒杀</a></div>'; 
                        html +='<div class="none"><p>已参加</p><a href="javascript:;" data-index="'+i+'" class="btn btn-danger btn-sm js-remove-sku none">取消参加</a></div>'; 
                    }
                    
                    html +='</td></tr>';
                }
                html +='</tbody></table>';
                html += '<table class="hstool-table sku-table none" id="sku-table2"><thead><tr><th class="checkbox"></th><th class="cell-46">规格</th><th class="cell-18">秒杀单价（元）</th><th class="cell-18">秒杀库存</th><th class="cell-18"style="text-align:center;min-width:140px;">操作</th></tr></thead>';
                html +='<tbody><tr><td colspan="5" style="padding:80px 0;text-align:center;font-size:14px;">还没有选择商品规格</td></tr></tbody></table>';
                html +='</div>';
                html +='<div class="hstool-dialog-batch pr"><input type="checkbox" data-index="0" class="js-select-all" name=""/>全选<input type="button"class="ml10 btn btn-default btn-sm js-batch-add"value="批量参加"/><p>未选择的商品规格不参加秒杀活动</p><div class="text-right"style="position: absolute;bottom:-45px;right:10px;"><input type="button" class="btn btn-primary btn-sm sku-btn-next" value="下一步"/></div></div>'; 
                html +='<div class="hstool-dialog-batch none pr"><input type="checkbox" data-index="1" class="js-select-all" name=""/>全选<input type="button"class="ml10  btn btn-default btn-sm js-batch-remove"value="批量取消"/><div class="mt10">批量设置：<span class="js_batch_span"><a href="javascript:;"class="t-bule js_batch_price">秒杀价格</a><a href="javascript:;" class="ml10 t-bule js_batch_stock_num">秒杀库存</a></span>';
                html +='<span class="js_span_sprice none"><input type="text"class="form-control iblock input-sm w100"placeholder="请输入价格"/><a href="javascript:;"class="t-bule js_batch_save_price">保存</a><a href="javascript:;"class="js_batch_cancel_price t-bule ml10">取消</a></span>';
                html +='<span class="js_span_sstock_num none"><input type="text"class="form-control iblock input-sm w100"placeholder="请输入库存"/><a href="javascript:;"class="t-bule js_batch_save_stock_num">保存</a><a href="javascript:;"class="t-bule js_batch_cancel_stock_num ml10">取消</a></span></div>';
                html +='<div class="text-right"style="position: absolute;bottom:10px;right:10px;">秒杀库存为独立库存，请合理操作。<a href="javascript:;"class="t-bule"target="_blank">[查看详情]</a></div><div class="text-right"style="position: absolute;bottom:-45px;right:10px;"><input type="button" class="btn btn-primary btn-sm sku-btn-back" value="上一步"/><input type="button" class="btn btn-primary btn-sm sku-btn-submit ml10" value="保存"/></div></div>';
            }else{//无规格商品
                html  ='<div style="width:550px;color:#666;"><div class="mt20 text-left"><label class="iblock w120 text-right">秒杀价格：</label><span><input type="text" class="form-control valid iblock w200 js_sku_save_price" />价格：'+this.config.price+'</span><p class="error-message" style="margin-top:10px;margin-left:118px;">请输入秒杀价格</p></div>';
                html +='<div class="mt10 text-left"><label  class="iblock w120 text-right">秒杀库存：</label><span><input type="text"  class="form-control valid iblock w200 js_sku_save_stock" />现有库存：'+this.config.stock_num+'</span><p class="error-message" style="margin-top:10px;margin-left:118px;">请输入秒杀库存</p></div>';
                html +='<p class="text-right mt20">秒杀库存为独立库存，请合理操作。<a href="https://www.huisou.cn/home/index/helpDetail/347" class="t-bule" target="_blank">[查看详情]</a></p>';
                html +='<p class="text-right" style="position:absolute;bottom:10px;right:10px;"><input type="button" class="btn btn-primary btn-sm js_sku_save_sig" value="保存" /></p></div>';
            } 
            return html;
        } 
        /*
        * 重新渲染已选中的规格
        */
        hstool.renderSku = function(){
            var data = this.skuData; 
            var html = '';
            if(this.isSku){
                var is_select_sku = 0; 
                for(var i=0;i<data.length;i++){
                    if(data[i].checked == 1){
                        is_select_sku = 1; 
                        break;
                    }
                }
                html +='<thead><tr><th class="checkbox"></th><th class="cell-46">规格</th><th class="cell-18">秒杀单价（元）</th><th class="cell-18">秒杀库存</th><th class="cell-18"style="text-align:center;min-width:140px;">操作</th></tr></thead>';
                if(is_select_sku){
                    html +='<tbody>';
                    for(var i=0;i<data.length;i++){
                        if(data[i].checked){
                            html+='<tr data-index="'+i+'"><td><input type="checkbox" class="js-check-toggle"></td>';
                            html +='<td class="sku-comb"><span>'+data[i].k1+'：'+data[i].v1+'</span>';
                            if(data[i].k2){
                                html +='<span>，'+data[i].k2+'：'+data[i].v2+'</span>';
                            }
                            if(data[i].k3){
                                html +='<span>，'+data[i].k3+'：'+data[i].v3+'</span>';
                            }
                            html +='</td>';
                            html +='<td class="sku-meta"><input type="text" value="'+data[i].seckill_price+'" class="form-control input-sm w80 valid js_input_sprice"/>';
                            html +='<p class="error-message">请输入秒杀价格</p><p class="sku-price">单价（元）：'+data[i].price+'</p></td>';
                            html +='<td><input type="text" value="'+data[i].seckill_stock_num+'" class="form-control input-sm w80 valid js_input_sstock_num"/>';
                            html +='<p class="error-message">请输入秒杀库存</p><p>现有库存：'+data[i].stock_num+'</p></td>';
                            html +='<td class="text-center sku-opt"style="text-align:center;"><p>已参加</p><a href="javascript:;" data-index="'+i+'" class="btn btn-danger btn-sm js-remove-sku none">取消参加</a></td>';
                            html +='</tr>';
                        } 
                    } 
                    html +='</tbody>';
                }else{ 
                    html+='<tbody><tr><td colspan="5" style="padding:80px 0;text-align:center;font-size:14px;">还没有选择商品规格</td></tr></tbody>';
                }
                $(".sku-table").eq(1).html(html);
            }else{
                html  ='<div style="width:550px;color:#666;"><div class="mt20 text-left"><label class="iblock w120 text-right">秒杀价格：</label><span><input type="text" class="form-control valid iblock w200 js_sku_save_price" value="'+data[0].seckill_price+'" />价格：'+data[0].price+'</span><p class="error-message" style="margin-top:10px;margin-left:118px;">请输入秒杀价格</p></div>';
                html +='<div class="mt10 text-left"><label  class="iblock w120 text-right">秒杀库存：</label><span><input type="text"  class="form-control valid iblock w200 js_sku_save_stock" value="'+data[0].seckill_stock_num+'" />现有库存：'+data[0].stock_num+'</span><p class="error-message" style="margin-top:10px;margin-left:118px;">请输入秒杀库存</p></div>';
                html +='<p class="text-right mt20">秒杀库存为独立库存，请合理操作。<a href="https://www.huisou.cn/home/index/helpDetail/347" class="t-bule" target="_blank">[查看详情]</a></p>';
                html +='<p class="text-right" style="position:absolute;bottom:10px;right:10px;"><input type="button" class="btn btn-primary btn-sm js_sku_save_sig" value="保存" /></p></div>';
                $(".hstool-dialog-content").html(html);
            }
            
        }
        /*
        * 获取sku 信息
        */
        hstool.getSkuInfo = function(){ 
            var that = this; 
            if(!that.config.isEditSku){
                var data = {
                    "pid":that.config.pid
                }
                $.ajax({
                    type:'post',
                    url:'/merchants/product/getSku',
                    data:data,
                    async: false,
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': this.config._token
                    },
                    success:function(res){ 
                        if(res.status==1){
                            that.skuData = res.data.stocks || [];
                            if(that.skuData.length>0){
                                for(var i=0;i<that.skuData.length;i++){
                                    that.skuData[i].checked = that.skuData[i].checked || 0;
                                    that.skuData[i].seckill_price = that.skuData[i].seckill_price || "";
                                    that.skuData[i].seckill_stock_num = that.skuData[i].seckill_stock_num || "";
                                }
                                that.isSku = true;
                            }else{
                                var obj ={};
                                obj.checked = 1;
                                obj.seckill_price = "";
                                obj.seckill_stock_num = "";
                                obj.price = that.config.price;
                                obj.stock_num = that.config.stock_num;
                                that.skuData.push(obj);
                                that.isSku = false; 
                            }
                        }
                    },
                    error:function(){
                        console.log("获取商品数据异常");
                    }
                }); 
            }  
        } 
        /*
        * 验证秒杀价格
        */
        hstool.validSeckillPrice = function(k,newp,oldp){
            var is_valid = true,msg=""; 
            if(newp=="0" && oldp != ""){
                is_valid = false;
                msg = "请输入秒杀价格";
            }else if(parseFloat(newp)>parseFloat(oldp) && oldp != ""){
                is_valid = false;
                msg = "秒杀价格不能大于原价";
            }
            if(!is_valid){
                $(".sku-table").eq(1).find(".js_input_sprice").eq(k).parent().addClass('error');
                $(".sku-table").eq(1).find(".js_input_sprice").eq(k).siblings('.error-message').html(msg);
            }else{
                $(".sku-table").eq(1).find(".js_input_sprice").eq(k).parent().removeClass('error'); 
            }
            return is_valid;
        }
        /*
        * 验证秒杀库存
        */
        hstool.validSeckillStock = function(k,newp,oldp){
            var is_valid = true,msg="";
            if(newp=="0" && oldp !=""){
                is_valid = false;
                msg = "请输入秒杀库存";
            }
            // else if(parseFloat(newp)>parseFloat(oldp) && oldp !=""){
            //     is_valid = false;
            //     msg = "秒杀库存不能大于库存";
            // }
            if(!is_valid){
                $(".sku-table").eq(1).find(".js_input_sstock_num").eq(k).parent().addClass('error');
                $(".sku-table").eq(1).find(".js_input_sstock_num").eq(k).siblings('.error-message').html(msg);
            }else{
                $(".sku-table").eq(1).find(".js_input_sstock_num").eq(k).parent().removeClass('error'); 
            }
            return is_valid;
        }
    /*---------------设置秒杀价格和库存结束----------------*/  
        return hstool;
    })();
    win.hstool = hstool;
})(window);


//获取二维码 html css 参考 merchants/product/index 466行 推广商品
function getEwm(){
    var price ;//记录价格
    // 产品推广商品点击
    $('.ads').click(function(e){
        // alert($(this).offset().top);
        var product_id = $(this).data('id');
        var that = $(this);
        price = $(this).data('price');

        //商品详情页地址 Herry
        var url = that.data('url');

        //设置产品id的值
        $('input[name="product_id"]').val(product_id);
        //$.get('/merchants/product/getqrdiscount',{product_id:product_id},function(data){
        //获取二维码
        $.get('/merchants/product/getQRCode',{id:product_id},function(data){
            console.log(data);
            if(data.status=1){
                //有数据首次新增
                /*if(data.data.length>0){
                    if(data.data[0].discount_way ==1){
                      $('#scan_discount').prop('checked','checked');
                    }else if(data.data[0].discount_way ==2){
                      $('#scan_min').prop('checked','checked');
                    }
                    //设置产品价格
                    $('.js-final-price').html(price*data.data[0].discount_num);
                    //设置折扣价格
                    $($('input[name="discount_num"]').get(1)).val(data.data[0].discount_num);
                    $('input[name="discount_num"]').val(data.data[0].discount_num);
                    $('#scan_id').val(data.data[0].id);
                }else{
                  $('.js-final-price').html(price);
                }*/
                $('.widget-promotion').show();

                $('.widget-promotion .product_detail').val(url);
                $('.widget-promotion .qr_img').html(data.data);
                $('.widget-promotion .down_qrcode').attr('data-id',product_id);

                $('.widget-promotion').css('top',that.offset().top-65);
                $('.widget-promotion').css('left',that.offset().left-380);
            }else{
                tipshow('没有找到此商品！','warn');
            }
            //居中弹窗
            $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        },'json')
        $.get('/merchants/product/getLiteAppQRCode',{id:product_id},function(data){
            console.log(data);
            if(data.status=1){
                var dataEwm = data.data
                //有数据首次新增
                /*if(data.data.length>0){
                    if(data.data[0].discount_way ==1){
                      $('#scan_discount').prop('checked','checked');
                    }else if(data.data[0].discount_way ==2){
                      $('#scan_min').prop('checked','checked');
                    }
                    //设置产品价格
                    $('.js-final-price').html(price*data.data[0].discount_num);
                    //设置折扣价格
                    $($('input[name="discount_num"]').get(1)).val(data.data[0].discount_num);
                    $('input[name="discount_num"]').val(data.data[0].discount_num);
                    $('#scan_id').val(data.data[0].id);
                }else{
                  $('.js-final-price').html(price);
                }*/
                if(dataEwm.errCode == 0){
                    $(".ewm_li").show().parent('ul').children('li').css('width','33.33%')
                }
                var html = '<img src="data:image/png;base64,'+dataEwm.data+'"/>'
                $('.widget-promotion .ewm_img').html(html);
                $('.widget-promotion .ema_qrcode').attr('data-id',product_id);

            }else{
                tipshow('没有找到此商品！','warn');
            }
        },'json')
        e.stopPropagation();
    })
    // 推广商品
    $('.widget-promotion-tab li').click(function(e){
        $('.widget-promotion-tab li').removeClass('active');
        $(this).addClass('active');
        if($(this).data('tab')=='qrcode'){
            $('.js-tab-content-qrcode').show();
            $('.js-tab-content-link').hide();
            $('.js-tab-content-ewm').hide();
        }else if($(this).data('tab')=='ewm'){
            $('.js-tab-content-qrcode').hide();
            $('.js-tab-content-link').hide();
            $('.js-tab-content-ewm').show();
        }else{
            $('.js-tab-content-qrcode').hide();
            $('.js-tab-content-link').show();
            $('.js-tab-content-ewm').hide();
        }
        e.stopPropagation();
    })

    $('.widget-promotion').click(function(e){
        e.stopPropagation();
    })
}

