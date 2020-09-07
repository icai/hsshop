/** 
 * cread file by 华亢 at 2018/7/16
 * for invoiceList.blade.php && invoiceList.scss
*/
(function(){
    //获取发票列表 && 渲染
    $.ajax({
        url:host+'merchants/fee/invoice/select/all',
        // data:{page:1},
        type:'get',
        success:function(res){
            if(res.errCode == 0){
                renderContent(res.data)
            }
        }
    })
    
    //渲染列表
    function renderContent(arr){
        var html = '';
        if(arr.length > 0){
            for(var i=0,l=arr.length;i<l;i++){
                html+='<ul class="clearfix">'
                html+=  '<li>'+arr[i].create_time+'</li>\
                        <li>'+arr[i].amount+'元</li>\
                        <li>'+ arr[i].typeName +'</li>\
                        <li>'+ arr[i].styleName +'</li>\
                        <li>'+ arr[i].statusName +'</li>\
                        <li>'+arr[i].express_no+'</li>\
                        <li>'+ arr[i].invoiceLoad +'</li>\
                        <li><a class="check" href="javascript:void(0);" data-orderNo="'+ arr[i].id +'">查看</a></li></ul>'
            }
        }else{
            html = '<p class="noData">暂无数据</p>'
        }
        $('.table-body').html(html)
    }


    $('body').on('click','.check',function(e){
        e.stopPropagation();
        var obj = {};
        obj.id = $(this).attr('data-orderNo')
        getOneDetail(e,obj)
    })
    //通过订单号查询某个发票信息
    function getOneDetail(e,obj){
        console.log(obj,222222)
        $.ajax({
            url:'/merchants/fee/invoice/select/one',
            data:obj,
            type:'get',
            success:function(res){
                if(res.errCode == 0){
                    renderDetail(e,res.data)
                }
            }
        })
    }
    function renderDetail(e,obj){
        var tax_number = obj.tax_number?obj.tax_number:'无'
        var html = '<div id="checkPop"><div class="invoiceDetail">\
                    <p class="title">发票信息</p><ul><li>发票类型:'+ obj.typeName +'</li>\
                    <li>发票金额:'+ obj.amount +'元</li>\
                    <li>发票抬头:'+ obj.title +'</li>\
                    <li>纳税人识别号:'+ tax_number +'</li>'
        if(obj.type == 2){
            //专票信息
            html += '<li class="red">开户行地址:'+ obj.deposit_bank_address +'</li>\
                    <li class="red">开户行:'+ obj.deposit_bank_account +'</li>\
                    <li class="red">公司地址:'+ obj.company_address +'</li>'
            if(obj.company_telephone){
                html+='<li class="red">公司电话:'+ obj.company_telephone +'</li>'
            }
        }
        html+='</ul></div>'
        if(obj.style == 1){
            //纸质发票
            html+='<div class="consignee"><p class="title">收件人信息</p><ul><li>收货人:'+ obj.receiver +'</li>\
            <li>联系电话:'+ obj.telephone +'</li>\
            <li>地址: '+obj.detail_address+'</li></ul></div>'
        }
        var image = ''
        if(obj.status == 0){
            //待开具
            image = 'waitPrint'
        }else{
            image = 'havePrinted'
        }
        html+='<div class="printLogo"><img src="'+host+'static/images/'+ image +'.png" /></div></div>'
        showCheckPop(e,html)
    }
    function showCheckPop(e,str){
        e.stopPropagation();
        var t_index = layer.open({
            type: 1,
            title: '开票申请信息',
            closeBtn:true, 
            move: false,
            shadeClose:false,
            area: ['560px'],
            content: str
        });
        /*取消订单关闭按钮*/
        $("body").on("click",".layui-layer-setwin,.layui-layer-shade",function(e){
            closePop(e,t_index)
        });
        $("body").on("click",".renew-close",function(e){
            closePop(e,t_index)
        });
    }

    
    $('body').on('click','.download',function(e){
        e.stopPropagation();
        fileDownLoad($(this).attr('data-file'))
    })
    // 发票下载 fileName 文件名
    function fileDownLoad(fileName){
        $.ajax({
            url:'',
            data:{fileName:fileName},
            type:'get',
            success:function(res){
                console.log(res)
            }
        })
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
})()