"use strict"
//文档加载完成
$(function() {
    //删除储值规则
    $(".del").click(function(e) {
        e.stopPropagation();
        var _this = this;
        var id = $(this).parent().attr("data-id");
        showDelProver($(_this), function() {
            $.ajax({
                url: "/merchants/member/delBalanceRule",
                type: "get",
                data: { id: id },
                success: function(res) {
                    if (res.errCode == 0) {
                        tipshow("删除成功");
                        $(_this).parent().parent().remove();
                    } else {
                        tipshow(res.errMsg,'warn'); // 增加会员卡删除失败错误提示 update  by 倪凯嘉 2019-06-13
                    }
                },
                error: function(res) {
                    console.log(res);
                }

            })
        })
    });
});