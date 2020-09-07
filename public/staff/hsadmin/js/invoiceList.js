(function(window){
    // event1: 页面渲染
    getListConent({})
    
    //list1:总数据
    //step1: 获取数据
    /** 
     * author 华亢 at 2018/7/24
     * params obj 
     * type: object
     * toDo: 获取列表数据
    */
    function getListConent(obj){
        $.ajax({
            url:'/staff/fee/invoice/select/all',
            type:'get',
            data:{
                name:obj.name, // 店铺名称
                requestNo:obj.requestNo,//申请编号
                status:obj.status//发票状态
            },
            dataType:'json',
            success:function(res){
                if(res.errCode == 0){
                    renderList(res.data)
                }
            }
        })
    }
    //step2:渲染列表数据
    /** 
     * author 华亢 at 2018/7/24
     * params arr
     * type arrray
     * toDo:渲染列表数据
    */
    function renderList(arr){
        var html = ''
        if(arr.length>0){
            for(var i=0,l=arr.length;i<l;i++){
                html+='<ul class="table_body  flex-between"><li>'+ arr[i].request_no  +'</li>';//发票编码
                html+='<li>'+ arr[i].widName+'</li>'//店铺名称
                html+='<li>'+ arr[i].mphone +'</li>'//手机号码
                html+='<li>'+ arr[i].serviceName +'</li>'
                html+='<li>'+ arr[i].create_time +'</li>'//申请时间
                html+='<li>'+ arr[i].amount +'</li>'//发票金额
                html+='<li>'+ arr[i].typeName +'</li>'//发票类型
                html+='<li>'+ arr[i].styleName +'</li>'//发票性质
                html+='<li>'+ arr[i].title+'</li>'//发票信息
                html+='<li>'+ arr[i].statusName +'</li>'//状态
                html+='<li><a href="javascript:;" class="modify" data-id="'+ arr[i].id +'">查看</a></li></ul>'
            }    
        }else{
            html = '<p class="noData">暂无数据</p>'
        }
        $('.t_body').html(html)
    }

    //list2:查询某个发票明细
    //step1: 获取该发票数据
    /**
     * author 华亢 at 2018/07/24
     * params id 单个项的id
     * type: number
     * toDo: 获取单个数据
     */
    function getOneDetail(id){
        $.ajax({
            url:'/staff/fee/invoice/select/one',
            type:'get',
            data:{id:id},
            success:function(res){
                if(res.errCode == 0){
                    var data = res.data
                    renderPopPaper(data)
                }
            }
        })
    }
    //渲染弹框--单个数据
    /** 
     * author 华亢 at 2018/07/24
     * params obj
     * type object
     * toDo: 渲染弹框数据
    */
    function renderPopPaper(obj){
        var html = '<div id="pop" data-id="'+ obj.id +'"><dl class="clearfix">\
        <dt>开票信息</dt>\
        <dd><p>发票类型：'+ obj.typeName +'</p>\
        <p class="sldSend" data-style="'+ obj.style +'">发票性质：'+ obj.styleName +'</p>\
        <p>发票抬头：'+ obj.title +'</p>\
        <p>纳税人识别号：'+ (obj.tax_number?obj.tax_number:'空') +'</p>\
        <p>金额：'+ obj.amount +'元</p>'
        if(obj.type==2){
            html+='<p>地址及电话：'+ obj.company_address+'---'+ obj.company_telephone+'</p>\
            <p>开户行及账号：'+ obj.deposit_bank_account+'---'+ obj.deposit_bank_address +'</p>'
        }
        if(obj.style == 1){
            html += '</dd></dl><dl class="clearfix"><dt>收件人信息</dt><dd>\
            <p>收货人：'+ obj.receiver +'</p>\
            <p>联系电话：'+ obj.telephone +'</p>\
            <p class="addr">地址：'+ obj.detail_address +'</p>'
        }
        remark = obj.remark?JSON.parse(obj.remark).join(','):'';
        html+='</dd></dl>\
        <dl class="clearfix"><dt>备注</dt><dd>\
        <textarea name="remark" class="form" cols="50" rows="5" placeholder="用于工作人员备注发票的修改信息及收件人信息">'+ remark +'</textarea>\
        </dd></dl>'
        html+='<dl class="clearfix"><dt>发票状态</dt><dd>'
        //待开具
        if(obj.status == 0){
            html+='<label><input type="radio" name="status" value="0" checked/><span>待开具</span></label>'
        }
        if(obj.style == 2){
            //电子发票
            html+='<label><input type="radio" name="status" value="1"'
            if(obj.status==1){
                html+='checked'
            }
          // var tip = obj.invoice_image?obj.invoice_image.match(/([a-zA-Z0-9_-])*.pdf$/g)[0]:'请上传文件'
            var tip = obj.invoice_image?obj.invoice_image.replace(/[^\\\/]*[\\\/]+/g,''):'请上传文件'
            html+='/><span>已开具</span></label></dd></dl>\
            <dl class="clearfix"><dt>上传发票</dt><dd>\
            <div id="upload"><input type="file" name="upfile" id="upfile" accept=".pdf"/><span class="grey tip">'+ tip +'</span></div>\
            <span class="grey">默认文件名称为数字或英文,pdf格式</span></dd></dl>'
        }else{
            //纸质发票
            html+='<label><input type="radio" name="status" value="2"'
            if(obj.status==2){
                html+='checked'
            }
            var express_no = obj.express_no?obj.express_no:''
            html+= '/><span>已邮寄</span></label></dd></dl>\
            <dl class="clearfix"><dt>物流单号</dt><dd><input type="text" name="expressNo" placeholder="寄出发票的物流单号" class="form" value="'+ express_no +'"/></dd></dl>'
        }
        html+='</div>'
        pop(html)
    }
    //查看弹框
    /** 
     * author 华亢 at 2018/07/24
     * params str
     * type string
     * toDo: 弹窗出现关闭
    */
    function pop(str){
        var t_index = layer.open({
            type: 1,
            title: '开票申请信息',
            closeBtn:true, 
            move: false,
            btn:['确定'],
            yes:function(index, e){
                var params = new FormData();
                params.append('id',$('#pop').attr('data-id'));
                params.delete('status')
                params.append('status',$('input[name="status"]:checked').val());
                params.delete('remark')
                params.append('remark',$('textarea[name="remark"]').val());

                if($('.sldSend').attr('data-style') == 2){
                    //电子发票 - 文件
                    params.append('invoiceImage',$('#upfile').get(0).files[0]);         
                    if($('input[name="status"]:checked').val() == 1 &&  typeof $('#upfile').get(0).files[0] == 'undefined'){
                        tipshow('请上传发票文件','warn');
                        return
                    }
                    if(typeof $('#upfile').get(0).files[0] != 'undefined' && $('input[name="status"]:checked').val() != 1){
                        tipshow('请勾选相应发票状态','warn');
                        return
                    }
                }else{
                    //普通发票 - 物流单号
                    params.append('expressId',$('input[name="expressNo"]').val());  
                    if($('input[name="status"]:checked').val()==2 && $('input[name="expressNo"]').val() == ''){
                        tipshow('请填写物流单号','warn');
                        return
                    }
                    if($('input[name="expressNo"]').val() != '' && $('input[name="status"]:checked').val() != 2){
                        tipshow('请勾选相应发票状态','warn');
                        return
                    }
                }
                updateInfo(params)
            },
            shadeClose:false,
            area: ['626px', '600px'],
            content:str
        });
        /*取消订单关闭按钮*/
        $("body").on("click",".layui-layer-setwin",function(e){
            closePop(e,t_index)
        });
        $("body").on("click",".renew-close",function(e){
            closePop(e,t_index)
        });
    }
    //关闭弹窗的函数
    function closePop(e,t_index){
        e.stopPropagation();
        if(t_index)
            layer.close(t_index);
    }
    /** 
     * author 华亢 at 2018/7/24
     * toDo: event 店家查看
    */
    $('body').on('click','.modify',function(e){
        e.stopPropagation();
        var id = $(this).attr('data-id')
        getOneDetail(id)
    })

    //list3: 更改发票信息
    function updateInfo(obj){
        $.ajax({
            url:'/staff/fee/invoice/update',
            data:obj,
            type:'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            success:function(res){
                if(res.errCode == 0){
                    tipshow('提交成功','info')
                    layer.closeAll();
                    location.reload()
                }else{
                    tipshow(res.errMsg,'warn')
                }
            }
        })
    }

    //搜索
    $('.search').click(function(e){
        e.stopPropagation();
        var params = {};
        params.requestNo = $('input[name="requestNo"]').val();
        getListConent(params)
    })
    $('#choose').change(function(e){
        e.stopPropagation()
        var params = {};
        params.status = $('#choose option:selected').val()
        getListConent(params)
    })
    $('body').on('change','#upfile',function(e){
        e.stopPropagation();
        var reg = /[^\\\/]*[\\\/]+/g; //匹配文件的名称和后缀的正则表达式
        var name = $(this).val().replace(reg, '');
        if(name){
            $('.tip').text(name)
        }else{
            tipshow('请确认文件名','warn')
        }
    })
})(window)