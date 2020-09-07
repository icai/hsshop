var isChange = false;
$(function () {
    $('#stockNum,.js-control-num').on('input',function(){
        this.value = this.value.replace(/[^\d]/g,'')
    })
    $('.js-buy-min').on('input',function(){
        if (this.value.length == 1) {
            this.value = this.value.replace(/[^1-9]/g,'')
        } else {
            this.value = this.value.replace(/[^\d]/g,'')
        }
    })
})
function isSubmitDistribute(yprice){
    // var setPrice = $('.s_price');
    // setPrice.val(yprice.price);
    // var changeEvent = document.createEvent ("HTMLEvents");
    // changeEvent.initEvent ("change", true, true);
    // setPrice[0].dispatchEvent (changeEvent);
    var appElement = document.querySelector('[ng-controller=myCtrl]');
    //获取$scope变量
    var $scope = angular.element(appElement).scope();
    //调用msg变量，并改变msg的值
    $scope.fxMobelId = yprice.id;
    $scope.goodsinfo.zero = yprice.zero;
    $scope.goodsinfo.cost = yprice.cost;
    $scope.goodsinfo.one = yprice.one;
    $scope.goodsinfo.sec = yprice.sec;
    $scope.goodsinfo.three = yprice.three;
    $scope.$apply();
    var $f_level = $('.f_level').eq(0);
    $f_level.html(yprice.title);
//  console.log($scope.goodsinfo.price);
    //上一行改变了msg的值，如果想同步到Angular控制器中，则需要调用$apply()方法即可
	layer.closeAll("iframe"); 
}
function GetQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}
//chosen  init selected method
function chose_mult_set_ini(select, values){
    if (values) {
        if(typeof values != "number" ){
            var arr = values.split(','); 
            var length = arr.length;
            var value = '';
            for(i=0;i<length;i++){
                value = arr[i]; 
                $(select+' option'+"[value='"+value+"']").attr('selected','selected');
            }
        }else{//选择单个尅性
             $(select+' option'+"[value='"+values+"']").attr('selected','selected');
        }
        $(select).chosen("destroy")
        $(select).chosen();
    }
}
//add by 魏冬冬 2018-6-28 angular 自定义指令 验证输入表单是否是正整数，用法标签上面添加属性 ensure-int 
app.directive('ensureInt',function() {
  return {
    require: 'ngModel',
    link: function(scope, ele, attrs, c) {
        $(ele).on('keyup',function(event){
            if(!/^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/.test($(ele).val())){
                $(ele).val('');
            }
        })
    }
  }
});
app.factory('fileReader', ["$q", "$log", function($q, $log){
    var onLoad = function(reader, deferred, scope) {
        return function () {
            scope.$apply(function () {
                deferred.resolve(reader.result);
            });
        };
    };
    var onError = function (reader, deferred, scope) {
        return function () {
            scope.$apply(function () {
                deferred.reject(reader.result);
            });
        };
    };

    var getReader = function(deferred, scope) {
        var reader = new FileReader();
        reader.onload = onLoad(reader, deferred, scope);
        reader.onerror = onError(reader, deferred, scope);
        return reader;
    };

    var readAsDataURL = function (file, scope) {
        var deferred = $q.defer();
        var reader = getReader(deferred, scope);         
        reader.readAsDataURL(file);
        return deferred.promise;
    };
    return {
        readAsDataUrl: readAsDataURL  
    };
}])
    app.controller('myCtrl', function($scope,$timeout,$http,fileReader,commonServer) {
        console.log(template);
        $scope.templateList = [];//商品页模板列表
        $scope.checkedTemplateId = "";//商品页模板列表选择
        $scope.productIntro = "";//商品简介
        $scope.editorImg = 0;//第三部富文本图片上传
        $scope.shop_url = store.url //店铺主页url;
        $scope.member_url = store.member_url;//会员主页URL
        $scope.group_url = '/shop/grouppurchase/index/' + store.id;
        $scope.community_url = '/shop/microforum/forum/index/'+ store.id;//微社区url 
        $scope.picNumber= true;
        setTimeout(function(){
            chose_mult_set_ini('.chzn-select',$scope.checkedTemplateId);
        },1000)
        $scope.chooseStep = [
            {title:'1.编辑基本信息',isactive:'active'},
            {title:'2.编辑商品详情',isactive:''}
        ]
        $scope._host=_host+'static/images/000.png';
        $scope.postData = {};
        $scope.step = 1;
        $scope.rowIndex = 0; //判断规格row的位置
        $scope.specImg = [];
        $scope.upSpecImg = "";
        $scope.imageSrc = null;
        //隐藏类目 Herry 20171218
        //$scope.shopKind = shopKind;
        $scope.error = false;
        // $scope.kindItem = []//存储分类数组
        $scope.submitted = false;//判断是否提交;
        $scope.postData.stock_show = 0;
//      $scope.postData.freight_id = 0;//初始化运费模板
        $scope.uploadShow = false; //判断上传可图片model显示
        $scope.eventKind = 1;
        $scope.editSku = {};//编辑规格字段
        // 基本信息设置
        $scope.addPic="+添加图片";//分享图片加图或者修改
        $scope.classShow=true;//分享图片显示样式
        $scope.baseinfo = {
            type: 1,//商品类型
            category_id:'',//商品类目
            buy_way:1,//购买方式1会搜，2链接到外部
            shopGrounp:[],//商品分组
            group_id:[],
            shopType:1,//商品类型
            presell_flag:0, //预售设置
            presell_delivery_type:1,//预售发货类型 1自定义发货时间，2付款成功后几天发货
            presell_delivery_time:'',//上面类型为1 时 的时间 yyyy-mm-dd
            presell_delivery_payafter:null,//上面类型为2 的 天数 
            type: 1,
            is_hexiao:0,//是否开启核销
            hexiao_start:'',//核销开启日期
            hexiao_end:'',//核销结束日期
            no_logistics:0,
            is_wholesale:0,//是否批发
            is_logistics:1,//是否物流
            //update: 华亢 at 2018/8/8 cam_id>0 -> is_card=1 ;cam_id=0 -> is_card=0
            is_card:0,// 是否是卡密商品
            cam_id:0// 卡密商品id
        }
        // 商品信息对象
        $scope.goodsinfo = {
            title:'',
            price:'',
            oprice:null,
            cost_price:'',
            buy:'',
            img:[],
            out_buy_link:'',
            stock:null,
            zero:template.zero,
            cost:template.cost,
            one:template.one,
            sec:template.sec,
            three:template.three,
            sold_num:0,
            is_price_negotiable:0,
            wholesale_array:[{"min":"","max":"","price":""}],
            negotiable_type:0,
            negotiable_value:''
        }
        $scope.negotiable = [
            {
                value:''
            },
            {
                value:''
            },
            {
                value:''
            },
        ]
        $scope.guigeSel = null;
        $scope.guilist = [];//商品库存规格
        // 运费设置
        $scope.setEms  = {
            freight_type:1,//运费类型1统一运费2，运费模板
            freight_price:0,//运费类型为1时的价格
            freight_id:[],//运费模板
            quota:null,//每人限购数量，0表示不限购
            buy_min: 1,//最小购买量，默认为1
            buy_permissions_flag:0,//0所有人，1指定会员级别
            buy_permissions_level:[],//会员卡等级数组
            buy_permissions_level_id:'',
            noteList:[],//留言数组
            sale_time_flag:1,//开售时间标准 1立即 2定时
            sale_time:null,//上面类型为 2 时填入 此字段
            is_discount:0, //0否 1是
            introduce:null, //商品详情
            isWeightTel: false,//重量运费模板
            weight:"",//商品重量
            is_point:0//默认开启
        }
        $scope.ngHide = 'hide';
        $scope.setFenxiao = {
        	fenxiao_flag:product.is_distribution
        }
        $scope.fxMobelId = template.id
        $scope.fxTitle = template.title
        $scope.fxVisible = {
        	fx_flag:true
        },
        $scope.grounps = [];
        //视频功能
        $scope.video = {
            checkedIndex:-1,//视频选中下标
            checkedItem:null,//视频选中对象
            groupingIndex:0,//分组下标
            groupList:[],//视频弹框分组
            groupingId:0,//分组id
            modeosearchTitle: "",//视频模态框搜索
            modelVideoList:[],//模态框视频列表
        }
        $scope.flag = false; //表单防重字段flag add by 魏冬冬2018-7-5
        var ue_category = initUeditor('category_editor');
        var timer = null;
        ue_category.addListener("selectionchange",function(){
            if(timer){
                clearTimeout(timer);
            }
            timer = $timeout(function(){
                angular.forEach($scope.editors,function(val,key){
                    if(val.type == 'shop_detail'){
                        $scope.$apply(function(){
                            $scope.editors[key].content = UE.getEditor("category_editor").getContent();
                        });
                    }
                })
            },500)
        });
        $scope.tcStyle = {
        	"left": 0,
        	"top": 0
        }
        $scope.tcSelStyle = {
        	"left": 0,
        	"top": 0,
        	"width": "245px"
        }
        $scope.discount_show = discount_show //折扣是否显示

        /**
         * 是否开启核销
         */
        $scope.$watch('baseinfo.is_hexiao',function(v,oldv,scope){
            if(v==1){
                // 2018-6-1 将物流和隐藏
                $scope.ngHide = 'hide';
                $scope.zitihide = 'hide';
                $('.ziti_tip').css('display','inline')
            }else{
                $scope.ngHide = 'hide';
                $scope.zitihide = 'show';
                $('.ziti_tip').css('display','none')
            }
        });
        /**add by 韩瑜 2018-7-19
         * 是否开启物流
         */
        $scope.$watch('baseinfo.is_logistics',function(v,oldv,scope){
            if(v==0){//选择无需物流
                $scope.zitihide = 'hide';
                $('.ziti_tip').css('display','inline')
            }else{//选择物流
            	$scope.zitihide = 'show';
            	$('.ziti_tip').css('display','none')
            }
        });
        //end
        //弹窗显示隐藏
//      $scope.contFlag = false;
        //获取弹窗dom
        $scope.changeTitleProver = angular.element('#changeTitleProver').width();
        //输入的规格值
        $scope.Guival = [];
        $scope.specs = [];//多规格商品库存等字段数据
        $scope.spkucun = false;
		$scope.guiCheckImg = false;
		$scope.guiSel = [];
		$scope.guiSelS = [];
		$scope.tcSelS = [];
		$scope.guiGroupindex = 0;
        $scope.selGuige = true;
        $scope.isNotEdit=true; //是否编辑规格 编辑状态不显示规格列表 add by 倪凯嘉 2019-1-17
        $scope.is_edit_prop_value=0;//0 编辑状态规格未修改  1 编辑状态规格已修改 add by 倪凯嘉 2019-1-17
		/*@author huoguanghui start*/
        $scope.host = imgUrl;
        //商品及商品分组弹框
        $scope.productModal = {
            list : [],//商品或分组列表
            navList : ["已上架商品","商品分组"],//商品或分组列表
            navIndex : 0,//商品弹框导航下标
            new : [//新建
                {
                    href: "/merchants/product/create",
                    title: "新建商品"
                },
                {
                    href: "/merchants/product/productGroup",
                    title: "新建分组"
                }
            ]
        };
        /*@author huoguanghui end*/
        
		$scope.selGuigeTip = function(){
			$scope.selGuige = !$scope.selGuige;
		}
        /**
         * 选择规格
         * @param  {[type]} title [description]
         * @param  {[type]} id       [要加入的id]
         * @param  {[type]} index   [数组索引值]
         */
        $scope.changOpt = function(title,id,index){ 
            if(id==""){ //新增元素
                $http.post('/merchants/product/addProp',{title:title}).success(function(res) {
                    if(res.status == 1){
                        id = res.data;
                        $scope.selectSku(title,id,index); 
                    }else{
                        tipshow(res.info,"warn");
                    } 
                });
            }else{
                $scope.selectSku(title,id,index);
            } 
        }
        /**
         * 选择规格
         * @param  {[type]} title [description]
         * @param  {[type]} id       [要加入的id]
         * @param  {[type]} index   [数组索引值]
         */
        $scope.selectSku = function(title,id,index){
            $scope.guiSelS[index].prop={};          
            $scope.guiSelS[index].values=[];          
            $scope.selGuige = false;
            for(var c=0;c<$scope.guiSelS.length;c++){
                if($scope.guiSelS[c]["prop"] && $scope.guiSelS[c]["prop"].id == id){
                    tipshow("规格名不能相同","warn");
                    return;
                }
            }
            $scope.guiSelS[index]["prop"].id = id;
            $scope.guiSelS[index]["prop"].title = title;
            $scope.guiSelS[index]["prop"].show_img = 0;//没有图片wei 0
            $scope.Guival[index] = [];
            isChange = true;
            $scope.guibg();
            var guiNoc = 0;
            for(var a=0;a<$scope.Guival.length;a++){
                if($scope.Guival[a].length == 0){
                    guiNoc++;
                }
            }
            if(guiNoc == $scope.Guival.length){
                $scope.spkucun = false;
            }
            var list = $scope.guiSel;
            for(var i=0;i<list.length;i++){
                list[i].isHide = false;
            }
        }

        // 获取运费模板
        $http.get('/merchants/product/getfreights').success(function(response) {
            for(var i=0;i<response.data.length;i++){//billing_type    重量模板字段为1
                $scope.setEms.freight_id.push({id:response.data[i]['id'],'title':response.data[i]['title'],'billing_type':response.data[i]['billing_type']});
            }
        });
        //获取模板列表
        $http.get('/merchants/product/getGoodsTemplates').success(function(response) {
            var defaultTpl = [{//默认存在两个模板
                id: -2,
                template_name: '简洁流畅版'
            },{
                id: -1,
                template_name: '普通版'
            }];
            for(var i = 0;i < defaultTpl.length;i ++){
                response.data.unshift(defaultTpl[i]);
            }
            $scope.templateList = response.data;
            if($scope.templateList.length > 0){
                $scope.checkedTemplateId = $scope.templateList[0].id;//选中的模板id值  默认为第一个
            }
            $timeout(function(){
                $(".procuctTemplate").chosen();
            },1000)
        })
       
        //      获取商品属性列表
		$http.get('/merchants/product/propList').success(function(response) { 
			$scope.guiSel = response.data; 
            for(var i=0;i<$scope.guiSel.length;i++){
                if(i==0){ 
                    $scope.guiSel[i].isActive = true;
                }else{
                    $scope.guiSel[i].isActive = false; 
                }
            }
		})
		//      根据属性获取属性值列表
//		$http.get('/merchants/product/propValues/1').success(function(response) {
//			console.log(response);
//		})
//      输入规格
//		$scope.selGuigeVal = function(e,selVal,$index){
//			console.log($scope.guiSel);
//			var keycode = window.event?e.keyCode:e.which;
//			if(keycode == 13){
//				$scope.selGuige = false;
//				console.log($scope.selGuige);
//				for(var c=0;c<$scope.guiSel.length;c++){
//	        		if($scope.guiSel[c].guiSelVal == selVal){
//	        			tipshow("规格名不能相同","warn");
////	        			$scope.guibg();
//	        			return;
//	        		}
//	        	}
////				$scope.guiSel.push({});
//      		$scope.guiSel[$index].guiSelVal = selVal;
//      		$scope.Guival[$index] = [];
//	            $scope.guibg();
//	            var guiNov = 0
//	            for(var a=0;a<$scope.Guival.length;a++){
//	        		if($scope.Guival[a].length == 0){
//	        			guiNov++;
//	        		}
//	        	}
//	        	if(guiNov == $scope.Guival.length){
//	        		$scope.spkucun = false;
//	        	}
//			}
//		}
        /**
         * @author: 魏冬冬（zbf5279@dingtalk.com）
         * @description: 
         * @param {type} 
         * @return: 
         * @Date: 2019-05-23 15:15:10
         * @update 文字限制18改成30 update by 魏冬冬 2019-5-23
         */
        function addAttribute(content,id){
            if (content.length > 30) {
                tipshow("规格名称字数不能超过30个字，请重新输入","warn");
                return false
            }
            var guiindex = $scope.guiIndex;
            var gValind = 0;
            for(var f=0;f<$scope.Guigeval.length;f++){
                if($scope.Guigeval[f].title == content){
                    gValind++;
                }
            }
            for(var g=0;g<$scope.Guival[$scope.guiIndex].length;g++){
                if($scope.Guival[$scope.guiIndex][g].title == content){
                    gValind++;
                }
            }
            if(gValind == 0){
                $scope.Guigeval.push({val:content,sel:$scope.guiSelS[guiindex].title,id:id});
                $scope.Guiarr.push({title:content,sel:$scope.guiSelS[guiindex].title,id:id});
            }else{
                tipshow("已经添加了相同的规格值","warn");
            }
           
        }  
        /*
        * 添加批发价
        */
       $scope.addWholesale = function(){
            $scope.goodsinfo.wholesale_array.push({})
       }
        /*
        * 删除批发价
        */
       $scope.deleteWholesale = function($index){
            $scope.goodsinfo.wholesale_array.splice($index,1)
       }

		//      选择属性值
		$scope.addContList = function(item,tit){
            //update by 倪凯嘉 2019-1-27
            if($scope.isNotEdit==false){
                $scope.inpGuigeval = tit;
                $scope.tcSelStyle.display = "none";
                return false;
            }
            //end
            addAttribute(tit,item.id);
			$scope.inpGuigeval = "";
			$scope.tcSelStyle.display = "none";
		}
//      商品库存表格价格值变化
		$scope.jiageC = function(jiage){
            if($scope.specs.length){
                var arr = [];
                angular.forEach($scope.specs,function(val,key){
                    if(val.price !==undefined){
                        arr.push(val.price);
                    }
                })  
                var min = Math.min.apply(Math, arr);
                $scope.goodsinfo.price = min.toFixed(2);
                // alert(min);
            }
		}
        //商品总库存
        $scope.tatolInvebtory = function(invebtory){
            var all = 0;
            for (var a = 0; a < $scope.specs.length; a++){ 
                if($scope.specs[a].stock_num){
                    all += parseInt($scope.specs[a].stock_num); 
                }
            }
            $scope.goodsinfo.stock = all;
        }
        /**
         author wdd
         desc 有规格时候计算总销量 
        **/
        $scope.tatolSellCount = function(){
            var all = 0;
            for (var a = 0; a < $scope.specs.length; a++){ 
                if($scope.specs[a].sold_num){
                    all += parseInt($scope.specs[a].sold_num); 
                }
            }
            $scope.goodsinfo.sold_num = all;
        }
        /**
         * author wdd
         * desc 修改商品价格改变商品原价1.2倍 
        **/
        $scope.changePrice = function(){
            if($scope.goodsinfo['price'] == undefined){
                $scope.goodsinfo['oprice'] = 0;
                return   
            }
            $scope.goodsinfo['oprice'] = ($scope.goodsinfo['price'] * 1.2).toFixed(2);
        }
        //添加按钮点击事件弹窗
        $scope.addCont = function($event,$index){
        	$scope.tcStyle.left = $event.target.offsetLeft - $scope.changeTitleProver / 2 + 8;
        	$scope.tcStyle.top = $event.target.offsetTop + 25;
        	$scope.tcStyle.display = "block";
        	$scope.guiIndex = $index;
        	$scope.Guiarr = [];
        	$scope.Guigeval = [];
        }

        //添加按钮弹窗input点击事件
		$scope.addContInp = function($event){
			$http.get('/merchants/product/propValues/'+$scope.guiSelS[$scope.guiIndex].prop.id).success(function(response) {
				$scope.tcSelS = response.data;
				$scope.guigeSearch = angular.element('#guigeSearch').height();
				$scope.tcSelStyle.left = $scope.tcStyle.left + 20;
	        	$scope.tcSelStyle.top = $scope.tcStyle.top + 10 + $scope.guigeSearch;
	        	$scope.tcSelStyle.display = "block";
			})
		}
		$scope.guigecancle = function($index){
			$scope.Guigeval.splice($index,1); 
			$scope.Guiarr.splice($index,1); 
		}
		//添加规格
        $scope.addRows1 = function($event){  
            $event.stopPropagation(); //阻止事件冒泡
            $scope.hideAllModel($event);
            $scope.guiSelS.push({});
            $scope.Guival.push({'selGuige':true}); 
            setTimeout(function(){
                $("input[name='sku_input']").eq($scope.Guival.length-1).focus();
            },50)
            //设置选中规格 
            $scope.initSkuData($scope.guiSel);
        }
        /**
         * 初始化 规格数据 用于规格选择框组件
         * @param {array} data 要初始化的规格数据
         * @return {array} 处理后的结果
         */
        $scope.initSkuData = function(data){
            for(var i=0;i<data.length;i++){
                if(data[i].isRemove){
                    data.splice(i,1);
                    i--;
                    continue;
                }
                data[i].isHide = false;
                if(i==0)
                    data[i].isActive = true;
                else
                    data[i].isActive = false;
            }
            return data;
        }

		// 规格移除一行
        $scope.removeRows = function($index){
            $scope.specImg.splice($index,1);
            $scope.guilist.splice($index,1);
            $scope.Guival.splice($index,1);
            $scope.guiSelS.splice($index,1);
            $scope.guibg();
            if($scope.Guival.length == 0){
            	$scope.spkucun = false;
            }
        }
        //删除规格值
        $scope.removeAtom = function($index,guiGroupindex){
            $scope.Guival[guiGroupindex].splice($index,1);
            isChange = true;
            $scope.guibg();
        	var guiNo = 0;
        	for(var a=0;a<$scope.Guival.length;a++){
        		if($scope.Guival[a].length == 0){
        			guiNo++;
        		}
        	}
        	if(guiNo == $scope.Guival.length){
        		$scope.spkucun = false;
        	}
        }
		/**
         * body点击关闭弹窗并初始化数据 
         */
        $scope.hideAllModel = function($event){
            var e = $event || window.event;
            if($scope.Guival.length && e.target.name !='sku_input'){
                angular.forEach($scope.Guival,function(val,key){
                    if(val.selGuige){
                        val.selGuige = false;
                    }
                })
                var list = $scope.guiSel;
                for(var i=0;i<list.length;i++){
                    if(list[i].isRemove){
                        list.splice(i,1);
                        i--;
                        continue;
                    }
                    list[i].isHide = false;
                }
                $("input[name='sku_input']").val('');
            }
        } 
        //显示下拉规格
        $scope.showGuiDialog = function(item,$event,index){
            if($scope._id == 1){
                return false
            }
            $event.stopPropagation();
            $scope.hideAllModel($event);
            if(!item.selGuige){
                item.selGuige = true;
            }
            $scope.tcSelStyle.display = "none";
            setTimeout(function(){
                $("input[name='sku_input']").eq(index).focus();
            },50);
            var kk =0;
            for(var i=0;i<$scope.guiSel.length;i++){ 
                if(typeof $scope.guiSelS[index].prop !=='undefined'){
                    if($scope.guiSel[i].id == $scope.guiSelS[index].prop.id){
                        $scope.guiSel[i].isActive = true;
                        if(i>5){
                            kk = i;
                        } 
                    }else{
                        $scope.guiSel[i].isActive = false;
                    }
                }else{
                    if(i==0){
                        $scope.guiSel[i].isActive = true;
                    }else{
                        $scope.guiSel[i].isActive = false;
                    }
                }
            }
            if(kk>0){
                setTimeout(function(){ 
                    var pul = $(".select2-results").eq(index); 
                    var top = pul.scrollTop()+23*(kk-5); 
                    pul.scrollTop(top); 
                },50)
            }

        }
        /**
         * 规格选择组件 设置鼠标移动效果
         */
        $scope.setIsActive = function(index){ 
            var k=0;
            for(var i=0;i<$scope.guiSel.length;i++){
                k=$scope.guiSel[i].isActive?k++:k; 
            }  
            for(var i=0;i<$scope.guiSel.length;i++){
                if(index == i){
                    if(k!=1)
                        $scope.guiSel[i].isActive = true;
                }else{
                    $scope.guiSel[i].isActive = false;
                } 
            }
        }
        /**
         * 规格选择组件的搜索框输入事件
         * 检索功能实现
         */
        $scope.searchKeyup = function(e,index){ 
            var code = e.keyCode; //ASCII码
            if(code !== 40 && code != 38 && code != 13){
                var value = e.target.value;
                var list = $scope.guiSel; 
                if(value !=''){ //值不等于空才执行检索功能 
                    var isAdd = true; //是否需要插入新对象
                    var num = 0; //只能一个被选中
                    for(var i=0;i<list.length;i++){
                        if(list[i].isRemove){//移除新插入的对象
                            list.splice(i,1);
                            i--; 
                            continue;
                        }
                        if(list[i].title.indexOf(value)>=0){
                            list[i].isHide = false;
                            if(num==0){
                                list[i].isActive = true;
                                num++;
                            }
                            if(list[i].title == value){
                                isAdd = false;
                            }
                        }else{
                            list[i].isHide = true;
                        }
                    }
                    if(isAdd){
                        for(var i=0;i<list.length;i++){ //出现新增对象 取消所有已有的数据选中状态
                            list[i].isActive = false;
                        }
                        var obj = {
                            title:value,
                            id:'',
                            isHide:false,
                            isRemove:true, //是否需要移除,检索时出现数据库未保存的字段自动时使用
                            isActive:true,
                        }
                        list.splice(0,0,obj); 
                    }
                }else{
                    for(var i=0;i<list.length;i++){
                        if(list[i].isRemove){//移除新插入的对象
                            list.splice(i,1);
                            i--; 
                            continue;
                        }
                        list[i].isHide = false;
                        if(typeof $scope.guiSelS[index].prop !=='undefined'){
                            if($scope.guiSelS[index].prop.id == list[i].id){
                                list[i].isActive = true;
                            }
                        }else{
                            list[0].isActive = true;
                        }
                    } 
                } 
            } 
        }

        /**
         * 处理guiSel 将数组下对象中含有 isRemove 
         */
        $scope.clGuiSel = function(){
            var res = [];
            for(var i=0;i<$scope.guiSel.length;i++){
                if(!$scope.guiSel[i].isHide){
                    res.push($scope.guiSel[i]);
                }
            }
            return res;
        }

        /**
         * 规格选择组件的搜索框键盘按下事件处理方法
         */
        $scope.searchKeydown = function(e,index){
            var code = e.keyCode; //ASCII码
            var list =$scope.clGuiSel();
            switch(code){
                case 38://上箭头 
                    e.preventDefault();
                    for(var i=0;i<list.length;i++){
                        if(list[i].isActive) {
                            if(typeof list[i-1] !=='undefined'){
                                list[i].isActive = false;
                                list[i-1].isActive = true;
                                var pul = $(".select2-results").eq(index); 
                                pul.scrollTop(pul.scrollTop()-23);
                                break; 
                            }
                        }   
                    }
                    break;
                case 40: //下箭头
                    e.preventDefault();
                    for(var i=0;i<list.length;i++){
                        if(list[i].isActive) {
                            if(typeof list[i+1] !=='undefined'){
                                list[i].isActive = false;
                                list[i+1].isActive = true; 
                                if(i>5){
                                    var pul = $(".select2-results").eq(index); 
                                    pul.scrollTop(pul.scrollTop()+23);
                                }  
                                break;
                            }
                        }   
                    }
                    break;
                case 13: //回车键
                    e.preventDefault();
                    var obj = {};
                    for(var i=0;i<list.length;i++){
                        if(list[i].isActive) {
                            obj =list[i];
                        }   
                    }
                    $scope.changOpt(obj.title,obj.id,index);
                    break;
            }  
        }

        //弹窗确定按钮点击事件
        $scope.tc_sure = function(){
            if($scope.inpGuigeval){
                $http.post('/merchants/product/addPropValue',{title:$scope.inpGuigeval}).success(function(response) {
                    if(response.status == 1){
                        addAttribute($scope.inpGuigeval,response.data); 
                        dataParsing();
                        console.log(1111111);
                        if($scope.goodsinfo.is_price_negotiable == 1){
                            $timeout(function(){
                                angular.element('.pNegotiable').attr('disabled',true);
                                console.log(2222);
                            })
                        }
                    }else{
                        tipshow("添加失败","warn");
                    }
                    $scope.inpGuigeval="";//清除input内容
                });
                console.log(1111111)
            }else{
                dataParsing();
            }
            function dataParsing(){
                for(var i=0;i<$scope.Guiarr.length;i++){
                    var repeat = false;
                    if($scope.Guival[$scope.guiIndex].length){
                        angular.forEach($scope.Guival[$scope.guiIndex],function(val,key){
                            if(val.id == $scope.Guiarr[i]['id']){
                                repeat = true;
                            }
                        })
                    }
                    if(repeat){
                        tipshow("已经添加了相同的规格值","warn");
                        return;
                    }
                    $scope.Guiarr[i].parent = $scope.guiSelS[$scope.guiIndex].prop;//获取父级的值
                    //update by 倪凯嘉 2019-1-27
                    if($scope.isNotEdit){//添加
                        $scope.Guival[$scope.guiIndex].push($scope.Guiarr[i]);
                    }else{//编辑
                        if(productId){
                            $scope.is_edit_prop_value=1
                        }
                        $scope.Guival[$scope.guiIndex][$scope.guiItemIndex]=$scope.Guiarr[i];
                    }
                    //end
                    $scope.spkucun = true;
                    $scope.goodsinfo.price = 0;
                    isChange = true;
                    $scope.guibg();
                }
                $scope.isNotEdit=true; //add by 倪凯嘉 2019-1-27
            }
        	$scope.tcStyle.display = "none";
            $scope.tcSelStyle.display = "none";
        }
        
        //编辑规格 add by 倪凯嘉 2019-1-27
        $scope.editCont=function($event,index,item,key){
            $scope.inpGuigeval=item.title;
            $scope.isNotEdit=false;
            $scope.guiIndex = key;
            $scope.guiItemIndex = index;
            $scope.Guigeval = [];
            $scope.Guiarr = [];
            $scope.tcStyle.left = $event.target.offsetParent.offsetLeft - 160;
            $scope.tcStyle.top = $event.target.offsetParent.offsetTop + 70;
            $scope.tcStyle.display = "block";
            $scope.tcSelStyle.display = "none";
        },
        //规格库存表格
        $scope.guibg = function(){
        	var arr0 = $scope.Guival[0];
        	var arr1 = $scope.Guival[1];
        	var arr2 = $scope.Guival[2]; 
        	$scope.specs = [];
			if(arr0 && arr0.length > 0){ 
        		var obj ={};
				for(var i=0;i<arr0.length;i++){ 
	        		if(arr1 && arr1.length > 0){
	        			for(var j=0;j<arr1.length;j++){
	        				if(arr2 && arr2.length > 0){
	        					for(var k=0;k<arr2.length;k++){
                                    obj.sold_num = 0;//销量
                                    obj.k1_id = arr0[i].parent.id; //一级id
                                    obj.k1 = arr0[i].parent.title; //一级title
                                    obj.v1_id = arr0[i].id; //一级属性id
                                    obj.v1 = arr0[i].title; //一级属性title
                                    obj.k2_id = arr1[j].parent.id; //二级id
                                    obj.k2 = arr1[j].parent.title; //二级title
                                    obj.v2_id = arr1[j].id; //二级属性id
                                    obj.v2 = arr1[j].title; //二级属性title
                                    obj.k3_id = arr2[k].parent.id; //三级id
                                    obj.k3 = arr2[k].parent.title; //三级title
                                    obj.v3_id = arr2[k].id; //三级属性id
                                    obj.v3 = arr2[k].title; //三级属性title
                                    obj.spec0 = arr0[i].title;
		        					obj.spec1 = arr1[j].title;
		        					obj.rowspan0 = arr1.length * arr2.length;
		        					obj.is_show =  k>0||j>0?false:true; 
		        					obj.is_show1 = k>0?false:true; 
		        					obj.spec2 = arr2[k].title;
		        					obj.rowspan1 = arr2.length;
	        						$scope.specs.push(obj);  
		        					obj ={};
	        					}
	        				}else{
                                obj.sold_num = 0;//销量
                                obj.k1_id = arr0[i].parent.id; //一级id
                                obj.k1 = arr0[i].parent.title; //一级title
                                obj.v1_id = arr0[i].id; //一级属性id
                                obj.v1 = arr0[i].title; //一级属性title
                                obj.k2_id = arr1[j].parent.id; //二级id
                                obj.k2 = arr1[j].parent.title; //二级title
                                obj.v2_id = arr1[j].id; //二级属性id
                                obj.v2 = arr1[j].title; //二级属性title
	        					obj.spec0 = arr0[i].title;  
	        					obj.spec1 = arr1[j].title; 
	        					obj.is_show = j > 0 ? false : true; 
	        					obj.is_show1 = true;
	        					obj.rowspan0 = arr1.length;
	        					$scope.specs.push(obj);  
	        					obj ={};
	        				}
	        			}
	        		}else{
	        			if(arr2 && arr2.length > 0){
	        				for(var k=0;k<arr2.length;k++){
                                obj.sold_num = 0;//销量
                                obj.k1_id = arr0[i].parent.id; //一级id
                                obj.k1 = arr0[i].parent.title; //一级title
                                obj.v1_id = arr0[i].id; //一级属性id
                                obj.v1 = arr0[i].title; //一级属性title
                                obj.k3_id = arr2[k].parent.id; //三级id
                                obj.k3 = arr2[k].parent.title; //三级title
                                obj.v3_id = arr2[k].id; //三级属性id
                                obj.v3 = arr2[k].title; //三级属性title
	        					obj.spec0 = arr0[i].title;
		        				obj.spec2 = arr2[k].title;
                                obj.rowspan0 = arr2.length;
                                obj.is_show = k>0||j>0?false:true;
		        				$scope.specs.push(obj);  
			        			obj ={};
	        				}
	        			}else{
                            obj.sold_num = 0;//销量
                            obj.k1_id = arr0[i].parent.id; //一级id
                            obj.k1 = arr0[i].parent.title; //一级title
                            obj.v1_id = arr0[i].id; //一级属性id
                            obj.v1 = arr0[i].title; //一级属性title
	        				obj.spec0 = arr0[i].title;
		        			obj.is_show = true;
	    					$scope.specs.push(obj);  
		        			obj ={};
	        			}
	        			
	        		} 
	        	}
			}else{
				var obj ={};
				if(arr1 && arr1.length > 0){
        			for(var j=0;j<arr1.length;j++){
        				if(arr2 && arr2.length > 0){
        					for(var k=0;k<arr2.length;k++){
                                obj.sold_num = 0;//销量
                                obj.k2_id = arr1[j].parent.id; //二级id
                                obj.k2 = arr1[j].parent.title; //二级title
                                obj.v2_id = arr1[j].id; //二级属性id
                                obj.v2 = arr1[j].title; //二级属性title
                                obj.k3_id = arr2[k].parent.id; //三级id
                                obj.k3 = arr2[k].parent.title; //三级title
                                obj.v3_id = arr2[k].id; //三级属性id
                                obj.v3 = arr2[k].title; //三级属性title
	        					obj.spec1 = arr1[j].title;
	        					obj.spec2 = arr2[k].title;
                                obj.is_show1 = k>0?false:true; 
	        					obj.rowspan1 = arr2.length;
        						$scope.specs.push(obj);  
	        					obj ={};
        					}
        				}else{
                            obj.sold_num = 0;//销量
                            obj.k2_id = arr1[j].parent.id; //二级id
                            obj.k2 = arr1[j].parent.title; //二级title
                            obj.v2_id = arr1[j].id; //二级属性id
                            obj.v2 = arr1[j].title; //二级属性title
        					obj.spec1 = arr1[j].title; 
        					obj.is_show = false; 
        					obj.is_show1 = true;
        					$scope.specs.push(obj);  
        					obj ={};
        				}
        			}
        		}else{
        			if(arr2 && arr2.length > 0){
                        obj.sold_num = 0;//销量
        				for(var k=0;k<arr2.length;k++){
                            obj.k3_id = arr2[k].parent.id; //三级id
                            obj.k3 = arr2[k].parent.title; //三级title
                            obj.v3_id = arr2[k].id; //三级属性id
                            obj.v3 = arr2[k].title; //三级属性title
	        				obj.spec2 = arr2[k].title;
	        				$scope.specs.push(obj);  
		        			obj ={};
        				}
        			}
        		}
            }
        }
        
//      弹窗取消按钮点击事件
        $scope.tc_cancle = function(){
            $scope.isNotEdit=true; //add by 倪凯嘉 2019-1-27
        	$scope.tcStyle.display = "none";
        	$scope.tcSelStyle.display = "none";
        }
        //规格批量设置价格
        $scope.stockPricePosition = 1;//1为价格2为库存
        $scope.changeShopPrice = function(){
            $scope.stockPricePosition = 1;
            $('.js-batch-form input').val('');
            $('.js-batch-type').css('display','none');
            $('.js-batch-form').css('display','inline');
        }
        //规格批量设置库存
        $scope.changeStock = function(){
            $scope.stockPricePosition = 2;
            $('.js-batch-form input').val('');
            $('.js-batch-form input').attr('placeholder','请输入库存');
            $('.js-batch-type').css('display','none');
            $('.js-batch-form').css('display','inline');
        }
        //规格批量设置销量
        $scope.changeSales = function(){
        	$scope.stockPricePosition = 3;
        	$('.js-batch-form input').val('');
            $('.js-batch-form input').attr('placeholder','请输入销量');
            $('.js-batch-type').css('display','none');
            $('.js-batch-form').css('display','inline');
        }
        //批量设置保存
        $scope.savePriceStock = function(){
            var price = $('.js-batch-form input').val();
            if(price == ''){
                tipshow('价格不能为空','warn');
                return;
            }
            if($scope.stockPricePosition == 1){
                angular.forEach($scope.specs,function(val,key){
                    val.price = parseFloat(price).toFixed(2);
                })
                $scope.goodsinfo.price = parseFloat(price).toFixed(2);
            }
            if($scope.stockPricePosition == 2){
                angular.forEach($scope.specs,function(val,key){
                    val.stock_num = parseFloat(price);
                })
            }
            if($scope.stockPricePosition == 3){
                angular.forEach($scope.specs,function(val,key){
                    val.sold_num = parseFloat(price).toFixed();
                })
            }
            $('.js-batch-type').css('display','inline');
            $('.js-batch-form').css('display','none');
        }
        //批量设置取消
        $scope.cancelPriceStock = function(){
            $('.js-batch-type').css('display','inline');
            $('.js-batch-form').css('display','none');
        }
        $scope.fxVf = function(){
            if(product.length == 0){
        		if(template == ""){
	        		$scope.fxVisible.fx_flag = false;
	        		$scope.setFenxiao.fenxiao_flag = 0;
	        	}else{
	        		$scope.fxVisible.fx_flag = true;
	        		$scope.setFenxiao.fenxiao_flag = 0;
	        	}
        	}else{
        		if(template == ""){
	        		$scope.fxVisible.fx_flag = false;
	        	}else{
	        		$scope.fxVisible.fx_flag = true;
	        	}
        	}
        }
        $scope.fxVf()
        $scope.safeApply = function(fn) {
            var phase = this.$root.$$phase;
            if (phase == '$apply' || phase == '$digest') {
                if (fn && (typeof(fn) === 'function')) {
                    fn();
                }
            } else {
                this.$apply(fn);
            }
        };
        $scope.fenxiao = function(fn){
        	layer.open({
		        type: 2,
		        title: false, 
		        closeBtn:false,
		        skin:"layer-tskin", //自定义layer皮肤 
		        shade: 0.8,
		        area: ['655px', '525px'],
		        content: '/merchants/distribute/choice?fn=isSubmitDistribute'
		    });
        };
        
        // //获取分类列表
        // $http.get('/merchants/product/getallcate').success(function(response) {
        //    // 第一步分类选择
        //     if (response.status && response.data.length) {
        //         for(var i=0;i<response.data.length;i++){
        //             $scope.kindItem.push({title:response.data[i]['category_name'],isactive:'',category_id:response.data[i]['id'],subsub_cate:response.data[i]['sub']});
        //             // console.log($scope.kindItem)
        //         }
        //     }
        //     //console.log($scope.kindItem)
        // });
        // 获取商品分组列表
        function queryGroup(){
            $http.get('/merchants/product/getallgroup').success(function(response) { 
                if (response.status && response.data.length) {
                    for(var i=0;i<response.data.length;i++){
                        $scope.baseinfo.shopGrounp.push({title:response.data[i]['title'],group_id:response.data[i]['id']});
                    }
                }

                $timeout(function(){
                    $('.chosen_select').chosen();
                    $(".choos_product_group").hide();
                },300)
            });
        }
        queryGroup();
        // 刷新商品分组
        var flag = false//标识有没有点击刷新
        $scope.refresh = function(){
            $scope.baseinfo.shopGrounp = [];
            if(flag) return;
            flag = true;
            $http.get('/merchants/product/getallgroup').success(function(response) {
                if (response.status && response.data.length) {
                    flag = false;
                    for(var i=0;i<response.data.length;i++){
                        $scope.safeApply(function(){
                            $scope.baseinfo.shopGrounp.push({title:response.data[i]['title'],group_id:response.data[i]['id']});
                        })
                    }
                }
                
                $timeout(function(){
                    $(".chosen_select").chosen("destroy");
                    $(".chosen_select").chosen();
                },1000)
            });
        }

        
        $scope.reloadTel = function(){
            $http.get('/merchants/product/getGoodsTemplates').success(function(response) {
                $scope.templateList = response.data;
                $timeout(function(){
                    $(".procuctTemplate").chosen("destroy");
                    $(".procuctTemplate").chosen();
                },1000)
            })
        }
        // 获取会员等级
        $http.get('/merchants/product/getmembercards').success(function(response) {
            for(var i=0;i<response.data.length;i++){
                $scope.setEms.buy_permissions_level.push({id:response.data[i]['id'],'title':response.data[i]['title']});
            }
            $timeout(function(){
                $('#member_level').chosen();
            },1000)
        });
        // 初始化日期
        laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
        laydate({
            elem: '#datetime',
            format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
            festival: true, //显示节日
            choose: function(datas){ //选择日期完毕的回调
                // alert('得到：'+datas);
                $scope.$apply(function(){
                    $scope.baseinfo.presell_delivery_time = datas;
                })
            }
        });
        laydate({
            elem: '#datetimepicker',
            format: 'YYYY-MM-DD hh:mm:ss', // 分隔符可以任意定义，该例子表示只显示年月
            istime: true, //必须填入时间 
            min: laydate.now(0, 'YYYY-MM-DD'), //设定最小日期为当前日期
            festival: true, //显示节日
            choose: function(datas){ //选择日期完毕的回调
                // alert('得到：'+datas);
                console.log()
                if(new Date(datas).getTime()<new Date().getTime()){
                    $scope.$apply(function(){
                        $scope.setEms['sale_time'] = laydate.now(0, 'YYYY-MM-DD hh:mm:ss');
                    })
                }else{
                    $scope.$apply(function(){
                        $scope.setEms['sale_time'] = datas;
                    })
                }
                
            }
        })
        // 核销时间设置
        var hexiao_start = {
            elem: '#hexiao_start',
            format: 'YYYY-MM-DD',
            min: laydate.now(0, 'YYYY-MM-DD'), //设定最小日期为当前日期
            max: '2099-06-16 23:59:59', //最大日期
            istime: false,
            isclear: false,
            istoday: false,
            choose: function(datas){
                var data = new Date(datas);
                var data = data.getTime();
                data = data + 24*60*60*1000;
                data = new Date(data);
                var year = data.getFullYear(); // 获取完整的年份(4位,1970)
                var month = data.getMonth() + 1; // 获取月份(0-11,0代表1月,用的时候记得加上1)
                var date = data.getDate(); // 获取日(1-31)
                if(month < 9){
                    month = '0' + month 
                }
                if(date < 9){
                    date = '0' + date 
                }
                hexiao_end.min = year + '-' + month + '-'+ date; //开始日选好后，重置结束日的最小日期
                hexiao_end.start = datas //将结束日的初始值设定为开始日
                $scope.$apply(function(){
                    $scope.baseinfo.hexiao_start = datas;
                })
            } 
        }
        var hexiao_end = {
            elem: '#hexiao_end',
            format: 'YYYY-MM-DD',
            min: laydate.now(1, 'YYYY-MM-DD'), //设定最小日期为当前日期
            max: '2099-06-16 23:59:59',
            istime: false,
            isclear: false, 
            istoday: false,
            choose: function(datas){
                hexiao_start.max = datas; //结束日选好后，重置开始日的最大日期
                $scope.$apply(function(){
                    $scope.baseinfo.hexiao_end = datas;
                })
            }
        }
        laydate(hexiao_start)
        laydate(hexiao_end)
        // 当没有选中分类的时候选择分类
        $scope.choose_kind = function(){
            $scope.step = 1;
            angular.forEach($scope.chooseStep,function(val,key){
                val.isactive = '';
                if(key==0){
                    val.isactive = 'active';
                }
            })
        }
        //分销核销判断 @author huoguanghui
        $scope.judgeHexiao= function(){ 
            if($scope.baseinfo.is_hexiao == 1){//若开启核销，分销不能设置
                tipshow("若要开启分销，请先关闭自提","warn");
                $scope.setFenxiao['fenxiao_flag'] = 0;
            }
        }
        $scope.judgeFenxiao= function(){ 
            if($scope.setFenxiao['fenxiao_flag'] == 1){//若开启核销，分销不能设置
                tipshow("若要开启自提，请先关闭分销","warn");
                $scope.baseinfo.is_hexiao = 0;
            }
            $scope.baseinfo.is_logistics = 0;
        }
        /* 
         * add by 韩瑜 2018-7-19
         * 物流核销判断
         */
        $scope.clickwuliu= function(){ 
            if($scope.baseinfo.is_hexiao == 1){//若开启核销，不能设置物流
                tipshow("请先关闭自提哦","warn");
                $scope.baseinfo.is_logistics = 0;
            }
        }
        $scope.wuxuwuliu= function(){ 
            if($scope.baseinfo.is_hexiao == 0){//若未开启核销，物流不能设置
                tipshow("请先开启自提哦","warn");
                $scope.baseinfo.is_logistics = 1;
            }
        }
        $scope.wuliuhexiao= function(){ 
            $scope.baseinfo.is_logistics = 1;
        }
        //end
        //价格面议
        /*update by 邓钊 2018-7-13 价格面议开关*/
        /*
        * @auther 邓钊
        * @desc 开启价格面议
        * @date 2018-7-13
        * */
        $scope.priceNegotiable=function($event){
            $scope.goodsinfo.is_price_negotiable = 1;
        		$('.pNegotiable').attr('disabled',true);
                $('.s_price').attr('disabled',true);
                if($scope.goodsinfo['price'] == '' || $scope.goodsinfo['price'] == undefined){
                    $scope.goodsinfo['price'] = 0;
                }
        }
        /*
        * @auther 邓钊
        * @desc 关闭价格面议
        * @date 2018-7-13
        * */
        $scope.priceNegotiableClose=function () {
            $scope.goodsinfo.is_price_negotiable = 0;
            $('.pNegotiable').attr('disabled',false);
            if($scope.specs.length){
                var arr = [];
                angular.forEach($scope.specs,function(val,key){
                    if(val.price !==undefined){
                        arr.push(val.price);
                    }
                })
                var min = Math.min.apply(Math, arr);
                $scope.goodsinfo.price = min.toFixed(2);
            }else{
                $('.s_price').attr('disabled',false);
                $scope.goodsinfo['price'] = '';
            }
        }
        $scope.negotiableType = function (num) {
            $scope.goodsinfo.negotiable_value = ""
            $scope.goodsinfo.negotiable_type = num
        }
        $scope.negotiableValue = function (val) {
            $scope.goodsinfo.negotiable_value = val
        }
        /*end*/
        //判断是否为修改
        if(getUrl()['2']=='editproduct'){
            // 初始化顶部栏选中
            angular.forEach($scope.chooseStep,function(val,key){
                val.isactive = '';
                if(key==0){
                    val.isactive = 'active';
                }
            })
            $scope.step = 1
            //---------------------- 发布商品编辑 ---------------------
            $http.get('/merchants/product/getproduct',{params:{id:getUrl()['3']}}).success(function(data){ 
                // 商品分类id
                data.status != 1 && tipshow(data.info,'warn')
                $scope.baseinfo.cam_id = data.data.cam_id?data.data.cam_id:0;
                if($scope.baseinfo.cam_id>0){
                    $scope.baseinfo.is_card = 1;
                    $('input[name="card_id"]').val(data.data.cam_title)
                    $('input[name="card_id"]').attr({cam_id:data.data.cam_id})
                    $('input[name="card_id"]').css({"background":"none","border":"none"})
                    $('input[name="is_card"]').prop({'disabled':true})
                    $('input[name="card_id"]').prop({'disabled':true})
                }else{
                    $scope.baseinfo.is_card = 0
                    $('input[name="is_card"]').prop({'disabled':true})
                }
                $scope.category_id = data.data.category_id;
                $scope.baseinfo.is_hexiao = data.data.is_hexiao;
                $scope.baseinfo.is_logistics = data.data.is_logistics;
                $scope.baseinfo.no_logistics = data.data.no_logistics;
                $scope.baseinfo.hexiao_start = parseInt(data.data.hexiao_start)==0 ? "" : data.data.hexiao_start;
                $scope.baseinfo.hexiao_end = parseInt(data.data.hexiao_end)==0 ? "" : data.data.hexiao_end;
                if($scope.baseinfo.is_hexiao==1){
                    $scope.ngHide = 'hide';
                }
                //商品类型
                $scope.baseinfo.type = data.data.type;
                // 商品id
                if(data.data.sku && data.data.sku.props){
                    angular.forEach(data.data.sku.props,function(val,key){
                        $scope.Guival[key] = [];
                        $scope.Guival[key]['values'] = [];
                        $scope.guiSelS.push({"prop":val.props});
                        $scope.guiSelS[key]['values'] = [];
                        if(val.values.length){
                            angular.forEach(val.values,function(val1,key1){
                                val1.img = imgUrl + val1.img; 
                                val1['parent'] = val.props;
                                $scope.Guival[key].push(val1);
                                $scope.guiSelS[key]['values'].push(val1);
                            })
                        }
                    }) 
                    // alert(data.data.sku.props[0]['props']['show_img']);
                    $scope.guiCheckImg = data.data.sku.props[0]['props']['show_img'] == 1 ? true:false;
                    $scope.specs = data.data.sku.stocks;
                    if($scope.specs.length){
                        angular.forEach($scope.specs,function(val,key){
                            if(val.rowspan1 == 0){
                                val.rowspan1 = 1
                            }
                            if(val.rowspan0 == 0){
                                val.rowspan1 = 0
                            }
                        })
                    }
                    $scope.spkucun = true; 
                    $scope.selGuige = false;
                }
                //运费模板
                if(data.data.sku.length == 0){
                    $scope.setEms.weight = data.data.weight;
                }
                // if(){

                // }
                $scope.postData.id = data.data.id;
                $scope.postData.prop1 = data.data.prop1;
                $scope.postData.prop2 = data.data.prop2;
                // $scope.editSku = data.data.sku;//编辑总数据
                // $scope.guiSelS = $scope.editSku.props;//规格数据
                // $scope.specs = $scope.editSku.stocks;//库存数据
                // $scope.Guival = $scope.editSku.stocks;//库存数据
                // if($scope.specs.length > 0){
                //     $scope.spkucun = true;
                // }

                $timeout(function(){
                    // angular.forEach($scope.kindItem,function(val,key){
                    //     if(val.subsub_cate !== undefined && val.subsub_cate.length>0){
                    //         angular.forEach(val.subsub_cate,function(val1,key1){
                    //             if($scope.category_id == val1.id){
                    //                 $scope.shopKind = val1.category_name;
                    //                 val.isactive = 'current';
                    //                 val.title = val1.category_name;
                    //             }
                    //         })
                    //     }
                    //     if($scope.category_id == val.category_id){
                    //         $scope.shopKind = val.title;
                    //         val.isactive = 'current';
                    //     } 
                    // })
                    //初始化商品分组 
                    $scope.baseinfo.group_id = data.data.group_id.split(",");

                    

                    // 初始化核销
                    $scope.baseinfo.is_hexiao = data.data.is_hexiao;
                    $scope.baseinfo.hexiao_start = parseInt(data.data.hexiao_start)==0 ? "" : data.data.hexiao_start;
                    $scope.baseinfo.hexiao_end = parseInt(data.data.hexiao_end)==0 ? "" : data.data.hexiao_end; 
                    
                    // angular.forEach(data.data.group_id.split(','),function(val,key){
                    //     angular.forEach($scope.baseinfo.shopGrounp,function(val1,key1){
                    //         if(val == val1.group_id){
                    //             val1.seleted = true;
                    //         }
                    //     })
                    // })
                    chose_mult_set_ini('.chosen_select',data.data.group_id);
                    
                },500)
                $scope.baseinfo.buy_way = data.data.buy_way;
                $scope.baseinfo.presell_flag = parseInt(data.data.presell_flag);
                if($scope.baseinfo.presell_flag==1){
                    $scope.baseinfo.presell_delivery_type = parseInt(data.data.presell_delivery_type);
                    if($scope.baseinfo.presell_delivery_type ==1){
                        $scope.baseinfo.presell_delivery_time = data.data.presell_delivery_time;
                    }else if($scope.baseinfo.presell_delivery_type ==2){
                        $scope.baseinfo.presell_delivery_payafter = parseInt(data.data.presell_delivery_payafter);
                    }
                }                
                //初始化批发
                $scope.baseinfo.is_wholesale = data.data.wholesale_flag;
                $scope.goodsinfo.wholesale_array = [{min:'',max:'',price:''}];
                var simpleWholeArr = data.data.wholesale_array;
                for(var i=0;i<simpleWholeArr.length;i++){
                    $scope.goodsinfo.wholesale_array[i]={min:+simpleWholeArr[i].min,max:+simpleWholeArr[i].max,price:+simpleWholeArr[i].price}
                }
                // 初始化邮费
                $scope.setEms.freight_price = data.data.freight_price;
                // 初始化库存
                $scope.postData.stock_show = data.data.stock_show;
                $scope.goodsinfo.stock = data.data.stock;
                // 初始化销量
                $scope.goodsinfo.sold_num = data.data.sold_num;
                //初始化商家编码
                $scope.postData.goods_no = data.data.goods_no;
                // 初始化商品名称
                $scope.goodsinfo.title = data.data.title;
                // 初始化价格面议
                $scope.goodsinfo.buy = data.data.buy;
                //初始化商品价格
                $scope.goodsinfo.price = data.data.price;
                $scope.goodsinfo.oprice = data.data.oprice;
                $scope.goodsinfo.cost_price = data.data.cost_price;
                $scope.goodsinfo.is_price_negotiable = data.data.is_price_negotiable;
                $scope.goodsinfo.negotiable_type = data.data.negotiable_type;
                $scope.goodsinfo.negotiable_value = data.data.negotiable_value;
                $scope.negotiable[$scope.goodsinfo.negotiable_type].value = $scope.goodsinfo.negotiable_value
                if($scope.goodsinfo.is_price_negotiable == 1){
                    $('.pNegotiable').attr('disabled',true);
                    $('.s_price').attr('disabled',true);
                }
                //如果值为1，禁止输入价格
                if($scope.goodsinfo.is_price_negotiable == 1){
                    // $('.pNegotiable').attr('disabled',true);
                    $timeout(function(){
                        angular.element('.pNegotiable').attr('disabled',true);
                    })
                }
                // if($scope.goodsinfo.is_price_negotiable == 0){
                //     angular.element('.priceNegotiable').attr('disabled',true);
                // }
                //初始化商品图片
                angular.forEach(data.data.imgs,function(val,key){
                    $scope.goodsinfo.img.push({
                        'FileInfo':{'path':imgUrl + val},
                        'close':false,
                        'index':key
                    })
                })
                // 初始化商品分享图片,标题，描述
                if(data.data.share_img){
                    $scope.postData.share_img = imgUrl + data.data.share_img;
                }
                $scope.postData.share_title = data.data.share_title;
                $scope.postData.share_desc = data.data.share_desc;
                //初始化运费设置
                $scope.setEms.freight_type = data.data.freight_type;
                //初始化运费模板
                if($scope.setEms.freight_type == 2){
                    $timeout(function(){//词字段赋值不能先于运费末班数据
                        $scope.postData.freight_id = parseInt(data.data.freight_id);
                    },1000)
                }
                //初始化每人限购
                $scope.setEms.quota = data.data.quota;
                // 初始化最小购买量
                $scope.setEms.buy_min = data.data.buy_min;
                //初始化购买权限
                $scope.setEms.buy_permissions_flag = parseInt(data.data.buy_permissions_flag);
                //初始化购买权限会员等级
                $scope.setEms.buy_permissions_level_id = data.data.buy_permissions_level_id;
                chose_mult_set_ini('#member_level',data.data.buy_permissions_level_id);
                //初始化留言字段
                $scope.setEms.noteList = data.data.noteList;
                // 初始化开售时间
                $scope.setEms.sale_time_flag = parseInt(data.data.sale_time_flag);
                if($scope.setEms.sale_time_flag ==2){
                    $scope.setEms.sale_time = data.data.sale_time;
                }
                $scope.setEms.is_discount = data.data.is_discount; 
                // // 初始化商品详情
                // if (data.data.introduce) {
                //     //data.data.introduce.replace(/<img [^>]*src=['"]([^'"]+)[^>]*>/gi, function (_match, capture) {
                //     data.data.introduce.replace(/<img [^>]*src=['"](ueditor[^'"]+)[^>]*>/gi, function (_match, capture) {
                //         data.data.introduce = data.data.introduce.replace(capture, _host + capture);
                //     });
                // }
                // 初始化积分开启
                $scope.setEms.is_point = data.data.is_point;
                //第三步字段赋值
                $scope.checkedTemplateId = data.data.templete_use_id;
                chose_mult_set_ini('.chzn-select',$scope.checkedTemplateId);
                $scope.productIntro = data.data.summary;
                if(data.data.content){
                    $scope.editors = JSON.parse(data.data.content); 
                    if($scope.editors.length>0 && typeof($scope.editors) != 'string'){
                        angular.forEach($scope.editors,function(val,key){
                            if(val.type == 'goods'){
                                val.thGoods = [];
                                val.goods = val.goods == undefined ? []:val.goods; 
                                if(val.goods.length>0){
                                    angular.forEach(val.goods,function(val1,key1){
                                        val1.thumbnail = imgUrl + val1.thumbnail;
                                        if(val.thGoods.length > 0){
                                            if(val.thGoods[val.thGoods.length - 1].length>=3){
                                                $scope.editors[key]['thGoods'].push([]);
                                                $scope.editors[key]['thGoods'][val.thGoods.length-1].push(val1)
                                            }else{
                                                val.thGoods[val.thGoods.length - 1].push(val1)
                                            }
                                        }else{
                                            val.thGoods[0] = [];
                                            val.thGoods[0].push(val1)
                                        }
                                    })
                                }else{
                                    val.thGoods = [
                                        [
                                            {
                                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                                'name':'这里显示商品名称',
                                                'info':'这里显示商品通知',
                                                'price':'￥1'
                                            },
                                            {
                                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                                'name':'这里显示商品名称',
                                                'info':'这里显示商品通知',
                                                'price':'￥2'
                                            },
                                            {
                                                'thumbnail':_host + 'static/images/product_img_1.jpg',
                                                'name':'这里显示商品名称',
                                                'info':'这里显示商品通知',
                                                'price':'￥3'
                                            }
                                        ]
                                    ]
                                }
                                // if(val.thGoods.length>0){
                                //     angular.forEach(val.thGoods,function(val1,key1){
                                //         angular.forEach(val1,function(val2,key2){
                                //             val2.thumbnail = _host + val2.thumbnail;
                                //         })
                                //     })
                                // }
                            }
                            if(val.type=='image_ad'){ 
                                if(val.images.length>0){
                                    angular.forEach(val.images,function(val1,key1){
                                        val1.FileInfo.m_path = imgUrl + val1.FileInfo.m_path;
                                        val1.FileInfo.path = imgUrl + val1.FileInfo.path;
                                    })
                                }
                            }
                            if(val.type=="image_link"){
                                if(val.images.length > 0){
                                    angular.forEach(val.images,function(val1,key1){
                                        val1.thumbnail = imgUrl + val1.thumbnail;
                                    })
                                }
                            }
                            if(val.type == "goodslist"){
                                val.thGoods = [
                                    [
                                        {
                                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                                            'name':'这里显示商品名称',
                                            'info':'这里显示商品通知',
                                            'price':'￥1'
                                        },
                                        {
                                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                                            'name':'这里显示商品名称',
                                            'info':'这里显示商品通知',
                                            'price':'￥2'
                                        },
                                        {
                                            'thumbnail':_host + 'static/images/product_img_1.jpg',
                                            'name':'这里显示商品名称',
                                            'info':'这里显示商品通知',
                                            'price':'￥3'
                                        }
                                    ]
                                ]
                            }
                            if(val.type == 'header'){
                                val.logo = store.logo;
                            }
                            if(val.type == 'bingbing'){
                                val.bg_image = imgUrl + val.bg_image;
                                if(val.lists.length>0){
                                    angular.forEach(val.lists,function(val1,key){
                                        if(val1.icon != ''){
                                            val1.icon = imgUrl + val1.icon;
                                        }
                                        if(val1.bg_image != ''){
                                            val1.bg_image = imgUrl + val1.bg_image;
                                        }
                                    })
                                }
                            }
                        })
                    }
                }
                // ue_category.setContent(11111);
                // var ue = initUeditor('editor');
                ue_category.ready(function(){
                     angular.forEach($scope.editors,function(val,key){
                        if(val.type == 'shop_detail'){
                            ue_category.setContent($scope.editors[key].content);      
                        }
                    })
                    //清除导入img图片变形问题修改
                    setTimeout(function(){
                        $('.app-fields img').removeAttr('width');
                        $('.app-fields img').removeAttr('height');
                    },1000)
                })
            })
        }
        //最小购买量事件绑定
        $(".js-buy-min").on('input',function(){
            var buyMin = $(this).val();
            if($scope.setEms.quota>0 && +buyMin > +$scope.setEms.quota){
                tipshow("最小购买量不能超过限购数量","warn");
                $(this).val($scope.setEms.quota);
                $scope.setEms.buy_min = $scope.setEms.quota;
                return ;
                
            }
            if(+buyMin > +$scope.goodsinfo.stock){
                tipshow("最小购买量不能超过总库存","warn");
                $(this).val($scope.goodsinfo.stock);
                $scope.setEms.buy_min = $scope.goodsinfo.stock;
            }
        })
        //限购数量事件绑定
        $(".js-control-num").on('blur',function(){
            var quota = $(this).val();
            if(quota>0 && +quota < +$scope.setEms.buy_min){
                tipshow("限购数量不能低于最小购买量","warn");
                $(this).val($scope.setEms.buy_min);
                $scope.setEms.quota = $scope.setEms.buy_min;
                //console.log($scope.setEms.quota);
            }
            if(+quota > +$scope.goodsinfo.stock){
                tipshow("限购数量不能超过总库存","warn");
                $(this).val($scope.goodsinfo.stock);
                $scope.setEms.quota = $scope.goodsinfo.stock;
            }
        })
        //总库存事件绑定
        $("#stockNum").on('blur',function(){
            var stock = $(this).val();
            if(+stock < +$scope.setEms.quota){
                tipshow("总库存不能低于限购数量","warn");
                $(this).val($scope.setEms.quota);
                $scope.goodsinfo.stock = $scope.setEms.quota;
            } else if(+stock < +$scope.setEms.buy_min){
                tipshow("总库存不能低于最小购买量","warn");
                $(this).val($scope.setEms.buy_min);
                $scope.goodsinfo.stock = $scope.setEms.buy_min;
            }
        })
        //刷新分组
        $scope.refreshGroup = function(){
            queryGroup();
        }
        $scope.upload = function(){
            $scope.uploadShow = true;
            $('.webuploader-pick').next('div').css({
                'top': '19px',
                'width': '168px',
                'height': '44px',
                'left':'40%'
            })
        }
        //第一步选择一级分类
        // $scope.chooseKind = function(kind){
        //     angular.forEach($scope.kindItem,function($val,$key){
        //         $val.isactive = '';
        //     })
        //     kind.isactive = 'current';
        //     $scope.shopKind = kind.title;
        //     $scope.baseinfo.category_id = kind.category_id;
        //     $scope.postData.category_id = kind.category_id;
        //     $scope.category_id = kind.category_id;
        // }
        //选择二级分类
        $scope.chooseSubcate = function(kind,$index){
            kind.title = kind['subsub_cate'][$index]['category_name'];
            kind.category_id = kind['subsub_cate'][$index]['id'];
        }
        //清除active
        $scope.removeActive = function(){
            angular.forEach($scope.chooseStep,function(key,val){
                key.isactive = '';
            }) 
        }
        // 点击步骤按钮
        $scope.goStep = function($index,kind,isValid){
            if($index==0){
                //隐藏类目 Herry 20171218
                /*if($scope.shopKind==null){
                    $scope.error = true;//显示第一步错误信息
                    return;
                }*/
                $scope.removeActive();
                $scope.step = 1;
                kind.isactive = 'active';
            }else if($index ==1){
                //验证商品规格
                var skuSuccess = false;
                angular.forEach($scope.Guival,function(item,i){
                    if(!item.length){
                        skuSuccess = true;
                    }
                })
                if(skuSuccess){
                    tipshow("请完善商品规格","warn");
                   return; 
                }
                // 验证商品图片
                if($scope.goodsinfo.img.length==0){
                    $scope.is_post = true;
                    $scope.submitted = true;
                    tipshow("请先编辑基本信息","warn")
                    return false;
                    //return;
                }
                // 验证商品名称
                if($scope.goodsinfo.title.length<=4){
                    $scope.is_post = true;
                    //return;
                }
                //验证商品外链地址
                if($scope.baseinfo.buy_way==2){
                    if($scope.goodsinfo.out_buy_link ==''){
                        $scope.waiLink_show = true;
                        //return;
                    }
                }
                //隐藏类目 Herry 20171218
                if(!isValid){
                    $scope.submitted = true;
                    tipshow("请先编辑基本信息","warn")
                    return false;
                }
                $scope.removeActive();
                $scope.step = 2;
                kind.isactive = 'active';
            }
        }
        //点击上一步
        $scope.prev = function(){
            $scope.step =1;
            $scope.removeActive();
            $scope.chooseStep[0].isactive = 'active';
        }
        // 点击下一步按钮
        $scope.goNext = function(isValid){
            if($scope.step == 1){
                var share_title = $("input[name='share_title']").val();
                var share_desc = $("textarea[name='share_desc']").val();
                var share_img = $("input[name='share_img']").val();
                if(!((share_title && share_desc && share_img) || (!share_title && !share_desc && !share_img))){//都有内容或者都没内容通过
                    if(!share_img && share_title && share_desc){
                        tipshow("请填写分享图片","warn");
                        return false;
                    }
                    if(!share_title && share_img && share_desc){
                        tipshow("请填写分享标题","warn");
                        return false;
                    }
                    if(!share_desc && share_title && share_img){
                        tipshow("请填写分享内容","warn");
                        return false;
                    }
                    if(share_img){
                        tipshow("请填写分享标题及内容","warn");
                        return false;
                    }
                    if(share_title){
                        tipshow("请填写分享内容及图片","warn");
                        return false;
                    }
                    if(share_desc){
                        tipshow("请填写分享标题及图片","warn");
                        return false;
                    }
                }

                /*add by 邓钊 2018-7-13 验证面议*/
                if($scope.goodsinfo.is_price_negotiable == 1){
                    if($scope.goodsinfo.negotiable_type == 2){
                        var numType = $scope.goodsinfo.negotiable_type
                        if(!$scope.negotiable[numType].value){
                            tipshow("请填写咨询微信","warn");
                            return false;
                        }
                    }else if($scope.goodsinfo.negotiable_type == 1){
                        var numType = $scope.goodsinfo.negotiable_type
                        if(!$scope.negotiable[numType].value){
                            tipshow("请填写咨询电话","warn");
                            return false;
                        }
                    }
                }
                /*end*/

                if($scope.baseform.is_card.$modelValue == 1){
                    if($('input[name="card_id"]').val()==0){
                        tipshow('请选择卡密','warn')
                        return false
                    }
                }

                //验证商品规格
                var skuSuccess = false;
                angular.forEach($scope.Guival,function(item,i){
                    if(!item.length){
                        skuSuccess = true;
                    }
                })
                if(skuSuccess){
                    tipshow("请完善商品规格","warn");
                   return; 
                }
                // 验证商品图片
                if($scope.goodsinfo.img.length==0){
                    $scope.is_post = true;
                    $scope.submitted = true;
                    tipshow("请先编辑基本信息","warn")
                    return false;
                    //return;
                }
                // 验证商品名称
                if($scope.goodsinfo.title.length<=4){
                    $scope.is_post = true;
                    //return;
                }
                //验证商品外链地址
                if($scope.baseinfo.buy_way==2){
                    if($scope.goodsinfo.out_buy_link ==''){
                        $scope.waiLink_show = true;
                        //return;
                    }
                }
                console.log($scope.setEms['buy_min']);
                // 2018-06-01 核销时间的隐藏
                // if($scope.baseinfo.is_hexiao==1 && $scope.baseinfo.hexiao_start==""){
                //     $scope.submitted = true;
                //     tipshow("请设置核销时间","warn");
                //     return false;
                // }
                // if($scope.baseinfo.is_hexiao==1 && $scope.baseinfo.hexiao_end==""){
                //     $scope.submitted = true;
                //     tipshow("请设置核销时间","warn");
                //     return false;
                // }
                if(!isValid){
                    $scope.submitted = true;
                    tipshow("请先编辑基本信息","warn")
                    return false;
                }
                //判断库存是否都为0；
                if($scope.specs.length==0 && $scope.goodsinfo.stock==0){
                	layerOpen();
                	return false;
                }
                //判断多规格的时候的库存是否都为零
                if($scope.specs.length > 0){
                	var checkNum = 0;
                	angular.forEach($scope.specs,function(val,key){
                	    if(val.stock_num == 0){checkNum ++}
                	})  
                	if(checkNum == $scope.specs.length){
                		layerOpen();
                		return false;
                	}
                }
                
                function layerOpen(){
                	layer.open({
					  	title: '提示',
					  	content: '总库存为 0 时，发布的商品会上架到『已售罄的商品』列表中，请确认是否设置库存为0',
					  	skin:"layer-tip-stock", //自定义layer皮肤 
					  	closeBtn: 0,
					  	btn: ['确定', '取消'],
					  	yes: function(){
					  		$scope.$apply(function(){
					  			$scope.step =2;
				                $scope.removeActive();
				                $scope.chooseStep[1].isactive = 'active';
					  		})
			                layer.closeAll()
					  	}
					}); 
                }
                
                
                $scope.step =2;
                $scope.removeActive();
                $scope.chooseStep[1].isactive = 'active';
            }
        }

        //规格图片上传得到base64
        $scope.getFile = function (file) {
            fileReader.readAsDataUrl(file, $scope)
                .then(function(result) {
                // $scope.imageSrc = result;
                $scope.guilist[$scope.rowIndex]['thumbnail'] = result;
            });
        };

        //选择规格图片
        // $scope.upload= function($event){
        //     console.log($event);
        //     alert('a')
        // }
        $scope.imageCK = function($index){
            $scope.rowIndex = $index;
            commonServer.addAdvs($scope);
            $scope.eventKind = 2;
        }
        // 运费要求字段添加字段
        $scope.addNote = function(){
            if($scope.setEms.noteList.length>=10){
                return;
            }
            $scope.setEms.noteList.push(
                {
                    'title':'留言',
                    'type':0,//文本 text | 电话 tel | 邮箱 email | 日期 date | 时间 time | 身份证 id_no | 图片 image
                    'multiple':0,//0否 1是
                    'required':0,//0否 1是
                }
            )            
        }
        //删除一行文本字段
        $scope.removeNote = function($index){
            $scope.setEms.noteList.splice($index,1);
        }
        // 添加商品图片

        $scope.addImages = function(){
            // showModel($('#myModal-adv'),$('#modal-dialog-adv'));
            // $scope.uploadShow = false; //判断上传可图片model显示
            // $scope.tempUploadImage = [];
            // angular.forEach($scope.uploadImages,function(key,val){
            //     key.isShow = false;
            // })
            $scope.eventKind = 1;
            commonServer.addAdvs($scope);
        }
        //添加分享图片
        $scope.addShareImages = function(){
            $scope.eventKind = 3;
            commonServer.addShareImages($scope);
        }
        //删除分享图片
        $scope.removeShareImage = function(){
            $scope.postData.share_img = '';
            $scope.addPic="+添加图片";//分享图片加图或者修改
            $scope.classShow=true;//分享图片显示样式
        }
         //上传确定按钮
        $scope.uploadSureBtn = function(){
            $('#myModal-adv').hide();
            $('.modal-backdrop').hide();
            closeUploader();
            if($scope.editorImg == 1){
                commonServer.chooseAdvSureBtn($scope);
                $scope.editorImg = 0;
                return false;
            }
            console.log($scope.eventKind,'cccccc')
            if($scope.eventKind == 1){
                var img = $scope.goodsinfo.img;
                for(var i = 0 ; i < img.length;i++){
                    $scope.goodsinfo.img[i]['close'] = false;
                }          
            }
            if($scope.eventKind == 2){
                if($scope.upSpecImg){
                    $scope.specImg[$scope.rowIndex] = $scope.upSpecImg.FileInfo.path;
                    $($(".weui_uploader_input_wrp")[$scope.rowIndex]).css("background-image","url(" +$scope.specImg[$scope.rowIndex] +  ")");
                    $scope.guiSelS[0].prop.show_img = 1;  
                    $scope.guiSelS[0].values[$scope.rowIndex]={};
                    $scope.guiSelS[0].values[$scope.rowIndex].img = $scope.upSpecImg.FileInfo.path; //字段图片赋值
                    $scope.Guival[0][$scope.rowIndex].img = $scope.upSpecImg.FileInfo.path; 
                }
            }else if($scope.eventKind == 3){
                $scope.postData.share_img = $scope.tempUploadImage[0].FileInfo.path; 
            }
        }
        // 返回选择图片
        $scope.showImage = function(){
            $scope.uploadShow = false; //判断上传可图片model显示
        }
        //使用上图
        $scope.usePreImage = function($index){
        	if($index != 0){
        		$scope.guilist[$index]['img'] = $scope.guilist[$index-1]['img'];
        	}
        }
        //隐藏model
        $scope.hideModel = function(){
            $('#upload_model').hide();
            $('#myModal-adv').hide();
            $('.modal-backdrop').hide();
        }
        // 加图图片
        $scope.uploadImages = []
        $scope.tempUploadImage = [];//选择图片中转数组
        $scope.initchooseAdvImage = function(){
            commonServer.initchooseAdvImage($scope);
        }
        // 图片分组点击
        $scope.chooseGroup = function(grounp){
            commonServer.chooseGroup($scope,grounp);
        }
        // 选择图片
        $scope.chooseImage = function(image,$index){
            commonServer.chooseImage(image,$index,$scope)
        }
        //选择广告图片确定按钮
        $scope.chooseAdvSureBtn = function(){
            $scope.hideModel();
            console.log($scope.eventKind)
            if($scope.editorImg == 1){
                commonServer.chooseAdvSureBtn($scope);
                $scope.editorImg = 0;
                return false;
            }
            if($scope.eventKind == 1){
                var adverimg=$scope.tempUploadImage;
                for(var i=0; i<adverimg.length;i++){
                    $scope.tempUploadImage[i]['close'] = false;
                    $scope.goodsinfo.img.push($scope.tempUploadImage[i])
                }
            }
            if($scope.eventKind == 2){
                // $scope.guilist[$scope.rowIndex]={};
                // alert($scope.rowIndex);
                $scope.Guival[0][$scope.rowIndex]['img'] = $scope.tempUploadImage[0].FileInfo.path; 
                // $scope.guilist[$scope.rowIndex]['img'] = $scope.tempUploadImage[0].FileInfo.s_path;
                // $($(".weui_uploader_input_wrp")[$scope.rowIndex]).css("background-image","url(" +$scope.specImg[$scope.rowIndex] +  ")");
                // $scope.guiSelS[0].prop.show_img = 1;  
                // $scope.guiSelS[0].values[$scope.rowIndex] = {};
                // $scope.guiSelS[0].values[$scope.rowIndex].img = $scope.tempUploadImage[0].FileInfo.s_path; //字段图片赋值
                // $scope.Guival[0][$scope.rowIndex].img = $scope.tempUploadImage[0].FileInfo.s_path;   
            }else if($scope.eventKind == 3){
                    $scope.postData.share_img = $scope.tempUploadImage[0].FileInfo.path;
                    $scope.addPic="修改图片";//分享图片加图或者修改
                    $scope.classShow=false;//分享图片显示样式 
            }
           
        }
        // 鼠标移到图片上显示删除图标
        $scope.showDelete = function(image,$index){
            image['close'] = true;
        }
        //鼠标移开隐藏删除图标
        $scope.hideDelete = function(image){
            image['close'] = false;
        }
        //删除已经选择的商品图片
        $scope.removeImage = function($index){
            $scope.goodsinfo.img.splice($index,1)
        }
        // 显示规格图片点击
        $scope.showGuiImage = function(){
            if($('input[name="gui_image"]').get(0).checked){
                $scope.guiCheckImg = true;
            }else{
                $scope.guiCheckImg = false;
            }
        }
        //提交表单
        $scope._id = _id
        // if($scope._id == 0){
        //     $scope.submitForm = function(isValid){
        //         angular.forEach($scope.Guival,function(val,index){
        //             $scope.guiSelS[index].values=[];//初始化
        //             angular.forEach(val,function(v,i){
        //                 if($scope.Guival[index][i].img){
        //                     var img = $scope.Guival[index][i].img.replace(_host,'');
        //                     img = $scope.Guival[index][i].img.replace(imgUrl,'');
        //                 }else{
        //                     var img = '';
        //                 }
        //                 $scope.guiSelS[index].values[i]= {
        //                     id: $scope.Guival[index][i].id,
        //                     img: img
        //                 };
        //             })
        //         })
        //         // return false;
        //         //控制规格图片显示
        //         if($scope.guiSelS.length){
        //             angular.forEach($scope.guiSelS,function(val,key){
        //                 if(val.prop != undefined){
        //                     if($scope.guiCheckImg){
        //                         val.prop.show_img = 1;
        //                     }else{
        //                         val.prop.show_img = 0;
        //                     }
        //                 }
        //             })
        //         }
        //         //规格字段
        //         $scope.postData.sku = {};
        //         $scope.postData.sku.props = $scope.guiSelS;
        //         $scope.postData.sku.stocks = $scope.specs;//规格商品库存量
        //         if($scope.postData.sku.props.length){
        //             angular.forEach($scope.postData.sku.props,function(val,key){
        //                 if(val.prop != undefined){
        //                     val.prop.show_img = $scope.guiCheckImg ? 1 : 0;
        //                 }
        //             })
        //         }
        //         // 验证商品图片
        //         if($scope.goodsinfo.img.length==0){
        //             $scope.is_post = true;
        //             //return;
        //         }
        //         // 验证商品名称
        //         if($scope.goodsinfo.title.length<=4){
        //             $scope.is_post = true;
        //             //return;
        //         }
        //         //验证商品外链地址
        //         if($scope.baseinfo.buy_way==2){
        //             if($scope.goodsinfo.out_buy_link ==''){
        //                 $scope.waiLink_show = true;
        //                 //return;
        //             }
        //         }
        //         // isValid = true;
        //         if(!isValid){
        //             $scope.submitted = true;
        //         }else{
        //             $scope.postData.is_distribution = $scope.setFenxiao.fenxiao_flag;
        //             $scope.postData.distribute_template_id = template.length==0?0:$scope.fxMobelId;
        //             $scope.postData.type = $scope.baseinfo.type;
        //             $scope.postData.presell_flag = $scope.baseinfo.presell_flag;
        //             $scope.postData.presell_delivery_type = $scope.baseinfo.presell_delivery_type;
        //             if($scope.postData.presell_delivery_type == 1){
        //                 $scope.postData.presell_delivery_time =  $scope.baseinfo.presell_delivery_time
        //             }else if($scope.postData.presell_delivery_type == 2){
        //                 $scope.postData.presell_delivery_payafter = $scope.baseinfo.presell_delivery_payafter;
        //             }
        //             //核销
        //             $scope.postData.is_hexiao = $scope.baseinfo.is_hexiao;
        //             $scope.postData.no_logistics = $scope.baseinfo.no_logistics;
        //             $scope.postData.hexiao_start = $scope.baseinfo.hexiao_start;
        //             $scope.postData.hexiao_end = $scope.baseinfo.hexiao_end;
        //             // $scope.postData.guilist = $scope.guilist;
        //             // angular.forEach($scope.postData.guilist,function(val,key){
        //             //     val.img = val.img.replace(_host,'');
        //             // })
        //             $scope.postData.title = $scope.goodsinfo.title;
        //             // console.log($scope.postData.title);
        //             // alert(1)
        //             $scope.postData.price = $scope.goodsinfo.price;
        //             $scope.postData.oprice = $scope.goodsinfo.oprice;
        //             $scope.postData.cost_price = $scope.goodsinfo.cost_price;
        //             $scope.postData.is_price_negotiable = $scope.goodsinfo.is_price_negotiable;
        //             $scope.postData.buy = $scope.goodsinfo.buy;
        //             $scope.postData.img = [];
        //             angular.forEach($scope.goodsinfo.img,function(val,key){
        //                 $scope.postData.img.push(val['FileInfo'].path.replace(imgUrl,''));
        //             })
        //             // console.log($scope.postData.img);
        //             // console.log( $scope.postData);
        //             $scope.postData.out_buy_link = $scope.goodsinfo.out_buy_link;
        //             $scope.postData.stock = $scope.goodsinfo.stock;
        //             $scope.postData.sold_num = $scope.goodsinfo.sold_num;
        //             $scope.postData.freight_type = $scope.setEms.freight_type;
        //             $scope.postData.freight_price = $scope.setEms.freight_price;
        //             // $scope.postData.freight_id = $scope.setEms.freight_id;
        //             $scope.postData.quota = $scope.setEms.quota;
        //             $scope.postData.is_point = $scope.setEms.is_point;
        //             $scope.postData.buy_permissions_flag = $scope.setEms.buy_permissions_flag;
        //             if($scope.postData.buy_permissions_flag == 1){
        //                 $scope.postData.buy_permissions_level_id = $scope.setEms.buy_permissions_level_id;
        //             }
        //             $scope.postData.noteList = $scope.setEms.noteList;
        //             $scope.postData.sale_time_flag = $scope.setEms.sale_time_flag;
        //             if($scope.postData.sale_time_flag == 2){
        //                 $scope.postData.sale_time = $scope.setEms.sale_time;
        //             }
        //             $scope.postData.is_discount = $scope.setEms.is_discount;
        //             $scope.postData.introduce = $scope.setEms.introduce;
        //
        //             // return;
        //             //详情不为空 则 把是富文本编辑器上传的图片的相对路径 拼接上source url
        //             if ($scope.postData.introduce != undefined) {
        //                 $scope.postData.introduce.replace(/<img [^>]*src=['"](\/ueditor[^'"]+)[^>]*>/gi, function (match, capture) {
        //                     if(capture.substr(0,1) == '/'){
        //                         $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.substr(1, capture.length));
        //                     }else{
        //                         $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.replace(_host, ''));
        //                         $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.replace(imgUrl, ''));
        //                     }
        //                 });
        //             }
        //             //添加第三步字段
        //             if(typeof $scope.editors == 'string' && $scope.editors == ''){
        //                 $scope.editors = [];
        //             }
        //             var _html = '<h4 style="text-align: center;margin:20px 0 8px;">商品详情区</h4><p style="text-align: center;margin-bottom: 10px;">点击进行编辑</p>'
        //             if($scope.editors[0].content == _html){
        //                 $scope.editors[0].content = '';
        //             }
        //
        //             // console.log($scope.editors);return false;
        //             $scope.editorImg = angular.copy($scope.editors);
        //             angular.forEach($scope.editorImg,function(val,key){
        //                 if(val.type == 'goods'){
        //                     val.goods = [];
        //                     val.thGoods = [];
        //                 }
        //                 if(val.type == 'coupon'){
        //                     val.couponList = [];
        //                 }
        //                 if(val.type == "image_ad"){
        //                     if(val.images.length>0){
        //                         angular.forEach(val.images,function(val1,key1){
        //                             val1.FileInfo = [];
        //                             delete val1.id;
        //                         })
        //                     }
        //                 }
        //                 if(val.type == 'goodslist'){
        //                     val.goods = [];
        //                     val.thGoods = [];
        //                 }
        //                 if(val.type == 'image_link'){
        //                     if(val.images.length>0){
        //                         angular.forEach(val.images,function(val1,key1){
        //                             val1.thumbnail = val1.thumbnail.replace(_host,'');
        //                             val1.thumbnail = val1.thumbnail.replace(imgUrl,'');
        //                         })
        //                     }
        //                 }
        //                 if(val.type == 'header'){
        //                     if(val.logo !== ''){
        //                         val.logo = val.logo.replace(_host,'');
        //                         val.bg_image = val.bg_image.replace(_host,'');
        //                         val.logo = val.logo.replace(imgUrl,'');
        //                         val.bg_image = val.bg_image.replace(imgUrl,'');
        //                     }
        //                 }
        //                 if(val.type == "good_group"){
        //                     if(val.group_type == 1){
        //                         val.top_nav = [];
        //                     }else if(val.group_type == 2){
        //                         val.left_nav = [];
        //                     }
        //                 }
        //                 if(val.type == 'bingbing'){
        //                     val.bg_image = val.bg_image.replace(_host,'');
        //                     val.bg_image = val.bg_image.replace(imgUrl,'');
        //                     if(val.lists.length>0){
        //                         angular.forEach(val.lists,function(val1,key1){
        //                             if(val1.bg_image != ''){
        //                                 val1.bg_image = val1.bg_image.replace(_host,'');
        //                                 val1.bg_image = val1.bg_image.replace(imgUrl,'');
        //                             }
        //                             if(val1.icon != ''){
        //                                 val1.icon = val1.icon.replace(_host,'');
        //                                 val1.icon = val1.icon.replace(imgUrl,'');
        //                             }
        //                         })
        //                     }
        //                 }
        //             })
        //
        //             $scope.editorImg = JSON.stringify($scope.editorImg);
        //             $scope.postData.content = {
        //                 templateId: $scope.checkedTemplateId,//模板id
        //                 productIntro: $scope.productIntro,//商品简介
        //                 editors: $scope.editorImg
        //             }
        //             //商品重量模板
        //             if($scope.setEms.isWeightTel){//选择重量模板
        //                 $scope.postData.weight = $scope.setEms.weight;
        //             }else{
        //                 $scope.postData.weight = "";
        //             }
        //
        //             //修改商品
        //             if(getUrl()['2']=='editproduct'){
        //                 $scope.postData.category_id = $scope.category_id;
        //                 $scope.postData.group_id = typeof($scope.baseinfo.group_id)=='string' ? $scope.baseinfo.group_id : $scope.baseinfo.group_id.join(',');
        //                 // if(typeof($scope.baseinfo.group_id)=='string'){
        //                 //     $scope.postData.group_id = $scope.baseinfo.group_id;
        //                 //     alert('a')
        //                 // }else{
        //                 //     if($scope.baseinfo.group_id && $scope.baseinfo.group_id.length>0){
        //                 //         // angular.forEach($scope.baseinfo.group_id,function(val,key){
        //                 //         //     if(key>=1){
        //                 //         //         $scope.postData.group_id = $scope.postData.group_id + val +',';
        //                 //         //     }else{
        //                 //         //         $scope.postData.group_id = val +',';
        //                 //         //     }
        //                 //         // })
        //
        //                 //         $scope.postData.group_id = $scope.baseinfo.group_id.join(',') //$scope.postData.group_id.substring(0,$scope.postData.group_id.length-1)
        //                 //     }
        //                 // }
        //                 // return;
        //                 // console.log($scope.postData);return false;
        //                 // 商品分享图片去掉域名
        //                 var sendData = angular.copy($scope.postData);//解决图片404问题
        //                 if(sendData.share_img){
        //                     sendData.share_img = sendData.share_img.replace(imgUrl,'');
        //                 }
        //                 $http.post('/merchants/product/editproduct',{data:sendData}).success(function(response) {
        //                     if(response.status == 1){
        //                         // layer.alert('修改商品成功!',function(){
        //                         //     // window.location.href = response.url;
        //                         //     //返回上一页(列表页 可能是商品库 可能是导入商品列表)
        //                         //     window.location.href = history.back();
        //                         // });
        //
        //                         tipshow('修改商品成功!');
        //                         setTimeout(function(){
        //                             window.location.href = '/merchants/product/index/1';
        //                         },1000)
        //                     } else {
        //                         tipshow(response.info, 'warn');
        //                     }
        //                 });
        //             }else{
        //                 // 添加商品
        //                 $scope.postData.group_id = typeof($scope.baseinfo.group_id)=='string' ? $scope.baseinfo.group_id : $scope.baseinfo.group_id.join(',');
        //                 // 添加商品
        //                 var sendData = angular.copy($scope.postData);
        //                 if(sendData.share_img){
        //                     sendData.share_img = sendData.share_img.replace(imgUrl,'');
        //                 }
        //                 $http.post('/merchants/product/addproduct',{data:sendData}).success(function(response) {
        //                     // console.log(response);
        //                     if(response.status == 1){
        //                         tipshow('发布商品成功！');
        //                         setTimeout(function(){
        //                             window.location.href = '/merchants/product/index/1';
        //                         },1000)
        //                     }else{
        //                         tipshow(response.info, 'warn');
        //                     }
        //                 });
        //             }
        //         }
        //     }
        // }else if($scope._id == 1){
        //     $scope.submitForm = function(isValid){
        //         $(".model_box").show();
        //         return false
        //         angular.forEach($scope.Guival,function(val,index){
        //             $scope.guiSelS[index].values=[];//初始化
        //             angular.forEach(val,function(v,i){
        //                 if($scope.Guival[index][i].img){
        //                     var img = $scope.Guival[index][i].img.replace(_host,'');
        //                     img = $scope.Guival[index][i].img.replace(imgUrl,'');
        //                 }else{
        //                     var img = '';
        //                 }
        //                 $scope.guiSelS[index].values[i]= {
        //                     id: $scope.Guival[index][i].id,
        //                     img: img
        //                 };
        //             })
        //         })
        //         // return false;
        //         //控制规格图片显示
        //         if($scope.guiSelS.length){
        //             angular.forEach($scope.guiSelS,function(val,key){
        //                 if(val.prop != undefined){
        //                     if($scope.guiCheckImg){
        //                         val.prop.show_img = 1;
        //                     }else{
        //                         val.prop.show_img = 0;
        //                     }
        //                 }
        //             })
        //         }
        //         //规格字段
        //         $scope.postData.sku = {};
        //         $scope.postData.sku.props = $scope.guiSelS;
        //         $scope.postData.sku.stocks = $scope.specs;//规格商品库存量
        //         if($scope.postData.sku.props.length){
        //             angular.forEach($scope.postData.sku.props,function(val,key){
        //                 if(val.prop != undefined){
        //                     val.prop.show_img = $scope.guiCheckImg ? 1 : 0;
        //                 }
        //             })
        //         }
        //         // 验证商品图片
        //         if($scope.goodsinfo.img.length==0){
        //             $scope.is_post = true;
        //             //return;
        //         }
        //         // 验证商品名称
        //         if($scope.goodsinfo.title.length<=4){
        //             $scope.is_post = true;
        //             //return;
        //         }
        //         //验证商品外链地址
        //         if($scope.baseinfo.buy_way==2){
        //             if($scope.goodsinfo.out_buy_link ==''){
        //                 $scope.waiLink_show = true;
        //                 //return;
        //             }
        //         }
        //         // isValid = true;
        //         if(!isValid){
        //             $scope.submitted = true;
        //         }else{
        //             $scope.postData.is_distribution = $scope.setFenxiao.fenxiao_flag;
        //             $scope.postData.distribute_template_id = template.length==0?0:$scope.fxMobelId;
        //             $scope.postData.type = $scope.baseinfo.type;
        //             $scope.postData.presell_flag = $scope.baseinfo.presell_flag;
        //             $scope.postData.presell_delivery_type = $scope.baseinfo.presell_delivery_type;
        //             if($scope.postData.presell_delivery_type == 1){
        //                 $scope.postData.presell_delivery_time =  $scope.baseinfo.presell_delivery_time
        //             }else if($scope.postData.presell_delivery_type == 2){
        //                 $scope.postData.presell_delivery_payafter = $scope.baseinfo.presell_delivery_payafter;
        //             }
        //             //核销
        //             $scope.postData.is_hexiao = $scope.baseinfo.is_hexiao;
        //             $scope.postData.no_logistics = $scope.baseinfo.no_logistics;
        //             $scope.postData.hexiao_start = $scope.baseinfo.hexiao_start;
        //             $scope.postData.hexiao_end = $scope.baseinfo.hexiao_end;
        //             // $scope.postData.guilist = $scope.guilist;
        //             // angular.forEach($scope.postData.guilist,function(val,key){
        //             //     val.img = val.img.replace(_host,'');
        //             // })
        //             $scope.postData.title = $scope.goodsinfo.title;
        //             // console.log($scope.postData.title);
        //             // alert(1)
        //             $scope.postData.price = $scope.goodsinfo.price;
        //             $scope.postData.oprice = $scope.goodsinfo.oprice;
        //             $scope.postData.cost_price = $scope.goodsinfo.cost_price;
        //             $scope.postData.is_price_negotiable = $scope.goodsinfo.is_price_negotiable;
        //             $scope.postData.buy = $scope.goodsinfo.buy;
        //             $scope.postData.img = [];
        //             angular.forEach($scope.goodsinfo.img,function(val,key){
        //                 $scope.postData.img.push(val['FileInfo'].path.replace(imgUrl,''));
        //             })
        //             // console.log($scope.postData.img);
        //             // console.log( $scope.postData);
        //             $scope.postData.out_buy_link = $scope.goodsinfo.out_buy_link;
        //             $scope.postData.stock = $scope.goodsinfo.stock;
        //             $scope.postData.sold_num = $scope.goodsinfo.sold_num;
        //             $scope.postData.freight_type = $scope.setEms.freight_type;
        //             $scope.postData.freight_price = $scope.setEms.freight_price;
        //             // $scope.postData.freight_id = $scope.setEms.freight_id;
        //             $scope.postData.quota = $scope.setEms.quota;
        //             $scope.postData.is_point = $scope.setEms.is_point;
        //             $scope.postData.buy_permissions_flag = $scope.setEms.buy_permissions_flag;
        //             if($scope.postData.buy_permissions_flag == 1){
        //                 $scope.postData.buy_permissions_level_id = $scope.setEms.buy_permissions_level_id;
        //             }
        //             $scope.postData.noteList = $scope.setEms.noteList;
        //             $scope.postData.sale_time_flag = $scope.setEms.sale_time_flag;
        //             if($scope.postData.sale_time_flag == 2){
        //                 $scope.postData.sale_time = $scope.setEms.sale_time;
        //             }
        //             $scope.postData.is_discount = $scope.setEms.is_discount;
        //             $scope.postData.introduce = $scope.setEms.introduce;
        //
        //             // return;
        //             //详情不为空 则 把是富文本编辑器上传的图片的相对路径 拼接上source url
        //             if ($scope.postData.introduce != undefined) {
        //                 $scope.postData.introduce.replace(/<img [^>]*src=['"](\/ueditor[^'"]+)[^>]*>/gi, function (match, capture) {
        //                     if(capture.substr(0,1) == '/'){
        //                         $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.substr(1, capture.length));
        //                     }else{
        //                         $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.replace(_host, ''));
        //                         $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.replace(imgUrl, ''));
        //                     }
        //                 });
        //             }
        //             //添加第三步字段
        //             if(typeof $scope.editors == 'string' && $scope.editors == ''){
        //                 $scope.editors = [];
        //             }
        //             var _html = '<h4 style="text-align: center;margin:20px 0 8px;">商品详情区</h4><p style="text-align: center;margin-bottom: 10px;">点击进行编辑</p>'
        //             if($scope.editors[0].content == _html){
        //                 $scope.editors[0].content = '';
        //             }
        //
        //             // console.log($scope.editors);return false;
        //             $scope.editorImg = angular.copy($scope.editors);
        //             angular.forEach($scope.editorImg,function(val,key){
        //                 if(val.type == 'goods'){
        //                     val.goods = [];
        //                     val.thGoods = [];
        //                 }
        //                 if(val.type == 'coupon'){
        //                     val.couponList = [];
        //                 }
        //                 if(val.type == "image_ad"){
        //                     if(val.images.length>0){
        //                         angular.forEach(val.images,function(val1,key1){
        //                             val1.FileInfo = [];
        //                             delete val1.id;
        //                         })
        //                     }
        //                 }
        //                 if(val.type == 'goodslist'){
        //                     val.goods = [];
        //                     val.thGoods = [];
        //                 }
        //                 if(val.type == 'image_link'){
        //                     if(val.images.length>0){
        //                         angular.forEach(val.images,function(val1,key1){
        //                             val1.thumbnail = val1.thumbnail.replace(_host,'');
        //                             val1.thumbnail = val1.thumbnail.replace(imgUrl,'');
        //                         })
        //                     }
        //                 }
        //                 if(val.type == 'header'){
        //                     if(val.logo !== ''){
        //                         val.logo = val.logo.replace(_host,'');
        //                         val.bg_image = val.bg_image.replace(_host,'');
        //                         val.logo = val.logo.replace(imgUrl,'');
        //                         val.bg_image = val.bg_image.replace(imgUrl,'');
        //                     }
        //                 }
        //                 if(val.type == "good_group"){
        //                     if(val.group_type == 1){
        //                         val.top_nav = [];
        //                     }else if(val.group_type == 2){
        //                         val.left_nav = [];
        //                     }
        //                 }
        //                 if(val.type == 'bingbing'){
        //                     val.bg_image = val.bg_image.replace(_host,'');
        //                     val.bg_image = val.bg_image.replace(imgUrl,'');
        //                     if(val.lists.length>0){
        //                         angular.forEach(val.lists,function(val1,key1){
        //                             if(val1.bg_image != ''){
        //                                 val1.bg_image = val1.bg_image.replace(_host,'');
        //                                 val1.bg_image = val1.bg_image.replace(imgUrl,'');
        //                             }
        //                             if(val1.icon != ''){
        //                                 val1.icon = val1.icon.replace(_host,'');
        //                                 val1.icon = val1.icon.replace(imgUrl,'');
        //                             }
        //                         })
        //                     }
        //                 }
        //             })
        //
        //             $scope.editorImg = JSON.stringify($scope.editorImg);
        //             $scope.postData.content = {
        //                 templateId: $scope.checkedTemplateId,//模板id
        //                 productIntro: $scope.productIntro,//商品简介
        //                 editors: $scope.editorImg
        //             }
        //             //商品重量模板
        //             if($scope.setEms.isWeightTel){//选择重量模板
        //                 $scope.postData.weight = $scope.setEms.weight;
        //             }else{
        //                 $scope.postData.weight = "";
        //             }
        //
        //             //修改商品
        //             if(getUrl()['2']=='editproduct'){
        //                 $scope.postData.category_id = $scope.category_id;
        //                 $scope.postData.group_id = typeof($scope.baseinfo.group_id)=='string' ? $scope.baseinfo.group_id : $scope.baseinfo.group_id.join(',');
        //                 // if(typeof($scope.baseinfo.group_id)=='string'){
        //                 //     $scope.postData.group_id = $scope.baseinfo.group_id;
        //                 //     alert('a')
        //                 // }else{
        //                 //     if($scope.baseinfo.group_id && $scope.baseinfo.group_id.length>0){
        //                 //         // angular.forEach($scope.baseinfo.group_id,function(val,key){
        //                 //         //     if(key>=1){
        //                 //         //         $scope.postData.group_id = $scope.postData.group_id + val +',';
        //                 //         //     }else{
        //                 //         //         $scope.postData.group_id = val +',';
        //                 //         //     }
        //                 //         // })
        //
        //                 //         $scope.postData.group_id = $scope.baseinfo.group_id.join(',') //$scope.postData.group_id.substring(0,$scope.postData.group_id.length-1)
        //                 //     }
        //                 // }
        //                 // return;
        //                 // console.log($scope.postData);return false;
        //                 // 商品分享图片去掉域名
        //                 var sendData = angular.copy($scope.postData);//解决图片404问题
        //                 if(sendData.share_img){
        //                     sendData.share_img = sendData.share_img.replace(imgUrl,'');
        //                 }
        //                 $http.post('/merchants/product/editproduct',{data:sendData}).success(function(response) {
        //                     if(response.status == 1){
        //                         // layer.alert('修改商品成功!',function(){
        //                         //     // window.location.href = response.url;
        //                         //     //返回上一页(列表页 可能是商品库 可能是导入商品列表)
        //                         //     window.location.href = history.back();
        //                         // });
        //
        //                         tipshow('修改商品成功!');
        //                         setTimeout(function(){
        //                             window.location.href = '/merchants/product/index/1';
        //                         },1000)
        //                     } else {
        //                         tipshow(response.info, 'warn');
        //                     }
        //                 });
        //             }else{
        //                 // 添加商品
        //                 $scope.postData.group_id = typeof($scope.baseinfo.group_id)=='string' ? $scope.baseinfo.group_id : $scope.baseinfo.group_id.join(',');
        //                 // 添加商品
        //                 var sendData = angular.copy($scope.postData);
        //                 if(sendData.share_img){
        //                     sendData.share_img = sendData.share_img.replace(imgUrl,'');
        //                 }
        //                 $http.post('/merchants/product/addproduct',{data:sendData}).success(function(response) {
        //                     // console.log(response);
        //                     if(response.status == 1){
        //                         tipshow('发布商品成功！');
        //                         setTimeout(function(){
        //                             window.location.href = '/merchants/product/index/1';
        //                         },1000)
        //                     }else{
        //                         tipshow(response.info, 'warn');
        //                     }
        //                 });
        //             }
        //         }
        //     }
        // }
        $scope.getForm = function(isvalid,action){
            $scope.__isvalid = isvalid;
            $scope.action = action;
            if($scope._id == 0){
                $scope.submitForm($scope.__isvalid,$scope.action);
            }else if($scope._id == 1){
                $(".model_box").show();
            }
        }
        $(".btn_queren").on('click',function () {
            $(".model_box").hide();
            $scope.submitForm($scope.__isvalid,$scope.action)
        })

        $(".btn_close").on('click',function () {
            $(".model_box").hide();
        })
        $scope.submitForm = function(isValid,type){
            angular.forEach($scope.Guival,function(val,index){
                $scope.guiSelS[index].values=[];//初始化
                angular.forEach(val,function(v,i){
                    if($scope.Guival[index][i].img){
                        var img = $scope.Guival[index][i].img.replace(_host,'');
                            img = $scope.Guival[index][i].img.replace(imgUrl,'');
                    }else{
                        var img = '';
                    }
                    $scope.guiSelS[index].values[i]= {
                        id: $scope.Guival[index][i].id,
                        img: img
                    };
                })
            })
            // return false;
            //控制规格图片显示
            if($scope.guiSelS.length){
                angular.forEach($scope.guiSelS,function(val,key){
                    if(val.prop != undefined){
                        if($scope.guiCheckImg){
                            val.prop.show_img = 1;
                        }else{
                            val.prop.show_img = 0;
                        }
                    }
                })
            }
            //规格字段
            $scope.postData.sku = {};
            $scope.postData.sku.props = $scope.guiSelS;
            $scope.postData.sku.stocks = $scope.specs;//规格商品库存量
            if($scope.postData.sku.props.length){
                angular.forEach($scope.postData.sku.props,function(val,key){
                    if(val.prop != undefined){
                        val.prop.show_img = $scope.guiCheckImg ? 1 : 0;
                    }
                })
            }
            // 验证商品图片
            if($scope.goodsinfo.img.length==0){
                $scope.is_post = true;
                //return;
            }
            // 验证商品名称
            if($scope.goodsinfo.title.length<=4){
                $scope.is_post = true;
                //return;
            }
            //验证商品外链地址
            if($scope.baseinfo.buy_way==2){
                if($scope.goodsinfo.out_buy_link ==''){
                    $scope.waiLink_show = true;
                    //return;
                }
            }
            // isValid = true;
            if(!isValid){
                $scope.submitted = true;
            }else{
                //防止重复提交 add by 魏冬冬 2018-7-5
                if($scope.flag) return;
                $scope.flag = true;
                //end
                //编辑状态修改规格标记 add by 倪凯嘉 2019-1-27
                $scope.postData.is_edit_prop_value=$scope.is_edit_prop_value;
                //end
            	$scope.postData.is_distribution = $scope.setFenxiao.fenxiao_flag;
                $scope.postData.distribute_template_id = template.length==0?0:$scope.fxMobelId;
                $scope.postData.type = $scope.baseinfo.type;
                $scope.postData.presell_flag = $scope.baseinfo.presell_flag;
                $scope.postData.presell_delivery_type = $scope.baseinfo.presell_delivery_type;
                if($scope.postData.presell_delivery_type == 1){
                    $scope.postData.presell_delivery_time =  $scope.baseinfo.presell_delivery_time
                }else if($scope.postData.presell_delivery_type == 2){
                    $scope.postData.presell_delivery_payafter = $scope.baseinfo.presell_delivery_payafter;
                }
                //核销
                $scope.postData.is_logistics = $scope.baseinfo.is_logistics;
                $scope.postData.is_hexiao = $scope.baseinfo.is_hexiao;
                $scope.postData.no_logistics = $scope.baseinfo.no_logistics;
                // $scope.postData.hexiao_start = $scope.baseinfo.hexiao_start;
                // $scope.postData.hexiao_end = $scope.baseinfo.hexiao_end;
                // $scope.postData.guilist = $scope.guilist;
                // angular.forEach($scope.postData.guilist,function(val,key){
                //     val.img = val.img.replace(_host,'');
                // })
                $scope.postData.title = $scope.goodsinfo.title;
                // console.log($scope.postData.title);
                // alert(1)
                $scope.postData.price = $scope.goodsinfo.price;
                $scope.postData.oprice = $scope.goodsinfo.oprice;
                $scope.postData.cost_price = $scope.goodsinfo.cost_price;
                $scope.postData.is_price_negotiable = $scope.goodsinfo.is_price_negotiable;
                if($scope.postData.is_price_negotiable == 0){
                    $scope.goodsinfo.negotiable_type = 0
                    $scope.goodsinfo.negotiable_value = ''
                }
                $scope.postData.negotiable_type = $scope.goodsinfo.negotiable_type;
                $scope.postData.negotiable_value = $scope.goodsinfo.negotiable_value;
                $scope.postData.buy = $scope.goodsinfo.buy;
                $scope.postData.img = [];
                angular.forEach($scope.goodsinfo.img,function(val,key){
                    $scope.postData.img.push(val['FileInfo'].path.replace(imgUrl,''));
                })
                // console.log($scope.postData.img);
                // console.log( $scope.postData);
                $scope.postData.out_buy_link = $scope.goodsinfo.out_buy_link;
                $scope.postData.stock = $scope.goodsinfo.stock;
                $scope.postData.sold_num = $scope.goodsinfo.sold_num;
                $scope.postData.freight_type = $scope.setEms.freight_type;
                $scope.postData.freight_price = $scope.setEms.freight_price;
                // $scope.postData.freight_id = $scope.setEms.freight_id;
                $scope.postData.quota = $scope.setEms.quota;
                $scope.postData.buy_min = $scope.setEms.buy_min ? $scope.setEms.buy_min : 1;
                
                $scope.postData.is_point = $scope.setEms.is_point;
                $scope.postData.buy_permissions_flag = $scope.setEms.buy_permissions_flag;
                if($scope.postData.buy_permissions_flag == 1){
                    $scope.postData.buy_permissions_level_id = $scope.setEms.buy_permissions_level_id;
                }
                $scope.postData.noteList = $scope.setEms.noteList;
                $scope.postData.sale_time_flag = $scope.setEms.sale_time_flag;
                if($scope.postData.sale_time_flag == 2){
                    $scope.postData.sale_time = $scope.setEms.sale_time;
                }
                $scope.postData.is_discount = $scope.setEms.is_discount;
                $scope.postData.introduce = $scope.setEms.introduce;

                // return;
                //详情不为空 则 把是富文本编辑器上传的图片的相对路径 拼接上source url
                if ($scope.postData.introduce != undefined) {
                    $scope.postData.introduce.replace(/<img [^>]*src=['"](\/ueditor[^'"]+)[^>]*>/gi, function (match, capture) {
                        if(capture.substr(0,1) == '/'){
                            $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.substr(1, capture.length));
                        }else{
                            $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.replace(_host, ''));
                            $scope.postData.introduce = $scope.postData.introduce.replace(capture, capture.replace(imgUrl, ''));
                        }
                    });
                }
                //添加第三步字段
                if(typeof $scope.editors == 'string' && $scope.editors == ''){
                    $scope.editors = [];
                }
                var _html = '<h4 style="text-align: center;margin:20px 0 8px;">商品详情区</h4><p style="text-align: center;margin-bottom: 10px;">点击进行编辑</p>'
                if($scope.editors[0].content == _html){
                    $scope.editors[0].content = '';
                }

                // console.log($scope.editors);return false;
                $scope.editorImg = angular.copy($scope.editors);
                angular.forEach($scope.editorImg,function(val,key){
                    if(val.type == 'goods'){
                       val.goods = [];
                       val.thGoods = [];
                    }
                    if(val.type == 'coupon'){
                        val.couponList = [];
                    }
                    if(val.type == "image_ad"){
                        if(val.images.length>0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.FileInfo = [];
                                delete val1.id;
                            })
                        }
                    }
                    if(val.type == 'goodslist'){
                        val.goods = [];
                        val.thGoods = [];
                    }
                    if(val.type == 'image_link'){
                        if(val.images.length>0){
                            angular.forEach(val.images,function(val1,key1){
                                val1.thumbnail = val1.thumbnail.replace(_host,'');
                                val1.thumbnail = val1.thumbnail.replace(imgUrl,'');
                            })
                        }
                    }
                    if(val.type == 'header'){
                        if(val.logo !== ''){
                            val.logo = val.logo.replace(_host,'');
                            val.bg_image = val.bg_image.replace(_host,'');
                            val.logo = val.logo.replace(imgUrl,'');
                            val.bg_image = val.bg_image.replace(imgUrl,'');
                        }
                    }
                    if(val.type == "good_group"){
                        if(val.group_type == 1){
                            val.top_nav = [];
                        }else if(val.group_type == 2){
                            val.left_nav = [];
                        }
                    }
                    if(val.type == 'bingbing'){
                        val.bg_image = val.bg_image.replace(_host,'');
                        val.bg_image = val.bg_image.replace(imgUrl,'');
                        if(val.lists.length>0){
                            angular.forEach(val.lists,function(val1,key1){
                                if(val1.bg_image != ''){
                                    val1.bg_image = val1.bg_image.replace(_host,'');
                                    val1.bg_image = val1.bg_image.replace(imgUrl,'');
                                }
                                if(val1.icon != ''){
                                    val1.icon = val1.icon.replace(_host,'');
                                    val1.icon = val1.icon.replace(imgUrl,'');
                                }
                            })
                        }
                    }
                })

                $scope.editorImg = JSON.stringify($scope.editorImg);
                $scope.postData.content = {
                    templateId: $scope.checkedTemplateId,//模板id
                    productIntro: $scope.productIntro,//商品简介
                    editors: $scope.editorImg
                }
                //商品重量模板
                if($scope.setEms.isWeightTel){//选择重量模板
                    $scope.postData.weight = $scope.setEms.weight;
                }else{
                    $scope.postData.weight = "";
                }
                $scope.postData.wholesale_array = $scope.goodsinfo.wholesale_array;
                $scope.postData.wholesale_flag = $scope.baseinfo.is_wholesale;  
                if($scope.baseinfo.is_card==1){
                    $scope.postData.cam_id = $('input[name="card_id"]').attr("cam_id");
                }
                //修改商品
                if(getUrl()['2']=='editproduct'){
                    $scope.postData.category_id = $scope.category_id;
                    $scope.postData.group_id = typeof($scope.baseinfo.group_id)=='string' ? $scope.baseinfo.group_id : $scope.baseinfo.group_id.join(',');
                    // if(typeof($scope.baseinfo.group_id)=='string'){
                    //     $scope.postData.group_id = $scope.baseinfo.group_id;
                    //     alert('a')
                    // }else{
                    //     if($scope.baseinfo.group_id && $scope.baseinfo.group_id.length>0){
                    //         // angular.forEach($scope.baseinfo.group_id,function(val,key){
                    //         //     if(key>=1){
                    //         //         $scope.postData.group_id = $scope.postData.group_id + val +',';
                    //         //     }else{
                    //         //         $scope.postData.group_id = val +',';
                    //         //     }
                    //         // })

                    //         $scope.postData.group_id = $scope.baseinfo.group_id.join(',') //$scope.postData.group_id.substring(0,$scope.postData.group_id.length-1)
                    //     }
                    // }
                    // return;
                    // console.log($scope.postData);return false;
                    // 商品分享图片去掉域名
                    var sendData = angular.copy($scope.postData);//解决图片404问题
                    if(sendData.share_img){
                        sendData.share_img = sendData.share_img.replace(imgUrl,'');
                    }
                    if (!type) {
                        sendData.status = 0;
                    }
                    $http.post('/merchants/product/editproduct',{data:sendData}).success(function(response) {
                        // 2018/06/01 核销的隐藏物流和时间
                        // if(response.status == 1){
                        if(response.status == 1 || response.info == '请设置核销时间'){
                            // layer.alert('修改商品成功!',function(){
                            //     // window.location.href = response.url;
                            //     //返回上一页(列表页 可能是商品库 可能是导入商品列表)
                            //     window.location.href = history.back();
                            // });
                            if (type) {
                                tipshow('发布商品成功！');
                                setTimeout(function(){
                                    window.location.href = '/shop/preview/' + response.data.wid + '/' + response.data.id;
                                },1000)
                            } else {
                                tipshow('下架商品将会放入仓库中~');
                                setTimeout(function(){
                                    window.location.href = '/merchants/product/index/0?tag=0';
                                },1000)
                            }
                        } else {
                            //add by 魏冬冬 2018-7-5 提交失败 重置防重字段
                            $scope.flag = false;
                            //end
                            tipshow(response.info, 'warn');
                        }
                    });
                }else{
                    // 添加商品
                    $scope.postData.group_id = typeof($scope.baseinfo.group_id)=='string' ? $scope.baseinfo.group_id : $scope.baseinfo.group_id.join(',');
                    // 添加商品
                    var sendData = angular.copy($scope.postData);
                    if(sendData.share_img){
                        sendData.share_img = sendData.share_img.replace(imgUrl,'');
                    }
                    if (!type) {
                        sendData.status = 0;
                    }
                    console.log($scope.postData);
                    $http.post('/merchants/product/addproduct',{data:sendData}).success(function(response) {
                        // console.log(response);
                        if(response.status == 1){
                            if (type) {
                                tipshow('发布商品成功！');
                                setTimeout(function(){
                                    window.location.href = '/shop/preview/' + response.data.wid + '/' + response.data.id;
                                },1000)
                            } else {
                                tipshow('下架商品将会放入仓库中~');
                                setTimeout(function(){
                                    window.location.href = '/merchants/product/index/0?tag=0';
                                },1000)
                            }
                            
                        }else{
                            //add by 魏冬冬 2018-7-5 提交失败 重置防重字段
                            $scope.flag = false;
                            //end
                            tipshow(response.info, 'warn');
                        }
                    });
                }
            }
        }
        $scope.editors = [
            {
                'showRight':true,
                'cardRight':17, //3为富文本，4商品，5商品列表
                'type':'shop_detail',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'content':'<h4 style="text-align: center;margin:20px 0 8px;">商品详情区</h4>'
                             +'<p style="text-align: center;margin-bottom: 10px;">点击进行编辑</p>',
            }
        ];

        $scope.initCartRight = function(){
            if($scope.index == 0){
                commonServer.initCartRight($scope.index,$scope,-210);
            }else{
                commonServer.initCartRight($scope.index,$scope,25);
            }
        }
        $scope.index = 0;
        $scope.initCartRight();//初始化右边
        $scope.color = commonServer.color;//富文本设置背景颜色
        $scope.temp = commonServer.temp;//临时转存数组
        $scope.tempSure = commonServer.tempSure;//选择商品确定按钮
        $scope.chooseSureBtn = commonServer.chooseSureBtn; //选择广告图片确定按钮
        $scope.tempUploadImage = commonServer.tempUploadImage;//临时转存数组
        $scope.eventKind = commonServer.eventKind;//区分点击事件1，为添加广告多图，2为重新上传单图。
        $scope.advImageIndex = commonServer.advImageIndex; //重新上传图片索引记录
        $scope.changeImange = false; //判断是否是member修改图片
        $scope.uploadShow = false; //判断上传可图片model显示
        $scope.choosePage = 2 //1为美妆小店，2为微页面
        $scope.advsImagesIndex = commonServer.advsImagesIndex;
        var ue = initUeditor('editor');//初始化编辑器
        bindEventEditor(ue,$scope);//初始化编辑器
        laydate.skin('molv'); //切换皮肤，请查看skins下面皮肤库
        var start = {
            elem: '#date',
            format: 'YYYY-MM-DD',
            min: '2009-06-16 23:59:59', //设定最小日期为当前日期
            max: '2099-06-16 23:59:59', //最大日期
            event: 'focus',
            istime: true,
            istoday: false,
            choose: function(datas) {
                $scope.$apply(function(){
                    $scope.editors[$scope.index]['date'] = datas;
                })
            }
        };
        $scope.couponList = [];//券列表
        $scope.goodList = [];//商品列表
        $scope.uploadImages = [];//上传图片数组
        $scope.addeditor = function(position){
            commonServer.addeditor($scope,ue,position);
        } 
        //预览
        $scope.preview = function(){
            tipshow("页面重定向中");
            $timeout(function(){
                window.location.href = "/merchants/product/commodityPreview";
            },1000);
        }
        // 左侧点击
        $scope.tool = function(event,editor){
            editor['editing'] = 'editing';
            $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
            $('.app-field').removeClass('editing');
            event.currentTarget.className += ' editing';
            event.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)';
            $timeout(function(){
                $('.app-field').each(function(key,val){
                    if($(this).hasClass('editing')){
                        $scope.index = key;
                        if($scope.index == 0){
                            $('.card_right_list').css('margin-top',event.currentTarget.offsetTop-35);
                        }else{
                            $('.card_right_list').css('margin-top',event.currentTarget.offsetTop-80);
                        }
                        $scope.editors[$scope.index].showRight = true;
                        if(event.currentTarget.getAttribute('data-type')=='member'){
                            $scope.editors[$scope.index]['cardRight'] = 1;
                            $timeout(function(){
                                ue.setContent($('.editing').children('editor-text').html());
                                $scope.color = event.currentTarget.style.background;
                            },200);
                        }else if(event.currentTarget.getAttribute('data-type')=='category'){
                            $scope.editors[$scope.index]['cardRight'] = 2;
                            $timeout(function(){
                                var desc = document.getElementsByClassName('page_desc')[0];
                                ue_category.setContent(desc.innerHTML);
                                $scope.color = event.currentTarget.style.background;
                            },200);
                        }else if(event.currentTarget.getAttribute('data-type')=='rich_text'){
                            $scope.editors[$scope.index]['cardRight'] = 3;
                            $timeout(function(){
                                ue.setContent($('.editing').find('.custom-richtext').html());
                                $scope.color = event.currentTarget.style.background;
                            },200);
                        }else if(event.currentTarget.getAttribute('data-type')=='goods'){
                            $scope.editors[$scope.index]['cardRight'] = 4;
                        }else if(event.currentTarget.getAttribute('data-type')=='image_ad'){
                            $scope.editors[$scope.index]['cardRight'] = 5;
                        }else if(event.currentTarget.getAttribute('data-type')=='title'){
                            $scope.editors[$scope.index]['cardRight'] = 6; 
                        }else if(event.currentTarget.getAttribute('data-type')=='store'){
                            $scope.editors[$scope.index]['cardRight'] = 7;
                        }else if(event.currentTarget.getAttribute('data-type')=='shop_detail'){
                            $scope.editors[$scope.index]['cardRight'] = 17;
                            // console.log(ue_category)
                            // ue_category.setContent('');
                            $scope.color = event.currentTarget.style.background;
                        }
                    }
                }) 
            },100)
        }
        // 上传图片
        $scope.upload = function(){
            $scope.uploadShow = true;
            $('.webuploader-pick').next('div').css({
                'top': '19px',
                'width': '168px',
                'height': '44px',
                'left':'40%'
            })
        }
        // 添加边框
        $scope.addboder = function(editor){
            commonServer.addboder(editor,$scope);
        }
        // 减去边框
        $scope.removeboder = function($event,editor){
            commonServer.removeboder($event,editor,$scope);
        }
        //初始化清除editing
        $scope.removeClassEditing = function(){
            commonServer.removeClassEditing($scope);
        }
        // 初始化右边栏
        $scope.initCartRight = function(index){
            commonServer.initCartRight(index,$scope,80);
        }
        // 优惠券添加
        $scope.addCoupon = function(position){
           commonServer.addCoupon($scope,position);
        }
        // 删除一个优惠券
        $scope.deleteCoupon = function($index){
            commonServer.deleteCoupon($scope,$index);
        }
        //crmember右侧修改背景图
        $scope.changeBg = function(){
             commonServer.changeBg($scope);
        }
        // 添加商品
        $scope.addgoods = function(position){
            commonServer.addgoods($scope,position);
        }
        //显示优惠券弹窗
        $scope.showCouponModel = function(){
            commonServer.showCouponModel($scope);
        }
        //显示model
        $scope.showModel = function(){
            commonServer.showModel($scope);
        }
        /*
        *@author huoguanghui
        *商品及分类模态框编写
        */
        // 链接选择商品和分类
        $scope.chooseShop = function($index,position){
            commonServer.showShopModel($index,position,$scope);
        }
        //切换商品及分类
        $scope.switchProductNav = function($index){
            commonServer.switchProductNav($index,$scope);
        }
        //选择营销活动搜索
        $scope.searchProductList = function(){
            commonServer.searchProductList($scope);
        }

        //图片广告选择微页面链接极其分类弹窗
        $scope.choosePageLink = function($index,position){
            commonServer.choosePageLink($index,position,$scope)
        }
        // 图片广告选择微页面链接确定
        $scope.choosePageLinkSure = function($index,list){
            commonServer.choosePageLinkSure($index,list,$scope);
        }
        //搜索微页面
        $scope.searchPage = function(){
            commonServer.searchPage($scope);
        }
        // 隐藏model
        $scope.hideModel = function(){
            commonServer.hideModel();
        }
        //选择商品
        $scope.choose = function($index,list){
            commonServer.choose($index,$scope,list);
        }
        //图片广告选择商品链接
        $scope.chooseShopLink = function($index,list){
            commonServer.chooseShopLink($index,$scope,list)
        }
        // 商品弹窗搜索
        $scope.searchGoods = function(){
            commonServer.searchGoods($scope);
        }
        //选择优惠券
        $scope.chooseCoupon = function($index,list){
            commonServer.chooseCoupon($index,list,$scope);
        }
        //确定选择商品
        $scope.chooseSure =function(){
            commonServer.chooseSure($scope);
        }
        // 选择优惠券确定按钮
        $scope.chooseCouponSure = function(){
            commonServer.chooseCouponSure($scope);
        }
        // 优惠券弹窗搜索
        $scope.searchCoupon = function(){
            commonServer.searchCoupon($scope);
        }
        //显示卡密弹窗
        $scope.showCardIdModel = function(){
            commonServer.showCardIdModel($scope);
        }
        // 卡密弹窗搜索
        $scope.searchCardId = function(){
            commonServer.searchCardId($scope);
        }
        // 选择卡密确定按钮
        $scope.chooseCardIdSure = function(){
            commonServer.chooseCardIdSure($scope);
            $('input[name="card_id"]').val($scope.camName);
            $('input[name="card_id"]').attr({"cam_id":$scope.cam_id})
        }
        //选择卡密
        $scope.chooseCardId = function($index,list){
            commonServer.chooseCardId($index,list,$scope);
        }
        //微预约添加
        $scope.chooseAppoint = function($index,position){
            commonServer.chooseAppoint($index,position,$scope);
        }
        //微预约添加确定
        $scope.chooseAppointSure = function($index,list){
            commonServer.chooseAppointSure($index,$scope,list);
        }
        //微预约弹窗搜索
        $scope.searchAppoint = function(){
            commonServer.searchAppoint($scope);
        }
        // //显示删除按钮
        // $scope.showDelete = function($index){
        //     commonServer.showDelete($index,$scope);
        // }

        // //隐藏删除按钮
        // $scope.hideDelete = function($index){
        //     commonServer.hideDelete($index,$scope);
        // }

        //删除图片
        $scope.delete = function($index){
            commonServer.delete($index,$scope);
        }

        //删除模块
        $scope.deleteAll = function($index){
            // add by 魏冬冬 2018-7-5 模块为商品详情不让删除
            if($scope.editors[$index]['type'] == 'shop_detail'){
                return;
            }
            // end
            commonServer.deleteAll($index,$scope);
        }
        // 广告图片添加
        $scope.addAdvImages = function(position){
            commonServer.addAdvImages($scope,position);
        }
        $scope.initchooseAdvImage = function(){
            commonServer.initchooseAdvImage($scope);
        }
        //点击添加广告弹出model
        $scope.addAdvs = function(){
            $scope.uploadShow = false;
            $scope.editorImg = 1;
            $scope.eventKind=1;
            commonServer.addAdvs($scope);
        }
        // 广告图片分组点击
        $scope.chooseGroup = function(grounp){
            commonServer.chooseGroup($scope,grounp);
        }
        // 选择广告图片
        $scope.chooseImage = function(image,$index){
            commonServer.chooseImage(image,$index,$scope);
        }

        //上传确定按钮
        // $scope.uploadSureBtn = function(){
        //     commonServer.chooseAdvSureBtn($scope);
        //     $('#myModal-adv').hide();
        //     $('.modal-backdrop').hide();
        //     closeUploader();
        // }
        // 返回选择图片
        $scope.showImage = function(){
            $scope.uploadShow = false; //判断上传可图片model显示
        }
        //广告图片重新上传
        $scope.reUpload = function($index){
            $scope.editorImg = 1;
            commonServer.reUpload($index,$scope);
        }
        //删除广告图片
        $scope.removeAdvImages = function($index){
            commonServer.removeAdvImages($index,$scope);
        }
        // 选择链接
        $scope.chooseLinkUrl = function($event,$index,position,url,linktype){
            commonServer.chooseLinkUrl($event,$index,position,url,linktype,$scope);
        }
        //链接删除
        $scope.removeLink = function($index,position){
            commonServer.removeLink($index,position,$scope);
        }
        // 显示dropdown
        $scope.showDown = function($index,position){
            commonServer.showDown($index,position,$scope);
        }
        //隐藏dropDown
        $scope.hideDown = function($index){
            // $scope.editors[$scope.index]['images'][$index]['dropDown'] = false;
        }
        $scope.sureProverPosition = 1;//1为广告图片的，2为标题的
        //自定义link
        $scope.customLink = function($event,$index,position){
            commonServer.customLink($event,$index,position,$scope);
        }

        $scope.sureProver = function(){
            commonServer.sureProver($scope);
        }
        $scope.cancelProver = function(){
            commonServer.cancelProver();
        }

        //添加标题
        $scope.addTitle = function(position){
            commonServer.addTitle($scope,position);
        }
        // 传统样式添加一个文本链接
        $scope.addLink = function(){
            commonServer.addLink($scope);
        }
        $scope.deleteLinkWb = function(){
            commonServer.deleteLinkWb($scope);
        }
        //绑定laydate
        $scope.bindDate = function(){
            commonServer.bindDate(start);
        }
        //添加店铺导航
        $scope.addShop = function(position){
            commonServer.addShop($scope,position);
        }
        //公告添加
        $scope.addNotice = function(position){
            commonServer.addNotice($scope,position);
        }
        // 商品添加
        $scope.addSearch = function(position){
            commonServer.addSearch($scope,position);
        }
        //商品列表添加
        $scope.addGoodsList = function(position){
            commonServer.addGoodsList($scope,position);
        }
        // 商品列表选择商品分组
         $scope.position = 0 //记录商品位置1为商品列表，2位商品分组
        $scope.chooseShopGroup = function(position){
            commonServer.addShopGroup($scope,position);
        }
        //商品分组选择确定按钮
        $scope.chooseShopGroupSure = function($index,list){
            commonServer.chooseShopGroupSure($index,list,$scope);
        }
        // 商品分组搜索
        $scope.searchShopGroup = function(){
            commonServer.searchShopGroup($scope);
        }
        // 添加自定义模块
        $scope.addModel = function(position){
            commonServer.addModel($scope,position);
        }
        // 显示自定义弹窗提示
        $scope.showComponentModel = function(){
            commonServer.showComponentModel($scope);
        }
        //自定义模块选择
        $scope.chooseComponent = function($index,list){
            commonServer.chooseComponent($scope,$index,list);
        }
        // 搜索自定义模块
        $scope.searchComponent = function(){
            commonServer.searchComponent($scope);
        }
        //商品分组添加
        $scope.addGoodGroup = function(position){
            commonServer.addGoodGroup($scope,position);
        }
        // 商品分组数量选择
        $scope.chooseNum = function(list,num){
            commonServer.chooseNum($scope,list,num);
            list.dropDown = false;
        }
        // 显示下拉选择框
        $scope.showDropDown = function(list){
            list.dropDown = true;
        }
        //选择分组确定
        $scope.chooseGroupSure = function(){
            commonServer.chooseGroupSure($scope);
        }
        // 删除一个选中分组
        $scope.deleteGroup = function($index){
            commonServer.deleteGroup($index,$scope);
        }
        // 重新选择分组
        $scope.changeGroup = function(position,list){
            commonServer.addShopGroup($scope,position,list);
        }
        // 添加图片导航
        $scope.addLinkImages = function(position){
            commonServer.addLinkImages($scope,position);
        }
        // 选择图片导航弹出model
        $scope.chooseLinkImage = function($index){
            $scope.editorImg = 1;
            commonServer.chooseLinkImage($scope,$index);
        }
         //添加文本导航
        $scope.addtextLink = function(position){
            commonServer.addtextLink($scope,position);
        }
        //添加一个文本链接
        $scope.addOneTextLink = function(){
            commonServer.addOneTextLink($scope);
        }
        //删除一个文本链接
        $scope.deleteOneTextLink = function($index){
            commonServer.deleteOneTextLink($index,$scope);
        }

        $scope.deleteLinkWb = function(){
            commonServer.deleteLinkWb($scope);
        }
        // 微页面加内容
        $scope.addContent = function(event,$index,editor,top){
            commonServer.addContent(event,$index,editor,$scope,top);
        }
        /**
         * 视频模块
         * @author huoguanghui
         */
        //新增视频模块
        $scope.addVideo =function(position){
            commonServer.addVideo($scope,position);
        };
        //打开视频弹框
        $scope.openVideoModel = function(){
           commonServer.openVideoModel($scope);
        }
        //隐藏视频弹框
        $scope.hideVideoModel = function(){
            commonServer.hideVideoModel($scope);
        }
        //选择视频
        $scope.checkedVideoItem = function(item,index){
            commonServer.checkedVideoItem($scope,item,index)
        }
        //确认试用视频
        $scope.sureUseVideo = function(){
            commonServer.sureUseVideo($scope);
        }
        //切换视频分组
        $scope.switchVideoGroup = function(item,index){
            commonServer.switchVideoGroup($scope,item,index);
        }
        //搜索功能
        $scope.videoSearch = function(e){
            commonServer.videoSearch(e);
        }
        //上传视频
        $scope.uploadVideo = function(){
            $scope.isEditor = false;
            initUploadVideo();
            $('.upload_video').show();
            $('.zent-dialog-r-backdrop').show();
        }
        //关闭上传视频弹窗
        $scope.closeUploadVideo = function(){
            $('.upload_video').hide();
            $('.zent-dialog-r-backdrop').hide();
        }
        $('#upload_video').change(function(e){
            var formData = new FormData(),
                sucFormData = {};
            var size = ($(this)[0].files[0]['size']/1204/1024).toFixed(1);
            if(size>30){
                tipshow('上传文件不能大于30M','warn');
                return;
            }
            formData.append('file', $(this)[0].files[0]);
            formData.append('_token',$('meta[name="csrf-token"]').attr('content'));
            formData.append('file_mine', 2);
            $('.rc-video-upload__progress-item-detail-name').html($(this)[0].files[0]['name']);
            $('input[name="video_name"]').val($(this)[0].files[0]['name']);
            if($(this)[0].files[0]['name'].length>10){
                $('.video_name').addClass('has-error');
            }
            // formData.append('file_mine', 2);
            var filename = hex_md5(new Date().getTime() + parseInt(10000*Math.random())+$(this)[0].files[0]['type'].split("/")[0]) + '.' + $(this)[0].files[0]['type'].split("/")[1];
            // alert(SparkMD5(filename));
            sucFormData['path'] = filename;
            sucFormData['type'] = $(this)[0].files[0]['type'];
            sucFormData['size'] = $(this)[0].files[0]['size'];
            sucFormData['_token'] = $('meta[name="csrf-token"]').attr('content'); 
            $('.add_video').hide();
            $.get('/merchants/store/getVideoSign',{save_key:filename},function(data){
                if(typeof data == 'string'){
                    data = JSON.parse(data);
                }
                formData.append('policy', data.policy);
                formData.append('authorization', data.authorization);
                // formData.append('save-key', '/huisoucn/upload/test.mp4');
                $.ajax({
                    url: 'https://v0.api.upyun.com/huisoucn',
                    type: 'POST',
                    data: formData,
                    dataType:'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    xhr:   function() {
                      var xhr = $.ajaxSettings.xhr(e);
                      if(xhr.upload){
                        $('.rc-video-upload__progress').show();
                        xhr.upload.onprogress = function (e) {
                            var position = e.position || e.loaded, total = e.totalSize || e.total;
                            // var total = event.total,
                            // position = event.loaded || event.position,
                            percent = '';
                            var html = '已上传：' + (position/1204/1024).toFixed(2) + '/' + '共' + (total/1204/1024).toFixed(2) + 'MB';
                            $('.rc-video-upload__progress-item-detail-total').html(html);
                          
                        if(event.lengthComputable){
                            // console.log(position);
                            percent = Math.ceil(position / total * 100)
                            if(percent == 100 && $('input[name="aggree_input"]').is(':checked') && !$('.video_name').hasClass('has-error')){
                                $('.zent-btn').removeAttr('disabled');
                                $('.zent-btn').addClass('zent-btn-primary')
                            }
                            $('.rc-video-upload__progress-item-progress').css('width',percent + '%');
                          }
                        };
                      }
                      return xhr;
                    }
                }).done(function(data, textStatus) {
                    // console.log(data);
                    $.post('/merchants/myfile/setUpxVideo',sucFormData,function(data){
                        if(typeof data == 'string'){
                            data = JSON.parse(data);
                        }
                        $scope.video = data.data;
                        $scope.video['FileInfo']['path'] = videoUrl + $scope.video['FileInfo']['path'];
                        $('input[name="id"]').val(data.data.id);
                        $('input[name="video_url"]').val(data.data.FileInfo.path);
                    })
                    // alert('upload success');
                }).fail(function(res, textStatus, error) {
                    try {
                        var body = JSON.parse(res.responseText);
                        alert('error: ' + body.message);
                    } catch(e) {
                        // console.error(e);
                    }
                });
            })
        })
        //重新上传视频
        $scope.reUploadVideo = function(){
            $('.rc-video-upload__progress').hide();
            $('input[name="video_name"]').val('');
            $('.add_video').show();
            $('#upload_video').val('');
            $('.video_name').removeClass('has-error');
            $('.rc-video-upload__progress-item-progress').css('width', '0px');
        }
        // //获取分组
        $.get('/merchants/myfile/getClassify',{file_mine:2},function(data){
            if(data.data.length){
                for(var i=0;i<data.data.length;i++){
                    $scope.$apply(function(){
                        $scope.grounps.push(data.data[i]);
                    })
                }
            }
        })
        //同意点击
        $('input[name="aggree_input"]').click(function(){
            if(!$scope.isEditor){
                if($(this).is(':checked') && $('#upload_video')[0].files[0] && !$('.video_name').hasClass('has-error')){
                    btnStatus(2);
                }else{
                    btnStatus(1);
                }
            }else{
                if($(this).is(':checked') && !$('.video_name').hasClass('has-error')){
                    btnStatus(2);
                }else{
                    btnStatus(1);
                }
            }
            
        })
        //商品名称失焦
        $('input[name="video_name"]').keydown(function(){
            if($(this).val().length>10){
                $('.video_name').addClass('has-error');
                btnStatus(1);
            }else{  
                $('.video_name').removeClass('has-error');
                if(!$scope.isEditor){
                    if($('input[name="aggree_input"]').is(':checked') && $('#upload_video')[0].files[0]){
                        btnStatus(2);
                    }
                }else{
                    if($('input[name="aggree_input"]').is(':checked')){
                        btnStatus(2);
                    }
                }
            }
        })
        //商品名称聚焦
        $('input[name="video_name"]').focus(function(){
            if($(this).val().length<10){
                if($('input[name="aggree_input"]').is(':checked') && $('#upload_video')[0].files[0]){
                    btnStatus(2);
                }
            }
        })
        function btnStatus(type){
            //type1为禁止点击2为允许点击
            if(type == 1){
                $('.zent-btn').attr('disabled','disabled');
                $('.zent-btn').removeClass('zent-btn-primary');
                $('.zent-btn').addClass('zent-btn-disabled');
            }else if(type == 2){
                $('.zent-btn').removeAttr('disabled','disabled');
                $('.zent-btn').addClass('zent-btn-primary');
                $('.zent-btn').removeClass('zent-btn-disabled');
            }
        }
        //封面修改
        $('#upload_image').change(function(e){
            var formData = new FormData();
            // var size = $(this)[0].files[0]['size']/1204/1024.toFixed(1);
            // console.log($(this)[0].files[0]);
            formData.append('file', $(this)[0].files[0]);
            formData.append('_token',$('meta[name="csrf-token"]').attr('content'));
            formData.append('file_mine', 1);
            $.ajax({
                url:'/merchants/myfile/upfile',
                type: 'POST',
                cache: false,
                processData: false,
                dataType:'json',
                data: formData,
                contentType: false
            }).done(function(res) {                  
                //拼接返回的视频地址，这里的vframe/jpb/offset/1是七牛的视频截取图片的接口          
                // $('.v-box').find('img').attr('src',vistUrl+res.hash+'?vframe/jpg/offset/1').show();                    
                // $('#videoForm').val(''); 
                $('.image_views').css('display','inline-block');
                $('.image_views img').attr('src',imgUrl + res.data.FileInfo.path);              
                $('input[name="image_url"]').val(res.data.FileInfo.path);
            }).fail(function(res) {
                // console.log(res);
            });
        })
        //保存视频表单
        $('.video_btn').click(function(){
            var formData = {};
            formData.name = $('input[name="video_name"]').val();
            formData.id = $('input[name="id"]').val();
            formData.classifyId = $('select[name="grounp"]').val();
            formData.file_cover = $('input[name="image_url"]').val();
            formData._token = $('meta[name="csrf-token"]').attr('content');
            // if($scope.isEditor){
            //     formData.id = $('input[name="id"]').val();
            // }
            $.post('/merchants/myfile/modifyVedio',formData,function(data){
                if(data.status == 1){
                    tipshow(data.info);
                    $('.upload_video').hide();
                    $('.zent-dialog-r-backdrop').hide();
                    // console.log($scope.video);
                    if($scope.isEditor){
                        //编辑
                        angular.forEach($scope.videos,function(val,key){
                            if(val.id == $scope.video.id){
                                $scope.$apply(function(){
                                    val.FileInfo.name = formData.name;
                                    if(formData.file_cover.indexOf(imgUrl) != -1){
                                        val.file_cover = formData.file_cover;
                                    }else{
                                        val.file_cover = imgUrl + formData.file_cover;
                                    }
                                })
                            }
                        });
                    }else{
                        // return;
                        //新增
                        $scope.active_id = 0;
                        angular.forEach($scope.grounps,function(val,key){
                            if(val.isactive){
                                $scope.active_id = val.id
                            }
                        })
                        // alert($scope.active_id);
                        // alert($scope.video.file_classify_id);
                        if($scope.active_id != $('select[name="grounp"]').val()){
                            return;
                        }
                    }
                }else{
                    tipshow(data.info,'warn');
                }
            })
        })
        //初始化上传
        function initUploadVideo(){
            $('.rc-video-upload__progress').hide();
            $('input[name="video_name"]').val('');
            $('.add_video').show();
            $('#upload_video').val('');
            $('.image_views').css('display','none');
            $('.image_views img').attr('src','');
            $('input[name="aggree_input"]').removeAttr("checked");
            $('.video_name').removeClass('has-error');
            $('.zent-btn').removeClass('zent-btn-primary');
            $('.zent-btn').attr('disabled');
            $('.rc-video-upload__progress-item-progress').css('width', '0px');
        }
        /**
         * 视频模块  end
         */
        // 拼团商品拖动
        $scope.onDropComplete = function (index, obj, evt) {
            if(obj.cardRight != undefined || obj.type != undefined ){
                return;
            }
            var otherObj = $scope.editors[$scope.index]['groups'][index];
            var otherIndex = $scope.editors[$scope.index]['groups'].indexOf(obj);
            $scope.editors[$scope.index]['groups'][index] = obj;
            $scope.editors[$scope.index]['groups'][otherIndex] = otherObj;

            var otherObj1 = $scope.editors[$scope.index]['groups_id'][index];
            var otherIndex1 = $scope.editors[$scope.index]['groups_id'].indexOf(obj.id);
            $scope.editors[$scope.index]['groups_id'][index] = obj.id;
            $scope.editors[$scope.index]['groups_id'][otherIndex] = otherObj1;
        }
        //商品组件拖动
        $scope.onDropShopComplete = function(index, obj, evt){
            if(obj.cardRight != undefined || obj.type != undefined ){
                return;
            }
            var otherObj = $scope.editors[$scope.index]['goods'][index];
            var otherIndex = $scope.editors[$scope.index]['goods'].indexOf(obj);
            $scope.editors[$scope.index]['goods'][index] = obj;
            $scope.editors[$scope.index]['goods'][otherIndex] = otherObj;
            var editor = $scope.editors[$scope.index];
            editor.thGoods = [];
            angular.forEach($scope.editors[$scope.index]['goods'],function(val,key){
                if(editor.thGoods.length > 0){
                    if(editor.thGoods[editor.thGoods.length - 1].length>=3){
                        editor['thGoods'].push([]);
                        editor['thGoods'][editor.thGoods.length-1].push(val)
                    }else{
                        editor.thGoods[editor.thGoods.length - 1].push(val)
                    }
                }else{
                    editor.thGoods[0] = [];
                    editor.thGoods[0].push(val)
                }
            })
        }
        //微页面组件拖动
        $scope.onDropPageComplete = function(index, obj, evt){
            // add by 魏冬冬 2018-7-5 不能拖动到商品详情上
            if(index == 0 && $scope.editors[index]['type'] == 'shop_detail'){
                return;
            }
            //end
            if(obj.cardRight == undefined && obj.type == undefined){
                return;
            }
            var otherObj = $scope.editors[index];
            var otherIndex = $scope.editors.indexOf(obj);
            $scope.editors[index] = obj;
            $scope.editors[otherIndex] = otherObj;
            $scope.initCartRight();
        }
        //广告图片拖动
        $scope.onDropAdvsComplete = function(index, obj, evt){
            if(obj.cardRight != undefined || obj.type != undefined ){
                return;
            }
            var otherObj = $scope.editors[$scope.index]['images'][index];
            var otherIndex = $scope.editors[$scope.index]['images'].indexOf(obj);
            $scope.editors[$scope.index]['images'][index] = obj;
            $scope.editors[$scope.index]['images'][otherIndex] = otherObj;
        }
        //商品图片拖动
        $scope.onDropShopImageComplete = function(index, obj, evt){
            var otherObj = $scope.goodsinfo.img[index];
            var otherIndex = $scope.goodsinfo.img.indexOf(obj);
            $scope.goodsinfo.img[index] = obj;
            $scope.goodsinfo.img[otherIndex] = otherObj;
        }
        $('.discount input').click(function(){
            if($(this).is(':checked')){
                $scope.setEms.is_discount = 1
            }else{
                $scope.setEms.is_discount = 0
            }
        })
        //重量模板大小限制
        // $scope.weightLimit = function(){
        //     console.log($scope.setEms.weight)
        //     if($scope.setEms.isWeightTel && $scope.specs.length==0){//判断是否是没有规格的重量模板
        //         if($scope.setEms.weight <= 0){

        //         }
        //     }
        // }

        //监听temp有没有数据显示按钮
        $scope.$watch("temp",function(newVal,oldVal){
            if($scope.temp.length==0){
                $scope.tempSure = false;
            }else{
                $scope.tempSure = true;
            }
        },true)

        $scope.$watch("tempUploadImage",function(newVal,oldVal){
            if($scope.tempUploadImage.length==0){
                $scope.chooseSureBtn = false;
            }else{
                $scope.chooseSureBtn = true;
            }
        },true)
        $scope.$watch("step",function(newVal,oldVal){
            // if(newVal ==2){
            //     alert(2);
            //     $timeout(function(){
            //         var ue = initUeditor('editor');
            //         ue.addListener("selectionchange", function () {
            //             var content = ue.getContent();
            //             $scope.setEms.introduce = content;
            //         });
            //     })
            // }
            // if(newVal ==3){
            //     $timeout(function(){
            //         var ue = initUeditor('editor');
            //         ue.addListener("selectionchange", function () {
            //             var content = ue.getContent();
            //             $scope.setEms.introduce = content;
            //         });
            //     })
            // }
        })
        // 监听库存变化
        $scope.$watch("specs",function(newVal,oldVal){
            var item,item2;
            if (isChange) {
                if(newVal.length==oldVal.length){//编辑    update by 倪凯嘉 2019-1-28
                    for (var i = 0; i< newVal.length; i++) {
                        item = newVal[i];
                        for (var j=0; j< oldVal.length; j++){
                            item2 = oldVal[j];
                            if (item2.price !== undefined) {
                                item.price = item2.price;
                            }
                            if (item2.stock_num !== undefined) {
                                item.stock_num = item2.stock_num;
                            }
                            if (item2.code !== undefined) {
                                item.code = item2.code;
                            }
                            if (item2.weight !== undefined) {
                                item.weight = item2.weight;
                            }
                            if (item2.sold_num !== undefined) {
                                item.sold_num = item2.sold_num;
                            }
                        }
                    }
                }else{//新建 删除
                    for (var i = 0; i< newVal.length; i++) {
                        item = newVal[i];
                        for (var j=0; j< oldVal.length; j++){
                            item2 = oldVal[j];
                            if ((item.v1 == item2.v1 && item.v2 == item2.v2 && item.v3 == item2.v3) || (item.v1 == item2.v1 && item.v2 == undefined && item2.v2 =="" && item.v3 == undefined && item2.v3 == "") || (item.v1 == item2.v1 && item.v2 == item2.v2 && item.v3 == undefined && item2.v3 =="")){
                                if (item2.price !== undefined) {
                                    item.price = item2.price;
                                }
                                if (item2.stock_num !== undefined) {
                                    item.stock_num = item2.stock_num;
                                }
                                if (item2.code !== undefined) {
                                    item.code = item2.code;
                                }
                                if (item2.weight !== undefined) {
                                    item.weight = item2.weight;
                                }
                                if (item2.sold_num !== undefined) {
                                    item.sold_num = item2.sold_num;
                                }
                            }
                            
                        }
                    }
                }
            }
            isChange = false;
            $scope.goodsinfo.stock = 0;
            $scope.goodsinfo.sold_num = 0;
            angular.forEach($scope.specs,function(val,key){
                $scope.goodsinfo.stock = parseInt($scope.goodsinfo.stock) + parseInt(val.stock_num);
                $scope.goodsinfo.sold_num = parseInt($scope.goodsinfo.sold_num) + parseInt(val.sold_num);
            })
            $scope.tatolInvebtory();
        },'true')
        //是否使用统一模板
        $scope.$watch('setEms.freight_type',function(newVal,oldVal){
            if(newVal == 1){//统一运费隐藏重量模板显示
                $scope.setEms.isWeightTel = false;
            }else{
                angular.forEach($scope.setEms.freight_id,function(data,index,array){
                    if(data.id == $scope.postData.freight_id){
                        if(data.billing_type == 1){//选中重量模板
                            $scope.setEms.isWeightTel = true;
                        }else{//选中普通模板
                            $scope.setEms.isWeightTel = false;
                        }

                    }
                    if(!newVal){//选择请选择
                        $scope.setEms.isWeightTel = false;
                    }

                })
            }
        });
         /**
         * 是否使用重量模板
         */
        $scope.$watch('postData.freight_id',function(newVal,oldVal){
            if(newVal){
                $scope.setEms.freight_type = 2;
            }
            angular.forEach($scope.setEms.freight_id,function(data,index,array){
                if(data.id == newVal){
                    if(data.billing_type == 1){//选中重量模板
                        $scope.setEms.isWeightTel = true;
                    }else{//选中普通模板
                        $scope.setEms.isWeightTel = false;
                    }
                }
                if(!newVal){//选择请选择
                    $scope.setEms.isWeightTel = false;
                }
            }) 
        });

        uploader.on('uploadSuccess', function (file, response) {
            // console.log(333)
            if (response.status == 1) {
                $scope.$apply(function () {
                    response.data['FileInfo']['path'] = imgUrl + response.data['FileInfo']['path'];
                    $scope.tempUploadImage.unshift(response.data);
                })
                if($scope.eventKind == 1){ 
                    $scope.goodsinfo.img.push(response.data);
                }
                if($scope.eventKind == 2){
                    $scope.upSpecImg = response.data;
                }
            } 
        });
        /**
         * @auther 邓钊
         * @desc 改变商品库存是否显示状态
         * @date 2018-9-5
         * @return
         * */
        $scope.stock_check = function () {
            if($scope.postData.stock_show == 1){
                $scope.postData.stock_show = "0"
            }else if($scope.postData.stock_show == 0){
                $scope.postData.stock_show = "1"
            }

        }
    
    });
    app.directive('fileModel', ['$parse', function ($parse) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs, ngModel) {
                var model = $parse(attrs.fileModel);
                var modelSetter = model.assign;
                element.bind('change', function(event){
                    scope.$apply(function(){
                        modelSetter(scope, element[0].files[0]);
                    });
                    //附件预览
                    scope.file = (event.srcElement || event.target).files[0];
                    scope.getFile(scope.file);
                });
            }
        };
    }]);  
    // 自定义验证表单验证字段唯一
    app.directive('ensureUnique', ['$timeout',function($timeout) {
        return {
            restrict:"AE",
            require: 'ngModel',
            link: function(scope, ele, attrs, ngModel) {
                ele.bind('blur',function(){  
                    $timeout(function(){
                        ngModel.$setValidity('unique', true);
                    },100)
                    var i = 0;
                    angular.forEach(scope.setEms.noteList,function(val,key){
                        if(ele[0].value == val.title){
                            if(i>=1){
                                 $timeout(function(){
                                    ngModel.$setValidity('unique', false);
                                },100)
                                 return;
                            }
                           i++;
                        }
                    })
                });  
            }
        }
    }]);
    //自定义表单验证正数验证
    app.directive('ensureInteger1',function($http) {
        return {
            require: 'ngModel',
            link: function(scope, ele, attrs, c) {
                scope.$watch(attrs.ngModel,
                    function(n) {
                        if (n > 0){ 
                            c.$setValidity('integer1',true);//修改验证结果值
                        }else{ 
                            c.$setValidity('integer1',false);
                        }
                    }
                );

            }

        }

    });
    //物流添加字段多行禁用
    $(document).on("change",".log-select",function(){
    	$(this).next("input").attr("disabled",false).next("span").text("多行")
    	if($(this).find("option:selected").val()=="tel"){
			$(this).next("input").attr("disabled",true)
		}
    	if($(this).find("option:selected").val()=="email"){
			$(this).next("input").attr("disabled",true)
		}
    	if($(this).find("option:selected").val()=="date"){
			$(this).next("input").attr("disabled",true)
		}
    	if($(this).find("option:selected").val()=="id_no"){
			$(this).next("input").attr("disabled",true)
		}
    	if($(this).find("option:selected").val()=="image"){
			$(this).next("input").attr("disabled",true)
		}
    	if($(this).find("option:selected").val()=="text"){
			$(this).next("input").attr("disabled",false)
		}
    	if($(this).find("option:selected").val()=="time"){
			$(this).next("input").attr("disabled",false).next("span").text("含日期")
		}
    })
	
	//选择虚拟物品显示虚拟商品管理规范
	$("input[name='xuni']").click(function(){
		if ($(this).is(':checked')) {
			$(".js-virtual-goods-rules").removeClass("hide")
		}
	})
	$("input[name='shipment']").click(function(){
		$("input[name='xuni']").removeAttr('checked')
		$(".js-virtual-goods-rules").addClass("hide")
    })
    
    // 复制链接
    $('body').on('click','.copyPathBtn',function(e){
        e.stopPropagation();//组织事件冒泡
        var obj = $(this).siblings('.copyContent');
        copyToClipboard( obj );
        tipshow('复制成功','info');
    });


    function GetCardList(){
        $.ajax({
            url:'/merchants/linkTo/get?type=20&wid=42',
            data:{
                type:2,
                wid:wid
            },  
            type:'get',
            success:function(res){
                console.log(res)
            }
        })
    }
    GetCardList();

    // 模态框添加分组
    $(".btn_left").on('click', function () {
        var name = $('.add_group_input').val();
        if(!name){
            return false
        }
        $.ajax({
            url:'/merchants/myfile/addClassify',
            type: 'POST',
            data:{
                name:name,
                _token:_token,
            },
            success:function (data) {
                console.log(data);
                if(data.status == 1){
                    var _group = '<li class="js-category-item" data-id="'+data.data.id+'">'+data.data.name+'\
                                <span>0</span>\
                            </li>';
                    $('.category-list').append(_group);
                    $(".add_group_box").addClass('hide')
                }
            }
        })
    }) 
    
    //添加分组 by 崔源 2018.10.22
    $(".btn_right").on('click',function () {
        $(".add_group_box").addClass('hide')
        $('.add_group_list').attr('data-id','1');
    })
    $(".add_group_list").on('click',function () {
        var id = $(this).attr('data-id');
        if(id == 1){
            $(this).attr('data-id','2');
            $(".add_group_box").removeClass('hide')
        }else {
            $(this).attr('data-id','1');
            $(".add_group_box").addClass('hide')
        }
    });
    

    $()