(
    function(window){
        // -------------------module start -------------------
        //所选发票
        var orderIds = [];
        //step1: 获取可开具发票列表
        $(window).on('load',function(){
            counldPrint()
        })
        function counldPrint(obj){
            obj = obj?obj:{isInvoice:0,status:1}
            $.ajax({
                url:'/merchants/fee/order/select/all',
                data:obj,
                type:'get',
                success:function(res){
                    if(res.errCode == 0){
                        renderList(res.data)
                    }
                }
            })
        }
        /** 
         * 分页
        */
        // (function(){
        //     var item = 1;
        //     $('body').on('click','.pageItem',function(e){
        //         e.stopPropagation();
        //         if($(this).attr('data-val') == 'pre'){
        //             if(item == 1){
        //                 item==1
        //             }else{
        //                 --item
        //             }
        //         }else if($(this).attr('data-val') == 'next'){
                    
        //         }else{
        //             item=$(this).attr('data-val')
        //         }
        //         var obj = {isInvoice:0,status:1,page:item,limit:5}
        //         counldPrint(obj)
        //     })
        // })()
        

        function renderList(arr){
            var html = '';
            if(arr.length>0){
                for(var i=0,l=arr.length;i<l;i++){
                    var create_time = arr[i].create_time.split(' ')[0]
                    html += '<ul class="clearfix">';
                    html += '<li>'+ create_time +'</li>'
                    html += '<li>'+ arr[i].widName +'</li>'
                    html += '<li>'+ arr[i].serviceVersion +'</li>'
                    html += '<li>'+ arr[i].serviceTime +'</li>'
                    html += '<li>'+ arr[i].pay_amount +'</li>'
                    html += '<li>'+ arr[i].payName +'</li>'
                    html += '<li><a href="javascript:;" class="pitch btn" data-price="'+ arr[i].pay_amount +'" data-id="'+ arr[i].id +'">开具发票</a></li></ul>'
                }
            }else{
                html = '<p class="noData">暂无数据</p>'
            }
            $('.t_body').html(html)
            /** 
             * 渲染分页
            */
            // var page = '<ul class="pagination"><li><a href="javascript:void(0);" data-val="pre" >&laquo;</a></li>';
            // for(var i=0,l=Math.ceil(arr.length/5);i<l;i++){
            //     page+='<li class="pageItem" data-val="'+ (i+1) +'"><a href="javascript:void(0);">'+ (i+1) +'</a></li>'
            // }
            // page+='<li><a href="javascript:void(0);" data-val="next">&raquo;</a></li></ul>';
            
            // $('.page').html(page)
            var t_layer = layer.open({
                type: 1,
                title: '请选择需要开票的服务',
                closeBtn:true,
                cancel: function(){
                    location.href = host+'merchants/capital/fee/invoiceList'
                },
                shadeClose:false,
                area: ['626px', '400px'],
                content: $('#pop')
            })
            //step2: 选择所需开具发票
            $('body').on('click','.pitch',function(e){
                e.stopPropagation();
                orderIds.push($(this).attr('data-id'))
                $('.price').text($(this).attr('data-price')+'元')
                layer.close(t_layer)
            })
        }

        //step3: 提交
        $('#createREQ').click(function(e){
            e.stopPropagation();
            var params = {};
            params.orderIds = JSON.stringify(orderIds);
            // stage1 专票?专票:普票
            params.type = $('input:radio[name="type"]:checked').val();
            // stage2 抬头
            params.title= $('input:text[name="companyName"]').val();
            params.amount = $('.price').text().replace('元','')
            if(!params.title){
                getRed($('input:text[name="companyName"]'),'请输入发票抬头')
                return;
            }
          //  if($('.companyBusiness').prop('checked')){
                params.taxNumber= $('input:text[name="taxNumber"]').val();
                if(!params.taxNumber){
                    getRed($('input:text[name="taxNumber"]'),'请输入纳税人识别号')
                    return;
                }
          //  }

            
            if(params.type == 2){
                //专票
                params.style = 1//纸质发票
                params.titleType=1//抬头类型
                params.companyTelephone = $('input:text[name="companyTel"]').val()
                sureSpecial(params)
                surePaper(params)
                if(params.depositBankAddress == '' || params.detailAddress == '' || !params.detailAddress || !params.depositBankAddress){
                    return false
                }
                updatePrint(params)
            }else{
                //普票
                params.style = $('input:radio[name="nature"]:checked').val();//发票性质
                params.titleType = $('input:radio[name="headType"]:checked').val()//抬头类型
                if( params.style == 1 ){
                    //纸质发票
                    surePaper(params)
                    if(params.detailAddress == '' || !params.detailAddress){
                        return false
                    }
                }
                updatePrint(params)
            }
        })
        function getRed(obj,tip){
            $(obj).focus().css({'border':'2px solid #FF4343'})
            tipshow(tip,'warn')
        }
        //纸质发票信息确认
        function surePaper(obj){
            obj.receiver = $('input:text[name="recceptName"]').val()
            if(!obj.receiver){
                getRed($('input:text[name="recceptName"]'),'请填写收件人信息')
                return false
            }
            obj.telephone = $('input:text[name="recceptTel"]').val()
            if(!obj.telephone){
                getRed($('input:text[name="recceptTel"]'),'请填写联系方式')
                return false
            }else if(!obj.telephone.match(/\d{11}/g)){
                getRed($('input:text[name="recceptTel"]'),'请填写正确联系方式')
                return false
            }
            //判断城市
            obj.provinceId = $('#member_province').val()
            if(!obj.provinceId){
                getRed($('#member_province'),'请填写省')
                return false
            }
            obj.cityId = $('#member_city').val()
            if(!obj.cityId){
                getRed($('#member_city'),'请填写市')
                return false
            }
            obj.areaId = $('#member_county').val()
            if(!obj.areaId){
                getRed($('#member_county'),'请填写区')
                return false
            }
            obj.detailAddress = $('input:text[name="recceptAddress"]').val()
            if(!obj.detailAddress){
                getRed($('input:text[name="recceptAddress"]'),'请填写详细地址')
                return false
            }
            return obj
        }

        function sureSpecial(obj){
            obj.companyAddress = $('input:text[name="companyAddress"]').val()
            if(!obj.companyAddress){
                getRed($('input:text[name="companyAddress"]'),'请填写公司地址信息')
                return false
            }
            obj.depositBankAccount = $('input:text[name="depositBill"]').val()
            if(!obj.depositBankAccount){
                getRed($('input:text[name="depositBill"]'),'请填写开户账号')
                return false
            }
            obj.depositBankAddress = $('input:text[name="depositAddress"]').val()
            if(!obj.depositBankAddress){
                getRed($('input:text[name="depositAddress"]'),'请填写开户行地址')
                return false
            }
            return obj
        }

        function updatePrint(obj){
            //obj.orderIds = []
            //type 类型
            //style 性质
            //titleType 抬头类型
            // title 抬头
            //companyAddress companyTelephone depositBankAccount depositBankAddress
            //receiver 收件人
            //telephone addressId收货地址
            //detailAddress 详细地址
            //taxNumber  
            $.ajax({
                url:'/merchants/fee/invoice/insert',
                data:obj,
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(res){
                    console.log(res)
                    if(res.errCode == 0){
                        tipshow('提交成功')
                        setTimeout(function(){
                            location.href=host + "merchants/capital/fee/invoiceList"
                        },1000)
                    }else{
                        tipshow(res.errMsg,'warn')
                    }
                }
            })
        }
        

        // 页面 填写逻辑
        /** 
         * 1. 默认选择专票 电子发票和个人抬头无法选择
         * 2. 普通发票 无需填写 div.special的文本框
        */
       specialShow(2)//默认专票
       $('.inv-type').click(function(e){
           e.stopPropagation();
           if ($(e.target).is("input")){
            return;
           }
           var is_special = $(this).children('input').val()
           if(is_special == 2){
            //专票
            specialShow(2)
           }else if(is_special == 1){
            //普票
            specialShow(1)
           }
       })
       function specialShow(value){
           if(value == 1){
               // 普票
               $('.special').css({'display':'none'})
               $('.inv-nature').eq(1).children('input').attr({'disabled':false})
               $('.inv-headType').eq(1).children('input').attr({'disabled':false})
           }else if(value == 2){
                //专票
                $('.special').css({'display':'block'});
                $('.inv-nature').eq(0).children('input').prop("checked", true)
                $('.inv-headType').eq(0).children('input').prop("checked", true)
                $('.inv-nature').eq(1).children('input').attr({'disabled':true})
                $('.inv-headType').eq(1).children('input').attr({'disabled':true})
                recover()
           }
        }
        /** 
         * 1. 点击电子发票时候 右边的表单无法填写
        */
       $('.inv-nature').click(function(e){
            e.stopPropagation();
            if ($(e.target).is("input")){
                return;
            }
            //1. 点击的是电子发票 && 且电子发票被选中
            var target = $(this).children('input')
            if($(target).val() == 2 && typeof $(target).attr('disabled') == 'undefined'){
                $('.consigneeDetail input,.consigneeDetail select').attr({'disabled':true}).css({'background':'#e6e6e6','border':'1px solid #e6e6e6'})
            }else{
                recover()
            }
       })

       function recover(){
            $('.consigneeDetail input,.consigneeDetail select').attr({'disabled':false}).css({'background':'white','border':'1px solid #e6e6e6'})
       }

       // ----------------三级联动-----------------------
        var county = "<option value=''>选择地区</option>";
        /*省市区三级联动*/
        $('.js-province').change(function(){
            var dataId = $('.js-province option:selected').val();
            var province = json[dataId];
            var city = "<option value=''>选择城市</option>";
            for(var i = 0;i < province.length;i ++){
                city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
            }
            $('.js-city').html(city);
            // $('.js-county').html(county);
        });
        $('.js-city').change(function(){
            var dataId = $('.js-city option:selected').val();
            var city = json[dataId];
            for(var i = 0;i < city.length;i ++){
                county += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
            }
            $('.js-county').html(county);
        });
       //------------module end ------------------
    }
)(window)