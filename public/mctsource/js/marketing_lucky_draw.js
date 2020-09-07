$(function(){
	$('#datetimepicker1,#datetimepicker2').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',              
        dayViewHeaderFormat: 'YYYY 年 MM 月DD日',     
        useCurrent: true,                           
        stepping: 5,  								 
        collapse:true,                               
        showClear:true,                               
        showClose:true,                             
        showTodayButton:true,            
        locale:'zh-cn',   
        allowInputToggle:true, 
        useCurrent: true,
  	});
	$("#datetimepicker2").datetimepicker({useCurrent: false})
  //datetimepicker1 的时间一定小于 datetimepicker2 的时间；
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    });
    
    //左侧显示有效时间
    $('input[name="Btime"], input[name="Ctime"]').on("blur", function(){
    	$("#beginTime").html("").html($('input[name="Btime"]').val());
    	$("#closeTime").html("").html($('input[name="Ctime"]').val());
    })
    
    //中奖设置中的奖品切换
    $(".set_prizeDiv .prize_list li").each(function(index, ele){
    	$(this).click(function(){
    		$(".set_prizeDiv .prize_list li").removeClass("selected");
    		$(this).addClass("selected");
    		$(".prizeDiv").addClass("hide");
    		$(".prize_selectDiv_"+(index+1)+"").removeClass("hide")
    	})
    })
    
    //第四步中点击 复制  实现链接的复制；
    $("#copy").click(function(){
    	var e=document.getElementById("linkCopy");
        e.select(); //选择对象 
        document.execCommand("Copy");  //执行浏览器复制命令
     	//alert("已复制好，可贴粘。"); 
	    var successEle = $(".successPromrt");
		successEle.removeClass("hide");
		successEle.animate({"opacity":0},4000);
		setTimeout(function(){
			successEle.addClass("hide");
			successEle.animate({"opacity":1},1);
		}, 3000);
   })
    
    //创建活动中的表单验证
    $('#defaultForm').bootstrapValidator({
        message: '填写的值不合法',
        feedbackIcons: {
//          valid: 'glyphicon glyphicon-ok',
//          invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            activeName: {
                validators: {
                    notEmpty: {
                        message: '活动名称不能为空'
                    }
                }
            },
            Btime: {
                validators: {
                    notEmpty: {
                        message: '开始时间不能为空'
                    }
                }
            },
            Ctime: {
                validators: {
                    notEmpty: {
                        message: '结束时间不能为空'
                    }
                }
            },
            lose_integral: {
                validators: {
                    notEmpty: {
                        message: '请填写'
                    }
                }
            },
            send_integral: {
                validators: {
                    notEmpty: {
                        message: '请填写'
                    }
                }
            },
            prize_send_integral_1: {
                validators: {
                    notEmpty: {
                        message: '请填写'
                    }
                }
            },
            prize_send_integral_2: {
                validators: {
                    notEmpty: {
                        message: '请填写'
                    }
                }
            },
            prize_send_integral_3: {
                validators: {
                    notEmpty: {
                        message: '请填写'
                    }
                }
            },
            prize_send_integral_4: {
                validators: {
                    notEmpty: {
                        message: '请填写'
                    }
                }
            },
        }
    });

	var n = 0;
    $('.next').click(function(){
        var judge =$('#defaultForm').data('bootstrapValidator').isValid();
       	if(!judge){   // 未通过验证
            $('#defaultForm').data('bootstrapValidator').validate();
       	}else{       // 通过验证
       		n ++;
            $(".steps").addClass("hide").siblings(".step_"+(n+1)+"").removeClass("hide");
            $(".functionBtn .prev").removeClass("hide");
            $(".step li:eq("+n+")").addClass("active");
	       	if (n==3) {
	       		$(".prev, .next").addClass("hide").siblings(".reset, .sure").removeClass("hide");
	       	}
       	}
    });
   	$(".reset").click(function(){
   		n =0;
   		$(".steps").addClass("hide").siblings(".step_"+(n+1)+"").removeClass("hide");
        $(".prev, .reset, .sure").addClass("hide").siblings(".next").removeClass("hide");
        $(".step li").removeClass("active");
        $(".step li:eq("+n+")").addClass("active");
   	})
	$(".prev").click(function(){
		n --;
        $(".steps").addClass("hide").siblings(".step_"+(n+1)+"").removeClass("hide");
        $(".functionBtn .prev").addClass("hide");
        $(".step li:eq("+(n+1)+")").removeClass("active");
        if (n==1) {
        	$(".functionBtn .prev").removeClass("hide");
        }
	})
	
	
	
	
	
	
	
	
	//蒙板设置
    $(".board").css({width: $(window).width()+"px",
					height: $(window).height()+"px",})
    
    //点击 上传奖品图片 显示弹出层；
    $(".update").click(function(){
    	$("#maskDiv").show();
		$("#maskDiv").css({width: $(window).width()+"px",height: $(window).height()+"px"});
	})
    
    //输入框点击动画；
	$("#search").focus(function(){
		$(this).animate({width: "200px"});
	})
	$("#search").blur(function(){
		$(this).animate({width: "100px"});
	})
	
	
	//关闭蒙板
	$(".closeImg").click(function(){
		$("#maskDiv").hide();
	})
	
	//------------图片上传弹出层的切换----------------
	function hideAndShow(hideEle, showEle){
		$(hideEle).hide();
		$(showEle).show();
	}
	$("a[href='#uploadImg']").click(function(){
		hideAndShow("#layer","#uploadImgLayer");
	})
	$("a[href='#layer']").click(function(){
		hideAndShow("#uploadImgLayer","#layer");
	})
	$("a[href='#uploadImgLayer']").click(function(){
		hideAndShow("#layer","#iconLibraryLayer");
	})
	$("a[href='#firLayer']").click(function(){
		hideAndShow("#iconLibraryLayer","#layer");
	})
	
	//--------------图标库弹出层------------------------
	//风格、颜色、类型选择；
	function clearStyle(eleSect){
		for (var i=0; i<$(eleSect).length; i++) {
			$(eleSect+":eq("+i+")").removeClass("selected");
		}
	}
	$("#style a").click(function(){
		clearStyle("#style a");
		$(this).addClass("selected");
	})
	$("#color a").click(function(){
		clearStyle("#color a");
		$(this).addClass("selected");
	})
	$("#type a").click(function(){
		clearStyle("#type a");
		$(this).addClass("selected");
	})
	
	//选择图片显示边框
	$("#iconImgSelect li").click(function(){
		for (var i=0; i<$("#iconImgSelect li").length; i++) {
			$("#iconImgSelect li:eq("+i+")").css("border-color","#ccc");
			$("#iconImgSelect li:eq("+i+")").removeClass("li_select")
		}
		$(this).css("border-color", "cornflowerblue");
		$(this).addClass("li_select")
	})
	
	//分页切换显示；
	$('.pagination').jqPaginator({
	    totalPages: 10,
	    visiblePages: 5,
	    currentPage: 1,
	    onPageChange: function (num, type) {
	        $('#show').html('当前第' + num + '页');
	        $("#iconImgSelect").css({"top": -(num-1)*parseInt($("#iconLibraryContent #iconImgShow").css("height"))+"px"});
	    }
	});
	
	//点击确定显示图片
	$("#iconLibraryLayerBtn button").click(function(){
		var showDiv = $(".prizeDiv").not($(".hide"));
		var imgSrc = $(".li_select img").attr("src");
		$("<img src='"+imgSrc+"' class='addSeleImg'/>").appendTo(showDiv.find(".selImg"));
		showImg("prize_selectDiv_1", "#prize_12 img");
		showImg("prize_selectDiv_2", "#prize_14 img, #prize_4 img");
		showImg("prize_selectDiv_3", "#prize_1 img, #prize_16 img");
		showImg("prize_selectDiv_4", "#prize_2 img, #prize_9 img");
		$("#maskDiv").hide();
		if(showDiv.find(".selImg").html() != ""){
			showDiv.find(".update").attr("disabled","disabled")
		}
		function showImg(showJudge, showEle){
			if (showDiv.hasClass(showJudge)) {
				$(showEle).prop("src", imgSrc).css("background", "#cb1573");
			}
		}
	});
    
    //点击删除 去除图片
	$(".clear").each(function(index, ele){
		$(this).click(function(){
			var showDiv = $(".prizeDiv").not($(".hide"));
			$(this).parents(".form-group").find(".selImg").html("");
			$(this).parents(".form-group").find(".update").attr("disabled",false);
			clearImg("prize_selectDiv_1", "#prize_12 img","public/hsadmin/images/present3.png");
			clearImg("prize_selectDiv_2", "#prize_14 img, #prize_4 img", "public/hsadmin/images/present2.png");
			clearImg("prize_selectDiv_3", "#prize_1 img, #prize_16 img","public/hsadmin/images/present1.png");
			clearImg("prize_selectDiv_4", "#prize_2 img, #prize_9 img", "public/hsadmin/images/present3.png");
			function clearImg(clearJudge, showEle, img_src){
				if (showDiv.hasClass(clearJudge)) {
					$(showEle).prop("src", img_src);
				}
			}
		})
	})
	
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
		
	$("#img").on("change",".filepath1",function() {
        //alert($('.imgbox1').length);
        var srcs = getObjectURL(this.files[0]);   //获取路径
        //this指的是input
        /* $(this).nextAll(".img22").attr("src",srcs);    //this指的是input
         $(this).nextAll(".img22").show();  //fireBUg查看第二次换图片不起做用*/
        var htmlImg='<div class="imgbox1">'+
                	'<div class="imgnum1">'+
                		'<input type="file" class="filepath1" />'+
                		'<span class="close1">×</span>'+
                		'<img src="public/hsadmin/images/add.png" class="img11" />'+
                		'<img src="'+srcs+'" class="img22" />'+
                	'</div>'+
                '</div>';
		if($(".imgbox1").length==5){
			return false;
		}
        $(this).parent().parent().before(htmlImg);
        $(this).val('');    //必须制空
        $(this).parent().parent().prev().find(".img11").hide();   //this指的是input
        $(this).parent().parent().prev().find('.close1').show();

        $(".close1").on("click",function() {
            $(this).hide();     //this指的是span
            $(this).nextAll(".img22").hide();
            $(this).nextAll(".img11").show();
            if($('.imgbox1').length>1){
                $(this).parent().parent().remove();
            }
        })
    })
})