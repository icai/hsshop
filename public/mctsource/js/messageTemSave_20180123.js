$(function(){
	var ListDataType = 0;
	// 预约时间
    $('.datetimepicker').datetimepicker({
        minDate: new Date(), //时间小于当前时间时会自动清空以有的数据
     	format: 'YYYY-MM-DD HH:mm:ss',
        // format: 'MM-DD',
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
    
    var Request = new Object();
	Request = GetRequest();
	//点击查看进入的时候控制的不可输入更改情况
	if(Request.ro == 1){
		$(".editDiv input, .editDiv textarea").prop("disabled", true);
		$("#remark").prop("contenteditable", false)
		$(".emoji").hide()
		$(".choosLink").removeClass("choose_link");
		$(".submitFunDiv").hide()
	}
	//页面类型判断
	if(Request.type==0){
		$(".type_1").show();
		$(".type_2").hide();
		$(".type_3").hide();
		$(".type_4").hide();
		$(".type_5").hide();
		$(".type_6").hide();
	}else if(Request.type==1){
		$(".type_1").hide();
		$(".type_2").show();
		$(".type_3").hide();
        $(".type_4").hide();
        $(".type_5").hide();
        $(".type_6").hide();
	}else if(Request.type==2) {
		$(".type_1").hide();
		$(".type_2").hide();
		$(".type_3").show();
        $(".type_4").hide();
        $(".type_5").hide();
        $(".type_6").hide();
	}else if(Request.type==3) {
        $(".type_1").hide();
        $(".type_2").hide();
        $(".type_3").hide();
        $(".type_4").show();
        $(".type_5").hide();
        $(".type_6").hide();
    }else if(Request.type==4) {
        $(".type_1").hide();
        $(".type_2").hide();
        $(".type_3").hide();
        $(".type_4").hide();
        $(".type_5").show();
        $(".type_6").hide();
    }else if(Request.type==5) {
        $(".type_1").hide();
        $(".type_2").hide();
        $(".type_3").hide();
        $(".type_4").hide();
        $(".type_5").hide();
        $(".type_6").show();
    }
    

	//点击关闭自定义框
	$("body").click(function(){
		$(".linkBox").hide()
		$(".emojiBox").hide()
	})
	$(".choose_link").click(function(e){
		e.stopPropagation();
		$(".linkBox").show()
	})
	$(".linkBox li").each(function(index, ele){
		$(this).click(function(){
			var id = $(this)["0"].dataset.id;
			if(id==1){
				ListDataType = 5;
				chooseLink(5, false, true);
			}else if(id==2){
				ListDataType = 3;
				chooseLink(3, true, true);
			}else if(id==3){
				ListDataType = 18;
				chooseLink(18, true, true);
				$(".list_active").text("拼团商品")
			}
		})
	})
	//搜索
	$("#myModal1 .search button").click(function(){
		var val = $("#myModal1 .search input").val();
		chooseLink(ListDataType, true, true, val);
	});
    //预约内容
    $("#templateContent").on('input', function(){
		$(".edit_content .ordered_content").text($(this).val())
    })
    //预约时间

    $('.datetimepicker').on('dp.change', function(ev){
        var value = ev.date.valueOf();
        $(".timeShow .pcontent").text(formatDateTime(value))
		$(".date_time").text(formatDateTime(value))

	});
	//商品名称
    $("#goodsName").on('input', function(){
		$(".goodsNameShow .goodsName").text($(this).val())
    })
    //商品现价
    $("#goodsPrice").on('input', function(){
		$(".goodsPriceShow .goodsPrice").text($(this).val())
    })
    //商品现价
    $("#goodsOprice").on('input', function(){
		$(".goodsOpriceShow .goodsOprce").text($(this).val())
    })
    //提醒
    $("#tixingContent").on('input', function(){
		$(".tixingShow .tixing").text($(this).val())
    })
	//备注内容
    $("#remark").on('input', function(e){
    	var remarkVal = e.delegateTarget.innerHTML;
    	var remarkLen = remarkVal.replace(/<img[^>]+>/g, '');
    	if(remarkLen.length < 40){
    		$(".remarkShow .pcontent").html(remarkVal)
			$(".remark").html(remarkVal)
    	}else{
    		tipshow("您输入的字数过多",'warning');
    	}
    })
    //备注内容
    $("#card_volume").on('input', function(e){
    	var inpVal = $(this).val()
		$(".card_volume").html(inpVal)
        // var remarkVal = e.delegateTarget.innerHTML;
        // var remarkLen = remarkVal.replace(/<img[^>]+>/g, '');
        // if(remarkLen.length < 40){
        //     $(".remarkShow .pcontent").html(remarkVal)
        // }else{
        //     tipshow("您输入的字数过多",'warning');
        // }
    })
    $("#time_limit").on('input', function(e){
        var inpVal = $(this).val()
        $(".time_limit").html(inpVal)
        // var remarkVal = e.delegateTarget.innerHTML;
        // var remarkLen = remarkVal.replace(/<img[^>]+>/g, '');
        // if(remarkLen.length < 40){
        //     $(".remarkShow .pcontent").html(remarkVal)
        // }else{
        //     tipshow("您输入的字数过多",'warning');
        // }
    })
    $("#shopName").on('input', function(e){
        var inpVal = $(this).val()
        $(".shopName").html(inpVal)
        // var remarkVal = e.delegateTarget.innerHTML;
        // var remarkLen = remarkVal.replace(/<img[^>]+>/g, '');
        // if(remarkLen.length < 40){
        //     $(".remarkShow .pcontent").html(remarkVal)
        // }else{
        //     tipshow("您输入的字数过多",'warning');
        // }
    })
    $("#shopName").on('input', function(e){
        var inpVal = $(this).val()
        $(".shopName").html(inpVal)
        // var remarkVal = e.delegateTarget.innerHTML;
        // var remarkLen = remarkVal.replace(/<img[^>]+>/g, '');
        // if(remarkLen.length < 40){
        //     $(".remarkShow .pcontent").html(remarkVal)
        // }else{
        //     tipshow("您输入的字数过多",'warning');
        // }
    })
    $("#presell_price").on('input', function(e){
        var inpVal = $(this).val()
		var index = inpVal.indexOf('.')
        if(index != -1 && index + 2 < inpVal.length) {
            tipshow("小数只保留小数点后两位", 'warning');
        }
        inpVal = parseInt(inpVal * 100) / 100
        $(".presell_price").html("￥" + inpVal)
    })
    $("#service").on('input', function(e){
        var inpVal = $(this).val()
        $(".service").html(inpVal)
        // var remarkVal = e.delegateTarget.innerHTML;
        // var remarkLen = remarkVal.replace(/<img[^>]+>/g, '');
        // if(remarkLen.length < 40){
        //     $(".remarkShow .pcontent").html(remarkVal)
        // }else{
        //     tipshow("您输入的字数过多",'warning');
        // }
    })
    $("#serviceName").on('input', function(e){
        var inpVal = $(this).val()
        $(".serviceName").html(inpVal)
        // var remarkVal = e.delegateTarget.innerHTML;
        // var remarkLen = remarkVal.replace(/<img[^>]+>/g, '');
        // if(remarkLen.length < 40){
        //     $(".remarkShow .pcontent").html(remarkVal)
        // }else{
        //     tipshow("您输入的字数过多",'warning');
        // }
    })
    //选择页面连接
    $(document).on("click",".chooseGoodsUrl",function(){
    	var itemData = $(this)["0"].dataset;
    	$(".choosLink").text(itemData.title)
		$("#linkType").val(itemData.url);
		$("#linktitle").val(itemData.title);
		$('#myModal1').modal('hide');
    })
    
    //表情
    $(".emoji").click(function(e){
    	e.stopPropagation();
    	$(".emojiBox").toggle()
    	if($(".emojiBox").html()==''){
    		for(var i=0; i<75; i++){
//  			var emojiItem = `<img class="emojiChoose" src="`+imgUrl+`mctsource/images/arclist/`+(i+1)+`.gif" height="20px" width="20px" style="display:inline-block"/>`;
    			var emojiItem = "<img class=\"emojiChoose\" src=\"" + imgUrl + "mctsource/images/arclist/" + (i+1) + ".gif\" height=\"20px\" width=\"20px\" style=\"display:inline-block;\"/>";;
    			$(".emojiBox").append(emojiItem)
    		}
    	}
    })
    
    //点击表情
    $(document).on("click", ".emojiBox .emojiChoose", function(){
    	var pos = $("#remark").selectionEnd;
    	var emojiImg = $(this)["0"].outerHTML
    	var remarkHtml = $("#remark").html()
    	$("#remark").html(remarkHtml+emojiImg);
    	
    	$(".remarkShow .pcontent").html(remarkHtml+emojiImg);
    })
    
    //提交
    $(".dataForm").submit(function(e){
    	e.preventDefault();
    	
    	var goodsPrice = $("#goodsPrice").val();
    	var goodsOprice = $("#goodsOprice").val();
    	if(isNaN(goodsPrice) || isNaN(goodsOprice)){
    		tipshow("价格输入错误！",'warning');
    		return false;
    	}
    	
    	var data = $('form').serializeArray();
    	var remark = $("#remark").html();
        data.push({name:'id', value: Request.id})
    	data.push({name:'remark', value: remark})
    	data.push({name:'type'  , value: Request.type})
    	$.ajax({
    		type:"post",
    		url:"/merchants/message/save",
    		data: data,
    		async:true,
    		success: function(res){
    			if(res.status==1){
    				tipshow(res.info,'info');
    				setTimeout(function(){
    					location.href = "/merchants/message/index"
    				}, 1000)
    			}else{
    				tipshow(res.info,'warning');
    			}
    		}
    	});
    });
})

function GetRequest() {
 	var url = location.search; //获取url中"?"符后的字串
 	var theRequest = new Object();
	if (url.indexOf("?") != -1) {
  		var str = url.substr(1);
  		strs = str.split("&");
  		for(var i = 0; i < strs.length; i ++) {
   			theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
  		}
 	}
 	return theRequest;
}

//选择连接分类
//* type: 获取数据的时候使用的type值；
//* openModal: 是否打开弹框；
//* loadPageDiv: 是否重新加载分页；
//* title: 搜索的时候的传值；
//* page: 页码分页值；
function chooseLink(type, openModal, loadPageDiv, title, page){
	var title = arguments[3]?arguments[3]:'';
    var page = arguments[4]?arguments[4]:1;
	$.get('/merchants/linkTo/get',{
		type : type,           	//参数类型
       	wid  : $('#wid').val(),	//页面标志
       	title: title, 			//搜索内容
       	page : page,
	},function(res){
        if(res.status == 1){
        	if(openModal){
        		$("tbody.small").html("")
        		$('#myModal1').modal();
        		var datas = res.data["0"].data;
        		for(var i=0; i<datas.length; i++){
        			var _html = "<tr>";
	                    _html +="    <td>";
	                    _html +="    	<a class=\"co_38f\" href=\""+datas[i].url+"\" target=\"_blank\">"+datas[i].page_title+"</a>";
	                    _html +="    </td>";
	                    _html +="    <td>"+datas[i].created_at+"</td>";
	                    _html +="    <td>";
	                    _html +="    	<button class=\"btn btn-default chooseGoodsUrl\" data-url=\""+datas[i].url+"\" data-title=\""+datas[i].page_title+"\">选取</button>";
	                    _html +="    </td>";
	                    _html +="</tr>";
	                $("tbody.small").append(_html)
        		}
        		
        		if(loadPageDiv){
	        		$('.myModal1Page').extendPagination({
						totalCount: res.data[0].total,	//数据总数
				        showCount : res.data[0].last_page,	//展示页数
				        limit     : res.data[0].per_page,		//每页展示条数
				        callback  : function (curr, limit, totalCount) {
				        	
				        	chooseLink(type, openModal, false, title, curr)
				        }
				    });
        		}
 		
        	}else{
        		if(!res.data.id){//没有进行设置
        		    tipshow('请设置后再进行','warning');
        		    return false;
        		}
        		$(".choosLink").text(res.data.page_title)
        		$("#linkType").val(res.data.url);
        		$("#linktitle").val(res.data.page_title);
        	}
        }
   	})
}

function num(obj){
	obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
	obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
	obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
	obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
}

//form表单json化
$.fn.serializeObject = function() {  
    var o = {};  
    var a = this.serializeArray();  
    $.each(a, function() {  
        if (o[this.name]) {  
            if (!o[this.name].push) {  
                o[this.name] = [ o[this.name] ];  
            }  
            o[this.name].push(this.value || '');  
        } else {  
            o[this.name] = this.value || '';  
        }  
    });  
    return o;  
}  

//插入文本
function insertText(inputEle, inTxt){
	let obj = inputEle;
	let str = inTxt;
	var newHtml = inputEle.html();
	if(document.selection) {
		obj.focus();
		let sel = document.selection.createRange();
		sel.text = str;
	} else if(typeof obj.selectionStart === 'number' && typeof obj.selectionEnd === 'number') {
		let startPos = obj.selectionStart;
		let endPos = obj.selectionEnd;
		let tmpStr = newHtml;
		newHtml = tmpStr.substring(0, startPos) + str + tmpStr.substring(endPos, tmpStr.length);
	} else {
		newHtml += str;
	}
	return newHtml;
}



function formatDateTime(inputTime) {
    var date = new Date(inputTime);
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    m = m < 10 ? ('0' + m) : m;
    var d = date.getDate();
    d = d < 10 ? ('0' + d) : d;
    var h = date.getHours();
    h = h < 10 ? ('0' + h) : h;
    var minute = date.getMinutes();
    var second = date.getSeconds();
    minute = minute < 10 ? ('0' + minute) : minute;
    second = second < 10 ? ('0' + second) : second;
    return y + '-' + m + '-' + d + ' ' + h + ':' + minute + ':' + second;
}