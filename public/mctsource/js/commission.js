$(function () {

    //     // 开始时间
    $('#startTime,#endTime').datetimepicker({
        // minDate: new Date(),
        format: 'YYYY-MM-DD HH:mm:ss',
        dayViewHeaderFormat: 'YYYY 年 MM 月',
        showClear: true,
        showClose: true,
        showTodayButton: true,
        locale: 'zh-cn',
        focusOnShow: true,
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
    var host = location.host;
    var page = 0;
    // 加载数据
    init("","","","");
    function init(start,end,nickname,status) {
        $("tbody").empty();
        $.get("/merchants/distribute/cashLog", { start: start, end: end, nickname: nickname, status: status ,page:page}, function (res) {

            if (res.status == 1) {
                var html = "<tr><th><input type='checkbox' id='checkAll' /></th><th>微信ID</th><th>微信昵称</th><th>提现金额</th><th>提现方式</th><th>收款信息</th><th>申请时间</th><th>状态</th><th>操作</th></tr>";
            
                var dataArr = res.data.cash;
                for (var i = 0; i < dataArr.length; i++) {

                    var type = pdType(dataArr[i].type);
                    var time = formatTime(dataArr[i].created_at);
                    var status = statu(dataArr[i].status);

                    html += "<tr><td><input type='checkbox' class='checkItem' value='"+dataArr[i].id+"' /></td>";
                    html += "<td>" + dataArr[i].member.wechat_id + "</td>";
                    html += "<td>" + dataArr[i].member.nickname + "</td>";
                    html += '<td class="price">¥ ' + dataArr[i].money + '</td>';
                    html += '<td class="type">转' + type + '</td>';
                    if (dataArr[i].type == 3){
                        html += '<td class="type">微信</td>';
                    }else {
                        html += '<td class="receiving-mesg"><div>收款人:' + dataArr[i].name + '</div><div>收款账号:' + dataArr[i].account + '</div></td>';
                    }

                    html += "<td><div>" + time[0] + "</div><div>" + time[1] + "</div></td>";
                    html += "<td>" + status + "</td>";
                    if(dataArr[i].status == 0){
                        html += '<td class="operation" data-id=' + dataArr[i].id + ' data-status=' + dataArr[i].status + '><span class="will-money">打款</span> ';
                        html += '<span class="will-reject">拒绝</span> </td></tr>';
                    }
                    if(dataArr[i].status == 1){
                        html += '<td class="operation" data-id=' + dataArr[i].id + ' data-status=' + dataArr[i].status + '><span class="sure-money">确认已打款</span></td></tr>';
                    }
                    if(dataArr[i].status == 2){
                       html += "<td></td></tr>";
                    }
                    if(dataArr[i].status == 3){
                        html += "<td></td></tr>";                       
                    }
                }
                $("table").append(html);


                // 分页 
                var pageInfo = res.data.pageInfo;
                $("ul.pagination").empty();
                var li = '<li><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';

                for(var i = 0; i < pageInfo.pageNum; i++){
                    li += '<li><a ">' + (i + 1) + '</a></li>';
                } 
                li += '<li><a  aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
                if(pageInfo.pageNum>0)
                    $("ul.pagination").append(li);
            }

            function pdType(type) {

                if (type == 1) {
                    return "银行卡";
                }
                if (type == 2) {
                    return "支付宝";
                }
                if (type == 3) {
                    return "微信";
                }

            }
            function formatTime(time) {

                return time.split(" ");

            }
            function statu(status) {

                if (status == 0) {
                    return "等待审核";
                }
                if (status == 1) {
                    return "同意提现";
                }
                if (status == 2) {
                    return "确认已打款";
                }
                if (status == 3) {
                    return "拒绝提现";
                }

            }

        })
    }
    
    //点击全选
    var outputIdArr = [];
    $(document).on("click", "#checkAll", function(){
    	var checked = $(this).prop("checked")
    	if(checked){
    		for(var i=0; i<$(".checkItem").length; i++){
    			if($(".checkItem").eq(i).prop("checked")){
    				continue;
    			}else{
    				outputIdArr.push($(".checkItem").eq(i).val())
    			}
    		}
    		$(".checkItem").prop("checked", true);
    	}else{
    		outputIdArr = []
    		$(".checkItem").prop("checked", false);
    	}
    })
    //单个选择
    $(document).on("click", ".checkItem", function(){
    	var checked = $(this).prop("checked")
    	if(checked){
    		outputIdArr.push($(this).val())
    	}else{
    		var idIndex = outputIdArr.indexOf($(this).val());
    		outputIdArr.splice(idIndex, 1)
    	}
    	//全选按钮的是否选中
    	if(outputIdArr.length == $(".checkItem").length){
    		$("#checkAll").prop("checked", true);
    	}else{
    		$("#checkAll").prop("checked", false);
    	}
    });
    
    //导出所选记录
    $(".outputChecked").click(function(){
    	if(outputIdArr.length==0){
    		layer.tips('请选择要导出的记录','.outputChecked',{
    			tips:[1,'#08BDB7']
    		});
    		return;
    	}
    	window.location.href='/merchants/distribute/exportXls?orderids='+outputIdArr+'&all=1';
    })

	//导出所有记录
    $(".outputAll").click(function(){
    	window.location.href='/merchants/distribute/exportXls?all=2';
    })

    // 切换状态
    var states = "";
    $(".state span").on("click", function () {
        $(".state span").removeClass("orange-under-line");
        $(this).addClass("orange-under-line");
        page = 0;
        // 态：'0:等待审核，1：同意提现，2：确认已打款，3：拒绝提现
        var index = $(this).index(".state span");
        if(index == 0){
            states = "";
        }
        if(index == 1){
            states = 0;
        }
        if(index == 2){
            states = 1;
        }
        if(index == 3){
            states = 2;
        }
        if(index == 4){
            states = 3;
        }
        init("","","",states);
    })

    // 最近7天
    $(".set-week").on("click", function () {

        var timestamp = new Date().getTime(); //当前时间戳
        var weekstamp = 7 * 24 * 60 * 60 * 1000; //一周时间戳

        $("#startTime").val((new Date(timestamp - weekstamp)).format('yyyy-MM-dd hh:mm:ss'));
        $("#endTime").val((new Date(timestamp)).format('yyyy-MM-dd hh:mm:ss'));

    })

    // 最近30天
    $(".set-month").on("click", function () {

        var timestamp = new Date().getTime(); //当前时间戳
        var monthstamp = 30 * 24 * 60 * 60 * 1000; //30天时间戳

        $("#startTime").val((new Date(timestamp - monthstamp)).format('yyyy-MM-dd hh:mm:ss'));
        $("#endTime").val((new Date(timestamp)).format('yyyy-MM-dd hh:mm:ss'));

    })

    // 点击筛选
    $(document).on("click", ".form .screen", function () {

        var start = $("#startTime").val() || "";
        var end = $("#endTime").val() || ""
        var nickname =  $("input[name='wxID']").val() || "";
        var status = $("select").val() || "";
        init(start,end,nickname,status);

    })

    // 点击打款
    var ajax = {
        id:"",
        status:""    // 1一次统一打款 2二次确认打款 3是拒绝
    }
    $(document).on("click", ".will-money", function () {

        var type = $(this).parent().parent().find(".type").text();
        var price = $(this).parent().parent().find(".price").text();

        ajax.id = $(this).parent().data("id");
        ajax.status = 1;
        
        $(".bgcolor .price").text(price);
        $(".bgcolor .type").text(type);

        $(".bgcolor , .play-money").show();

    })

    // 同意打款
    $(document).on("click", ".agree", function () {
        var type = $(this).parent().parent().find(".type").text();
      
        $(".play-money").hide();
        $(".sure-play-money").show();
    
        dkAjax();
    })

    // 不同意打款
    $(document).on("click", ".no-agree", function () {

        $(".bgcolor , .play-money").hide();

    })

    // 二次打款 确认
    $(document).on("click", ".make-sure", function () {
        // 发起AJAX
        dkAjax();
        $(".bgcolor , .sure-play-money").hide();

    })

    // 二次确认打款 取消
    $(document).on("click", ".cancel", function () {
        
        ajax.status = 2;
        $(".bgcolor,.sure-play-money").hide();

    })

    // 二次确认已打款
    $(document).on("click",".sure-money",function(){
        var price = $(this).parent().parent().find(".price").text();
        $(".bgcolor .price").text(price);
        $(".bgcolor , .sure-play-money").show();
        ajax.id = $(this).parent().data("id");
        ajax.status = 2;
        
    })


    

    // 拒绝打款
    $(document).on("click", ".will-reject", function () {
        $(".bgcolor ,.off-money").show();
        ajax.id = $(this).parent().data("id");
    })

    // 拒绝打款 确定
    $(document).on("click", ".yes", function () {
        $(".off-money").hide();
        $(".sure-off-money").show();
    })

    // 拒绝打款 二次确定
    $(document).on("click", ".two-yes", function () {
        // 发起AJAX

        $.get("/merchants/distribute/refuse/" + ajax.id,function(res){
           
            tipshow(res.info);
            $(".bgcolor , .sure-off-money , .off-money").hide();
            init("","","","");
        })  

    })

    // 拒绝打款 取消
    $(document).on("click", ".no", function () {
        $(".bgcolor, .off-money, .sure-off-money").hide();
    })

    // 打款ajax
    function dkAjax(){
        $.get("/merchants/distribute/agree/" + ajax.id +  "/" +  ajax.status ,function(res){
            if (res.status==1){
                tipshow(res.info);
                if (ajax.status == 1){
                    ajax.status=2;
                }
                init("","","","");
            }else{
                tipshow(res.info,'warn');
                
            }

        })
       
    }

    // 点击分页
    $(document).on("click",".pagination li",function(){
        var text = $(this).text(); // 
        page = text;
        if(text == "«"){
            page = 0;
        }
        if(text == "»"){
            page = $(".pagination li").length - 1;
        }

        init("","","",states)
    })

    Date.prototype.format = function (format) {
        var date = {
            "M+": this.getMonth() + 1,
            "d+": this.getDate(),
            "h+": this.getHours(),
            "m+": this.getMinutes(),
            "s+": this.getSeconds(),
            "q+": Math.floor((this.getMonth() + 3) / 3),
            "S+": this.getMilliseconds()
        };
        if (/(y+)/i.test(format)) {
            format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
        }
        for (var k in date) {
            if (new RegExp("(" + k + ")").test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length == 1
                    ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
            }
        }
        return format;
    }
})
