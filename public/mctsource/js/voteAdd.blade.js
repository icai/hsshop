$(function(){
	
	var ue = UE.getEditor("prize_set",{
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
                'fullscreen'
            ]
        ],
        initialFrameHeight:180,//设置编辑器高度
        autoFloatEnabled:false,
        autoHeightEnabled: false
    });
    //富文本ue change事件
    ue.addListener("ready", function () { 
        var content = prize_set ? prize_set : "";
        ue.setContent(content)
        ue.addListener( "selectionchange", function () {
            var _html = ue.getContent();
            if(!ue.getContent()){
                _html = "";
            }
           $("#prize_set").val(_html);
           $("#prize_set1").val(_html);
        });
    }); 
    var ue1 = UE.getEditor("canvass_info",{
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
                'fullscreen'
            ]
        ],
        initialFrameHeight:180,//设置编辑器高度
        autoFloatEnabled:false,
        autoHeightEnabled: false
    })
      //富文本ue1 change事件
    ue1.addListener("ready", function () { 
        var content1 = canvass_info ? canvass_info : "";
        ue1.setContent(content1)
        ue1.addListener( "selectionchange", function () {
            var _html = ue1.getContent();
            if(!ue1.getContent()){
                _html = "";
            }
           $("#canvass_info").val(_html);
           $("#canvass_info1").val(_html);
        });
    });
   	var ue2 = UE.getEditor("act_rule",{
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
                'fullscreen'
            ]
        ],
        initialFrameHeight:180,//设置编辑器高度
        autoFloatEnabled:false,
        autoHeightEnabled: false
    })
   	  //富文本ue2 change事件
    ue2.addListener("ready", function () { 
        var content2 = act_rule ? act_rule : "";
        ue2.setContent(content2)
        ue2.addListener( "selectionchange", function () {
            var _html = ue2.getContent();
            if(!ue2.getContent()){
                _html = "";
            }
           $("#act_rule").val(_html);
           $("#act_rule1").val(_html);
        });
    });
    if($("input[name='id']").val()){
   		var start_at = $('#datetimepicker1').val();
   	 	var end_at = $('#datetimepicker2').val();
    }
   // 开始时间
    $('#datetimepicker1,#datetimepicker2').datetimepicker({
		minDate: new Date(), //时间小于当前时间时会自动清空以有的数据
        format: 'YYYY-MM-DD HH:mm:ss',
        showClear: true,
        showClose: true,
        showTodayButton: true,
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
    if(start_at){
        $("#datetimepicker1").val(start_at);
    }
    if(end_at){
        $("#datetimepicker2").val(end_at);
    }
    //datetimepicker1 的时间一定小于 datetimepicker2 的时间；
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
        $(".start_time").removeClass("hide");
        $(".start_time .start_time_s").text($("#datetimepicker1").val());
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        $(".end_time").removeClass("hide");
        $(".end_time .end_time_s").text($("#datetimepicker2").val());
    });
    
   
    //活动圈
       $(".setImg").click(function(){
        obj = $(this);
        layer.open({
            type: 2,
            title:false,
            closeBtn:false, 
            // skin:"layer-tskin", //自定义layer皮肤 
            move: false, //不允许拖动 
            area: ['860px', '660px'], //宽高
            content: '/merchants/order/clearOrder/1'
        }); 
        /**
         * 图片选择后的回调函数
         */
        selImgCallBack = function(resultSrc){ 
            if(resultSrc.length>0){
                $("input[name='share_img']").val(resultSrc[0]);
                $(".share_img").attr("src",_host+resultSrc[0]).parent().removeClass('hide');  
                obj.children("input").val(resultSrc[0]);
                obj.children("img").attr("src",_host+resultSrc[0]);
            } 
        }
    });
    
    //提交预览
    $('.tj_1').click(function(){
    	var data = {};
        var id           = $("input[name='id']").val();
        var many_people  = $("#many_people").val();
        var many_ticket  = $("#many_ticket").val();
        var act_title    = $('#act_title').val();
        var start_time   = $('#datetimepicker1').val();
        var end_time     = $('#datetimepicker2').val();
        var act_img      = $('#act_img').attr('src');
        var vote_rule    = $('#vote_rule').val();
        var prize_set    = $('#prize_set1').val();
        var canvass_info = $('#canvass_info1').val();
        var act_rule     = $('#act_rule1').val();
        var keyword      = $('#num_keyword').val();
		
        data.id           = id
        data.many_people  = many_people
        data.many_ticket  = many_ticket
        data.act_title    = act_title
        data.start_time   = start_time
        data.end_time     = end_time
        data.act_img      = act_img.replace(_host,'');
        data.vote_rule    = vote_rule
        data.prize_set    = prize_set
        data.canvass_info = canvass_info
        data.act_rule     = act_rule
        data.keyword      = keyword
     
        if(act_title == ''){
            tipshow('填写活动标题','warn');
            return;
        }
        if(start_time == ''){
            tipshow('选择活动开始时间','warn');
            return;
        }
        if(end_time == ''){
            tipshow('选择活动结束时间','warn');
            return;
        }
        if(act_img == ""){
            tipshow('选择图片','warn');
            return;
        }
        if(vote_rule == ""){
            tipshow('填写投票规则','warn');
            return;
        }						
		$.ajax({
			headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
			type:"post",
			url:"/merchants/marketing/vote/save",
			async:true,
			data:data,
			success:function(response){
				if(response.status == 1 ){
                    tipshow(response.info);
                    setTimeout(function(){
                    	window.location.href='/merchants/marketing/vote';
                    },1000);
               } else {
                   tipshow(response.info,'warn')
                }
			}
		});
    });
  
});
