$(function(){
	//上传预览图片；
	$(".filepath").on("change",function() {
        var srcs = getObjectURL(this.files[0]);   //获取路径
        $(this).siblings("a").hide();   //this指的是input
        $(this).nextAll(".img2").show();  //fireBUg查看第二次换图片不起做用
        $(this).siblings('.close').show();   //this指的是input
        $(this).nextAll(".img2").attr("src",srcs);    //this指的是input
        $(this).val('');    //必须制空
        $(".close").on("click",function() {
            $(this).hide();     //this指的是span
            $(this).nextAll(".img2").hide();
            $(this).siblings("a").show();
            //手动重新验证
			Revalidate('store_img'); 
        })
   });
    function getObjectURL(file) {
        var url = null;
        if (window.createObjectURL != undefined) {
            url = window.createObjectURL(file)
        } else if (window.URL != undefined) {
            url = window.URL.createObjectURL(file)
        } else if (window.webkitURL != undefined) {
            url = window.webkitURL.createObjectURL(file)
        }
        return url
    };
    
    //上传图片的src改变所触发的事件；
	$(".imgnum img").load(function(){
		var imgSrc = $(this).attr("src");
		$("#store_img").val(imgSrc);
		//手动重新验证
		Revalidate('store_img'); 
	});
	$(".close").click(function(){
		$("#store_img").val("");
	})
    
    //------------------查看实例-----------------
    //蒙板设置
    $(".board").css({width: $(window).width()+"px",
					height: $(window).height()+"px",})
    
    //点击按钮显示弹出层；
    $("#see").click(function(){
    	$(".board, .layer").removeClass("hide");
    	//关闭弹出层；；
    	$(".layer .top_right, .layer_bottom button").click(function(){
    		$(".board, .layer").addClass("hide");
    	})
    })

	
	
	
	
	//关闭、开启定时送达
	$("#Sen").click(function(){
		$(this).prop("checked") ? $(".DsSend").removeClass("hide") : $(".DsSend").addClass("hide");
	})
	
	//点击星期div的样式显示：
	$(".week").each(function(index, ele){
		$(this).click(function(){
			$(this).hasClass("weekSelect") ? $(this).removeClass("weekSelect") : $(this).addClass("weekSelect");
		})
	})
	//星期蒙板点击事件：
	$(".weekBoard").click(function(event){
		event.stopPropagation();    //  阻止事件冒泡
	});

	//点击时间段确定按钮的事件；
	var storeTimes = [];
	$("#beSure").click(function(){
		//选择开始、结束时间的判断；
		var beginH = $("select[name='beginHour']").val();
		var beginM = $("select[name='beginMinut']").val();
		var endH = $("select[name='endHour']").val();
		var endM = $("select[name='endMinut']").val();
		if (beginH < endH) {
			pass(beginH, beginM, endH, endM);
		}else if(beginH == endH && beginM < endM){
			pass(beginH, beginM, endH, endM);
		}else{
			tipshow('关门时间要大于开门时间','warn');
		}
		storeTimes = [];
		$(".timeDivShow p").each(function(index,ele){
			var TM = $(this).children("t").text();
				TM += $(this).children("n").text();
			storeTimes.push(TM);
		})
		$("#store_time").val(storeTimes);
		//手动重新验证
		Revalidate('store_time'); 
	})
	
	//时间段选择验证通过事件
	function pass(bh,bm,eh,em){
		var weekName = "";
		if ($(".week").hasClass("weekSelect") == true) {
//			pass(beginH, beginM, endH, endM);
			$(".weekDiv .weekSelect").each(function(index, ele){
				weekName += $(this).text()+" ";
			})
			if ($(".week").hasClass("weekSelect") == true) {
				var time = "<p>"+
								"<t>"+bh+" 时 "+bm+" 分  ~ "+eh+" 时 "+em+" 分 . </t>"+
								"<n style='display:inline-block'>"+weekName+"</n>"+
								"<a href='##' class='del'>  | 删除</a>"+
							"</p>";
				$(".timeDivShow").prepend(time);
			}
			$(".selectTimeDiv").addClass("hide");
			$(".addTimeDiv").removeClass("hide");
			
			$(".week").each(function(index, ele){
				if ($(this).hasClass("weekSelect")) {
					$(this).removeClass("weekSelect").children(".weekBoard").show();
				}
			})
		}else{
			tipshow('请选择至少一天接待时间','warn');
		}
	}
	//点击删除，删除设置的时间段；
	$(document).on("click", ".del", function(){
		var resetWeekArr = $(this).siblings("n").html().split(" ");
		var weekArr = $(".week").text().split(" ");
		//拿到删除的时间段中的星期几；
		for(var i=0; i<resetWeekArr.length; i++){
			if (resetWeekArr[i]=='') {continue}
			var n = $.inArray(resetWeekArr[i], weekArr);
			if (n == -1) {n = null}
			$(".week .weekBoard").eq(n).hide();
		}
		
		$(this).parent().remove();
		
		var d_time = $(this).siblings("t").text()+$(this).siblings("n").text();
		var d_index = $.inArray(d_time, storeTimes);
		storeTimes.splice(d_index, 1);
		$("#store_time").val(storeTimes);
		//手动重新验证
		Revalidate('store_time'); 
	});

	
	//接待时间部分的“取消”点击事件
	$(".cancle").click(function(){
		$(".selectTimeDiv").addClass("hide");
		$(".addTimeDiv").removeClass("hide");
	});
	//点击“新增时间段”事件；
	$(".addTimeDiv").click(function(){
		$(".selectTimeDiv").removeClass("hide");
		$(".addTimeDiv").addClass("hide");
	});
	
	//表单验证；
	$('#defaultForm').bootstrapValidator({
        message: '这个值是无效的',
        excluded: [':disabled'],
        feedbackIcons: {
//          valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: {
        	intro:{
        		validators: {
                	notEmpty: {
                        message: '请填写范围介绍'
                	},
                }
        	},
        	sending_fee:{
        		validators: {
                	notEmpty: {
                        message: '请填写起送金额'
                 	}
                }
        	},
            shipping_fee: {
            	validators: {
                	notEmpty: {
                        message: '请填写配送费'
                 	}
                }
            },
            store_img:{
            	validators: {
                    notEmpty: {
                        message: '请选择至少一张图片'
                    }
                }
            },
            store_time:{
            	validators: {
                    notEmpty: {
                        message: '接待时间不可为空'
                    }
                }
            }
        }
  	});
	
	//手动重新验证
	function Revalidate(ele){
		$('#defaultForm')
			.data('bootstrapValidator')
	        .updateStatus(ele, 'NOT_VALIDATED', null)
	        .validateField(ele);
	}
})