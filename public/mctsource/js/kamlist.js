// add by 黄新琴 2018-8-7
$(function(){
     //使卡密失效
     $(".J_invalid").click(function(e){
        e.stopPropagation();//阻止事件冒泡
        var id = $(this).data("id");
        showDelProver($(this), function(){
            $.get('/merchants/cam/invalid?id='+id,function (res) {
                if(res.status==1){
                    tipshow("操作成功");
                    window.location.reload();
                }else{
                    tipshow(res.info,'warn');
                } 
            })
        }, '确定使失效吗?');
    });
    //删除卡密
    $(".pagecat-del").click(function(e){
        e.stopPropagation();//阻止事件冒泡
        var id = $(this).data("id");
        showDelProver($(this), function(){
            //执行删除
            $.get('/merchants/cam/delCam?id='+id,function (res) {
                if(res.status==1){
                    tipshow("删除成功");
                    window.location.reload();
                }else{
                    tipshow(res.info,'warn');
                } 
            })
        }, '确定要删除吗?');
    });
})