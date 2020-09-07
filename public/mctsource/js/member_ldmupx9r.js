$(function(){
    // 删除确定
    $(document).on('click','.sure_btn',function(){
        hideDelProver();//隐藏删除prover
        if(!url){
            tipshow('删除失败！','warn');
        }

        $.ajax({
            type: "get",
            url: url,
            dataType: "json",
            success: function (data) {
                if(data.status=="0"){
                    tipshow(data.info)
                }else{
                   
                    tipshow(data.info,'warn');
                    /*

                     */
                    window.location.href = window.location.href;
                }
            }
        });

        return false;
    })
    // 删除确定
    $(document).on('click','.cancel_btn',function(){
        hideDelProver();//隐藏删除prover
    })
    $('.delete').click(function(){
        url = $(this).attr('href');
        showDelProver($(this));//显示删除按钮
       // alert('a')
        return false;
    })  
})