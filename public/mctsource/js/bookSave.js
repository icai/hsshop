$(function(){
	/*上传图片*/
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
    
    /*图文封面上传图片*/
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
               $('.bs_fengmianlook').children("input").val(resultSrc[0]);
                $('.bs_fengmianlook').children("img").attr("src",_host+resultSrc[0]);
            } 
        }
    });
      /*预约顶部图片上传图片*/
	 $(".setImgf").click(function(){
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
               $('.bs_fengmianlookf').children("input").val(resultSrc[0]);
                $('.bs_fengmianlookf').children("img").attr("src",_host+resultSrc[0]);
            } 
        }
    });
   
    
     //bs_numtime 开始时间
    $('#datetimepicker1').datetimepicker({
		minDate: new Date(), //时间小于当前时间时会自动清空以有的数据
        format: 'YYYY-MM-DD',
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
    
    //bs_numtime 结束时间
    $('#datetimepicker2').datetimepicker({
		minDate: new Date(), //时间小于当前时间时会自动清空以有的数据
        format: 'YYYY-MM-DD',
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
    if(end_at){
        $("#datetimepicker2").val(end_at);
    }
    //datetimepicker1 的时间一定小于 datetimepicker2 的时间；
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    });
    
    //bs_content 开始时间
    $('#datetimepicker3').datetimepicker({
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
    if(bs_contentstart_at){
        $("#datetimepicker3").val(bs_contentstart_at);
    }
    
    //bs_content 结束时间
    $('#datetimepicker4').datetimepicker({
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
    if(bs_contentend_at){
        $("#datetimepicker4").val(bs_contentend_at);
    }
   /*预约详情 富文本编辑器*/ 
  var ue = UE.getEditor("details",{
        toolbars: [
               [
                'bold', //加粗
                'justifyleft', //居左对齐
                'justifyright', //居右对齐
                'justifycenter', //居中对齐
                'justifyjustify', //两端对齐 
            ]
        ],
        initialFrameHeight:180,//设置编辑器高度
        autoFloatEnabled:false,
        autoHeightEnabled: false
    });
    //富文本ue change事件
    ue.addListener("ready", function () { 
        var content = details ? details : "";
        ue.setContent(content)
        ue.addListener( "selectionchange", function () {
            var _html = ue.getContent();
            if(!ue.getContent()){
                _html = "";
            }
           $(".details").val(_html);
        });
    });   
    /*预约接受数量*/
   $('.time').on('click',function(){  	
   	$('.time').addClass('bs_numsall').siblings('div').removeClass('bs_numsall')
   	$('.time1').addClass('bs_numsalldiv').parent().siblings('div').children('div').removeClass('bs_numsalldiv')
   	$('.time2').addClass('bs_numsallp').parent().siblings('div').children('p').removeClass('bs_numsallp');  	$('.time3').addClass('bs_numsallspan').parent().parent().siblings('div').children('p').children('span').removeClass('bs_numsallspan')  	
   	$('.timer').css('display','block').siblings('div').css('display','none')   	
   	$('.bs_numtime').css('display','block').siblings('div').css('display','none')
   })  
   $('.liang').on('click',function(){ 
   	$('.liang').addClass('bs_numsall').siblings('div').removeClass('bs_numsall')
   	$('.liang1').addClass('bs_numsalldiv').parent().siblings('div').children('div').removeClass('bs_numsalldiv')
   	$('.liang2').addClass('bs_numsallp').parent().siblings('div').children('p').removeClass('bs_numsallp');  	$('.liang3').addClass('bs_numsallspan').parent().parent().siblings('div').children('p').children('span').removeClass('bs_numsallspan')
   	$('.liangr').css('display','block').siblings('div').css('display','none')
   	$('.bs_numDailyamount').css('display','block').siblings('div').css('display','none')
   }) 
   $('.zongLiang').on('click',function(){ 
   	$('.zongLiang').addClass('bs_numsall').siblings('div').removeClass('bs_numsall')
   	$('.zongLiang1').addClass('bs_numsalldiv').parent().siblings('div').children('div').removeClass('bs_numsalldiv')
   	$('.zongLiang2').addClass('bs_numsallp').parent().siblings('div').children('p').removeClass('bs_numsallp'); 	$('.zongLiang3').addClass('bs_numsallspan').parent().parent().siblings('div').children('p').children('span').removeClass('bs_numsallspan') 	
   	$('.zongLiangr').css('display','block').siblings('div').css('display','none')   	
   	$('.bs_numTotalamount').css('display','block').siblings('div').css('display','none')
   })
    
    
    /*提交*/
    $('.book_btn').on('click',function(){
    	var data={};
    	var title=$('.title').val()
    	var keywords=$('.keywords').val()
    	var cover_img=$('#cover_img').attr('src');
    	var address=$('.address').val()
    	var phone=$('.phone').val()
    	var details=$('.details').val()
    	var banner_img=$('#banner_img').attr('src');
    	var limit_type;
       
    	if($('.time').hasClass('bs_numsall')){
	    		limit_type=0
	    }
    	if($('.liang').hasClass('bs_numsall')){
    		limit_type=1
	    }
	    if($('.zongLiang').hasClass('bs_numsall')){
	    		limit_type=2
	    }
    	var start_time=$('#datetimepicker1').val()
    	var end_time=$('#datetimepicker2').val()
    	var limit_num=$('.limit_num').val()
    	var limit_total=$('.limit_total').val();
    	var content={}
    	var i=0
    	if($('.finput').is(':checked')){
    		i++
	    	content[i]={}
	    	var ikey=$('.name').val()
	    	var iclass=$('.name').attr('class')
	    	var itype=$('.name').attr('type')
	    	var ival=$('.name_after').val() ? $('.name_after').val() : '请输入您的姓名';
	    	content[i].ikey=ikey
	    	content[i].ival=ival
	    	content[i].iclass = iclass
            content[i].shopClass = 'finput';
	    	content[i].itype=itype
    	}
    	if($('.binput').is(':checked')){
    		i++
	    	content[i]={}
	    	var ikey=$('.phones').val()
	    	var iclass=$('.phones').attr('class')
	    	var itype=$('.phones').attr('type')
	    	var ival=$('.phones_after').val() ? $('.phones_after').val() : '请输入您的电话';
	    	content[i].ikey=ikey
	    	content[i].ival=ival
	    	content[i].iclass = iclass
            content[i].shopClass = 'binput';
	    	content[i].itype=itype
    	}
    	if($('.sinput').is(':checked')){
    		i++
    		content[i]={}
	    	var ikey=$('.book_date').val()
	    	var iclass=$('.book_date').attr('class')
	    	var itype=$('.book_date').attr('type')
	    	var ival=$('#start_times').val() ? $('#start_times').val() : '请输入预约日期';
	    	content[i].ikey=ikey
	    	content[i].ival=ival
	    	content[i].iclass = iclass
            content[i].shopClass = 'sinput';
	    	content[i].itype=itype
    	}
    	if($('.input4').is(':checked')){
    		i++
    		content[i]={}
	    	var ikey=$('.book_time').val()
	    	var iclass=$('.book_time').attr('class')
	    	var itype=$('.book_time').attr('type')
	    	var ival=$('#start_time').val() ? $('#start_time').val() : '请输入预约时间';
	    	var ival=ival
	    	content[i].ikey=ikey
	    	content[i].ival=ival
	    	content[i].iclass = iclass
            content[i].shopClass = 'input4';
	    	content[i].itype=itype
    	}
    	
    	/*输入要增加的内容 */
    	var length=$(".box").length
    	var other={}
    	for(var i=0;i<length;i++){
    		other[i]={}
    		var ikey=$('.box').eq(i).find('input').eq(0).val()
    		var itype=$('.box').eq(i).find('input').eq(0).attr('type')
    		var ival=$('.box').eq(i).find('input').eq(1).val()
    		other[i].ikey=ikey
    		other[i].ival=ival
	    	other[i].itype=itype
    	}
    	
    	/*下拉框 */
    	var dropBoxLength=$(".dropbox").length
    	/*console.log(length)*/
    	var dropBoxother={}
    	for(var i=0;i<dropBoxLength;i++){
    		dropBoxother[i]={}
    		var ikey=$('.dropbox').eq(i).find('input').eq(0).val()
    		var itype='select'
    		var ival=$('.dropbox').eq(i).find('input').eq(1).val()
    		dropBoxother[i].ikey=ikey
    		dropBoxother[i].ival=ival
	    	dropBoxother[i].itype=itype
    	}
    	data.dropBoxother=dropBoxother
    	data.other=other
    	data.title=title
    	data.keywords=keywords
    	data.cover_img=cover_img
    	data.address=address
    	data.phone=phone
    	data.details=details
    	data.banner_img=banner_img
    	data.limit_type=limit_type
    	data.start_time=start_time
    	data.end_time=end_time
    	data.limit_num=limit_num
    	data.limit_total=limit_total
    	data.content=content

    	$.ajax({
			headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
			type:"post",
			url:"",
			async:true,
			data:data,
			success:function(response){
				if(response.status == 1 ){
                    tipshow(response.info);
                    setTimeout(function(){
                    	window.location.href='/merchants/wechat/book';
                    },1000);
               } else {
                   tipshow(response.info,'warn')
                }
			}
		});	
    })
    /*输入要增加的内容 添加删除*/
    $('.caseBox').on('click','.add',function(){
    	var ul='';
    	ul+='<ul class="box">'
		ul+='<li>输入要增加的内容：</li>'
		ul+='<li><input class="box_add1" type="text"/></li>'
		ul+='<li><input class="box_add2" type="text"></li>'
		ul+='<li><span class="delete">删除</span></li>'
		ul+='</ul>'
		$('.caseBox').append(ul)
    })
    $('.caseBox').on('click','.delete',function(){
    	$(this).parent().parent().remove()
    })
    /*下拉框 添加删除*/
    $('.caseDropBox').on('click','.addbox',function(){
    	var ul='';
    	ul+='<ul class="dropbox">'
		ul+='<li>下拉框：</li>'
		ul+='<li><input class="dropbox_addf" type="text"/></li>'
		ul+='<li><input class="dropbox_adds" type="text" placeholder="每个选项之间以“，”分割"/></li>'
		ul+='<li><span class="deletebox">删除</span></li>'
		ul+='</ul>'
		$('.caseDropBox').append(ul)
    })
    $('.caseDropBox').on('click','.deletebox',function(){
    	$(this).parent().parent().remove()
    })
    
})
function isNumber(value) {         //验证是否为数字
    var patrn = /^(-)?\d+(\.\d+)?$/;
    if (patrn.exec(value) == null || value == "") {
        return false
    } else {
        return true
    }
}
