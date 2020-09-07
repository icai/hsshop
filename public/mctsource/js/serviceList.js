// file created by 华亢 at 2018/7/12
(function(obj){
   /** 
    * author 华亢 2018/7/17
    * toDo 获取服务信息 状态1的数据拼接
    * 进度 1
   */
  var firstStatusContent = null;
    $.ajax({
        url:'/merchants/fee/selfProduct/select/all',
        type:'get',
        success:function(res){
            if(res.errCode == 0){
            //1 对数据进行解析处理
            firstStatusContent = res.data
            for(var i=0;i<firstStatusContent.length;i++){
                firstStatusContent[i].content = JSON.parse(res.data[i].content)
                firstStatusContent[i].introduction = JSON.parse(res.data[i].introduction)
            }
            $('.service-item').each(function(idx){
                $(this).find('.i-title').text(firstStatusContent[idx].title)
                .siblings('.item-type').text(firstStatusContent[idx].versionName)
                .siblings('div').find('.num').text(firstStatusContent[idx].price)
                // ------ 具体服务内容 -------
                $(this)
                .find('.i-basis').text(firstStatusContent[idx].introduction[0].content)
                .siblings('.i-market').text(firstStatusContent[idx].introduction[1].content)
                .siblings('.i-data').text(firstStatusContent[idx].introduction[2].content)
            })
            return;
            }
        }
    })
  // status 1 点击立即订购 toDo 状态2的内容填充
    $('.rightNowPay').click(function(){
        var id = $(this).attr('data-type')
        console.log(id)
        location.href = host + 'merchants/capital/fee/serviceDetail?id=' + id
    })
})(window)