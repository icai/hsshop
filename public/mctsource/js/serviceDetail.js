(function(window){
    // step1: 获取页面参数
    function GetRequest() {
        var url = location.search; //获取url中"?"符后的字串
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            var strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
            }
        }
        return theRequest;
    }
    // step2: 数据渲染
    var id = GetRequest().id;
    getOneService(id)

    /** 
     * params id
     * type string
     * toDo: 获取数据并渲染
    */
    function getOneService(id){
        $.ajax({
            url:'/merchants/fee/selfProduct/select/one',
            data:{id:id},
            type:'get',
            success:function(res){
                if(res.errCode == 0){
                    var secondStatusContent = res.data;
                    var items = $('.comfirm-table tbody tr');
                    secondStatusContent.content = JSON.parse(res.data.content)
                    secondStatusContent.introduction = JSON.parse(res.data.introduction)
                    items.each(function(i){
                        $(this).find('td').eq(0).text(secondStatusContent.content[i].typeName).next('td').text(secondStatusContent.content[i].content)
                    })
                    $('.getPrice').text(secondStatusContent.price);
                    $('.getTitle').text(secondStatusContent.title)
                    $('.getVersionName').text('（'+secondStatusContent.versionName+'）')
                    $('.getYear').text(secondStatusContent.year+'年')
                }
            }
        })
    }

    // step3: 点击立即支付 toDo 跳转至status 3
    $('.pay-now').click(function(e){
        e.stopPropagation()
        if($('#agreement').prop('checked')){
            var id = GetRequest().id;
            createOrder(id)
        }else{
            tipshow('请勾选并同意《会搜云平台协议》','warn')
        }
        return;
    })

    // 生成orderId
    function createOrder(productId){
        $.ajax({
            url:'/merchants/fee/order/submit',
            data:{productId:productId},
            type:'post',
            dataType: 'json',
            async:true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                if(res.errCode == 0){
                    orderId = res.data 
                    location.href = host + 'merchants/capital/fee/order/pay/list?orderId='+orderId;
                }else{
                    tipshow(res.errMsg,'warn')
                }
            }
        })
      }

    // 点击返回status 1
    $('.couldBeBacked').click(function(e){
        e.stopPropagation();
        location.href = host+'merchants/capital/fee/serviceList'
    })
    //   ------------------合同点击出现--------------------
    $('.agreementShow').click(function(e){
        showCheckPop(e)
    })
    function showCheckPop(e){
        e.stopPropagation();
        var t_index = layer.open({
            type: 1,
            title: '会搜云微商城服务协议',
            closeBtn:true, 
            move: true,
            shadeClose:false,
            area: ['626px', '560px'],
            content: $('#contract')
    });
    /*取消订单关闭按钮*/
    $("body").on("click",".layui-layer-setwin",function(e){
        closePop(e,t_index)
    });
    $("body").on("click",".renew-close",function(e){
            closePop(e,t_index)
        });
    }
    /** 
     * author 华亢
     * created 2018/7/6
     * toDo 关闭弹框
     */
    function closePop(e,t_index){
        e.stopPropagation();
        if(t_index)
            layer.close(t_index);
    }
})(window)