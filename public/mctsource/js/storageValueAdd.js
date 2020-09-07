"use strict"
//文档加载完成
$(function() {
    $(".js-submit ").click(function() {
        var title = $("#title").val();
        if (title == '') {
            tipshow("储值规则名称不能为空", "warn");
            return;
        }
        var money = $("#money").val();
        if(money == ''){
            tipshow("储值金额不能为空", "warn");
            return;
        }
        var add_score = $("#add_score").val();
        var id = $("#id").val();
        var url = "";
        var data = { title: title, money: money, add_score: add_score }
        if(id!="0") {
            data.id = id; 
        }   
        $.ajax({
            url: "/merchants/member/addBalanceRule",
            type: 'get',
            data: data,
            dataType: "json",
            success: function(res) {
                if (res.errCode == 0) {
                    tipshow("操作成功");
                    setTimeout(function() {
                        location.href="/merchants/member/storageValue";
                    }, 1000);
                } else {
                    tipshow(res.errMsg, "warn");
                }
            },
            error: function(res) {
                console.log(res);
            }
        });
    });
    edit();
    // // 编辑页面时 
    function edit(){
    	if(edit_id!="0"){ 
    		$("#money").val(edit_money/100); 
    		$("#edit_title").html("编辑储值规则");
    	}
    }

})