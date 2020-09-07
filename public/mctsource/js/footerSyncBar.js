/**
 * @author  huoguanghui
 * @created by 2017年12月12日17:18:58
 */
//模态框居中控制
$('.modal').on('shown.bs.modal', function (e) { 
    // 关键代码，如没将modal设置为 block，则$modala_dialog.height() 为零 
    $(this).css('display', 'block'); 
    var modalHeight=$(window).height() / 2 - $(this).find('.modal-dialog').height() / 2; 
    if(modalHeight < 0){
        modalHeight = 0;
    }
    $(this).find('.modal-dialog').css({ 
        'margin-top': modalHeight 
    });

});

var app = angular.module('myApp', []);
app.controller('myCtrl',function($scope,$http,$timeout){
    // 数据开始
    $scope.isBinding = 0;//是否绑定小程序 0否 1 是
    $scope.is_auth_submit = 0;//是否开启自动提交  0 否 1 是
    $scope.btnSubmit  = true;//按钮提交  防止多点
    $scope._host = _host;//静态图片域名
    $scope.host = host;//网站域名
    $scope.imgUrl = imgUrl;//动态图片域名
    $scope.iconGroupList = [//icon列表
        {
            "text":"首页",//标题
            "iconPath": "mctsource/images/footerBar/home-unselected.png",//未选中图片
            "selectedIconPath": "mctsource/images/footerBar/home-selected.png"//选中图片
        },
        {
            "text":"购物车",
            "iconPath": "mctsource/images/footerBar/car-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/car-selected.png"
        },
        {
            "text":"一键参团",
            "iconPath": "mctsource/images/footerBar/pintuan-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/pintuan-selected.png"
        },
        {
            "text":"我的",
            "iconPath": "mctsource/images/footerBar/my-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/my-selected.png"
        },
        {
            "text":"店铺活动",
            "iconPath": "mctsource/images/footerBar/actived-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/actived-selected.png"
        },
        {
            "text":"最佳推荐",
            "iconPath": "mctsource/images/footerBar/best-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/best-selected.png"
        },
        {
            "text":"分类",
            "iconPath": "mctsource/images/footerBar/classify-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/classify-selected.png"
        },
        {
            "text":"特惠专区",
            "iconPath": "mctsource/images/footerBar/odds-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/odds-selected.png"
        },
        {
            "text":"好物优选",
            "iconPath": "mctsource/images/footerBar/goods-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/goods-selected.png"
        },
        {
            "text":"发现",
            "iconPath": "mctsource/images/footerBar/find-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/find-selected.png"
        },
        {
            "text":"人气特卖",
            "iconPath": "mctsource/images/footerBar/hot-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/hot-selected.png"
        },
        {
            "text":"必买清单",
            "iconPath": "mctsource/images/footerBar/menu-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/menu-selected.png"
        },
        {
            "text":"品牌特卖",
            "iconPath": "mctsource/images/footerBar/brand-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/brand-selected.png"
        },
        {
            "text":"热卖商城",
            "iconPath": "mctsource/images/footerBar/mail-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/mail-selected.png"
        },
        {
            "text":"联系我们",
            "iconPath": "mctsource/images/footerBar/relation_old.png",
            "selectedIconPath": "mctsource/images/footerBar/relation_new.png"
        },
        {
            "text":"客服",
            "iconPath": "mctsource/images/footerBar/kefu-unselected.png",
            "selectedIconPath": "mctsource/images/footerBar/kefu-selected.png"
        }
    ];
    $scope.iconGroupLists = [
        [//icon列表
            {
                "text":"首页",//标题
                "iconPath": "mctsource/images/footerBar/home-unselected.png",//未选中图片
                "selectedIconPath": "mctsource/images/footerBar/home-selected.png"//选中图片
            },
            {
                "text":"购物车",
                "iconPath": "mctsource/images/footerBar/car-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/car-selected.png"
            },
            {
                "text":"一键参团",
                "iconPath": "mctsource/images/footerBar/pintuan-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/pintuan-selected.png"
            },
            {
                "text":"我的",
                "iconPath": "mctsource/images/footerBar/my-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/my-selected.png"
            },
            {
                "text":"店铺活动",
                "iconPath": "mctsource/images/footerBar/actived-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/actived-selected.png"
            },
            {
                "text":"最佳推荐",
                "iconPath": "mctsource/images/footerBar/best-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/best-selected.png"
            },
            {
                "text":"分类",
                "iconPath": "mctsource/images/footerBar/classify-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/classify-selected.png"
            },
            {
                "text":"特惠专区",
                "iconPath": "mctsource/images/footerBar/odds-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/odds-selected.png"
            },
            {
                "text":"好物优选",
                "iconPath": "mctsource/images/footerBar/goods-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/goods-selected.png"
            },
            {
                "text":"发现",
                "iconPath": "mctsource/images/footerBar/find-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/find-selected.png"
            },
            {
                "text":"人气特卖",
                "iconPath": "mctsource/images/footerBar/hot-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/hot-selected.png"
            },
            {
                "text":"必买清单",
                "iconPath": "mctsource/images/footerBar/menu-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/menu-selected.png"
            },
            {
                "text":"品牌特卖",
                "iconPath": "mctsource/images/footerBar/brand-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/brand-selected.png"
            },
            {
                "text":"热卖商城",
                "iconPath": "mctsource/images/footerBar/mail-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/mail-selected.png"
            },
            //不要删除了  有用   by 邓钊 2018-7-24
            {
                "text":"门店",
                "iconPath": "mctsource/images/footerBar/relation_old.png",
                "selectedIconPath": "mctsource/images/footerBar/relation_new.png"
            },
            {
                "text":"客服",
                "iconPath": "mctsource/images/footerBar/kefu-unselected.png",
                "selectedIconPath": "mctsource/images/footerBar/kefu-selected.png"
            }
        ],
        [//icon列表
            {
                "text":"首页",//标题
                "iconPath": "mctsource/images/footerBar/home-unselected_1.png",//未选中图片
                "selectedIconPath": "mctsource/images/footerBar/home-selected_1.png"//选中图片
            },
            {
                "text":"购物车",
                "iconPath": "mctsource/images/footerBar/car-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/car-selected_1.png"
            },
            {
                "text":"一键参团",
                "iconPath": "mctsource/images/footerBar/pintuan-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/pintuan-selected_1.png"
            },
            {
                "text":"我的",
                "iconPath": "mctsource/images/footerBar/my-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/my-selected_1.png"
            },
            {
                "text":"店铺活动",
                "iconPath": "mctsource/images/footerBar/actived-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/actived-selected_1.png"
            },
            {
                "text":"最佳推荐",
                "iconPath": "mctsource/images/footerBar/best-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/best-selected_1.png"
            },
            {
                "text":"分类",
                "iconPath": "mctsource/images/footerBar/classify-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/classify-selected_1.png"
            },
            {
                "text":"特惠专区",
                "iconPath": "mctsource/images/footerBar/odds-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/odds-selected_1.png"
            },
            {
                "text":"好物优选",
                "iconPath": "mctsource/images/footerBar/goods-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/goods-selected_1.png"
            },
            {
                "text":"发现",
                "iconPath": "mctsource/images/footerBar/find-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/find-selected_1.png"
            },
            {
                "text":"人气特卖",
                "iconPath": "mctsource/images/footerBar/hot-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/hot-selected_1.png"
            },
            {
                "text":"必买清单",
                "iconPath": "mctsource/images/footerBar/menu-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/menu-selected_1.png"
            },
            {
                "text":"品牌特卖",
                "iconPath": "mctsource/images/footerBar/brand-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/brand-selected_1.png"
            },
            {
                "text":"热卖商城",
                "iconPath": "mctsource/images/footerBar/mail-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/mail-selected_1.png"
            },
            //不要删除了  有用   by 邓钊 2018-7-24
            {
                "text":"门店",
                "iconPath": "mctsource/images/footerBar/relation_old_1.png",
                "selectedIconPath": "mctsource/images/footerBar/relation_new_1.png"
            },
            {
                "text":"客服",
                "iconPath": "mctsource/images/footerBar/kefu-unselected_1.png",
                "selectedIconPath": "mctsource/images/footerBar/kefu-selected_1.png"
            }
        ],
        [//icon列表
            {
                "text":"首页",//标题
                "iconPath": "mctsource/images/footerBar/home-unselected_2.png",//未选中图片
                "selectedIconPath": "mctsource/images/footerBar/home-selected_2.png"//选中图片
            },
            {
                "text":"购物车",
                "iconPath": "mctsource/images/footerBar/car-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/car-selected_2.png"
            },
            {
                "text":"一键参团",
                "iconPath": "mctsource/images/footerBar/pintuan-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/pintuan-selected_2.png"
            },
            {
                "text":"我的",
                "iconPath": "mctsource/images/footerBar/my-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/my-selected_2.png"
            },
            {
                "text":"店铺活动",
                "iconPath": "mctsource/images/footerBar/actived-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/actived-selected_2.png"
            },
            {
                "text":"最佳推荐",
                "iconPath": "mctsource/images/footerBar/best-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/best-selected_2.png"
            },
            {
                "text":"分类",
                "iconPath": "mctsource/images/footerBar/classify-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/classify-selected_2.png"
            },
            {
                "text":"特惠专区",
                "iconPath": "mctsource/images/footerBar/odds-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/odds-selected_2.png"
            },
            {
                "text":"好物优选",
                "iconPath": "mctsource/images/footerBar/goods-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/goods-selected_2.png"
            },
            {
                "text":"发现",
                "iconPath": "mctsource/images/footerBar/find-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/find-selected_2.png"
            },
            {
                "text":"人气特卖",
                "iconPath": "mctsource/images/footerBar/hot-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/hot-selected_2.png"
            },
            {
                "text":"必买清单",
                "iconPath": "mctsource/images/footerBar/menu-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/menu-selected_2.png"
            },
            {
                "text":"品牌特卖",
                "iconPath": "mctsource/images/footerBar/brand-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/brand-selected_2.png"
            },
            {
                "text":"热卖商城",
                "iconPath": "mctsource/images/footerBar/mail-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/mail-selected_2.png"
            },
            {
                "text":"门店",
                "iconPath": "mctsource/images/footerBar/relation_old_2.png",
                "selectedIconPath": "mctsource/images/footerBar/relation_new_2.png"
            },
            {
                "text":"客服",
                "iconPath": "mctsource/images/footerBar/kefu-unselected_2.png",
                "selectedIconPath": "mctsource/images/footerBar/kefu-selected_2.png"
            }
        ],
        [//icon列表
            {
                "text":"首页",//标题
                "iconPath": "mctsource/images/footerBar/home-unselected_3.png",//未选中图片
                "selectedIconPath": "mctsource/images/footerBar/home-selected_3.png"//选中图片
            },
            {
                "text":"购物车",
                "iconPath": "mctsource/images/footerBar/car-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/car-selected_3.png"
            },
            {
                "text":"一键参团",
                "iconPath": "mctsource/images/footerBar/pintuan-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/pintuan-selected_3.png"
            },
            {
                "text":"我的",
                "iconPath": "mctsource/images/footerBar/my-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/my-selected_3.png"
            },
            {
                "text":"店铺活动",
                "iconPath": "mctsource/images/footerBar/actived-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/actived-selected_3.png"
            },
            {
                "text":"最佳推荐",
                "iconPath": "mctsource/images/footerBar/best-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/best-selected_3.png"
            },
            {
                "text":"分类",
                "iconPath": "mctsource/images/footerBar/classify-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/classify-selected_3.png"
            },
            {
                "text":"特惠专区",
                "iconPath": "mctsource/images/footerBar/odds-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/odds-selected_3.png"
            },
            {
                "text":"好物优选",
                "iconPath": "mctsource/images/footerBar/goods-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/goods-selected_3.png"
            },
            {
                "text":"发现",
                "iconPath": "mctsource/images/footerBar/find-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/find-selected_3.png"
            },
            {
                "text":"人气特卖",
                "iconPath": "mctsource/images/footerBar/hot-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/hot-selected_3.png"
            },
            {
                "text":"必买清单",
                "iconPath": "mctsource/images/footerBar/menu-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/menu-selected_3.png"
            },
            {
                "text":"品牌特卖",
                "iconPath": "mctsource/images/footerBar/brand-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/brand-selected_3.png"
            },
            {
                "text":"热卖商城",
                "iconPath": "mctsource/images/footerBar/mail-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/mail-selected_3.png"
            },
            {
                "text":"门店",
                "iconPath": "mctsource/images/footerBar/relation_old_3.png",
                "selectedIconPath": "mctsource/images/footerBar/relation_new_3.png"
            },
            {
                "text":"客服",
                "iconPath": "mctsource/images/footerBar/kefu-unselected_3.png",
                "selectedIconPath": "mctsource/images/footerBar/kefu-selected_3.png"
            }
        ],
        [//icon列表
            {
                "text":"首页",//标题
                "iconPath": "mctsource/images/footerBar/home-unselected_4.png",//未选中图片
                "selectedIconPath": "mctsource/images/footerBar/home-selected_4.png"//选中图片
            },
            {
                "text":"购物车",
                "iconPath": "mctsource/images/footerBar/car-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/car-selected_4.png"
            },
            {
                "text":"一键参团",
                "iconPath": "mctsource/images/footerBar/pintuan-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/pintuan-selected_4.png"
            },
            {
                "text":"我的",
                "iconPath": "mctsource/images/footerBar/my-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/my-selected_4.png"
            },
            {
                "text":"店铺活动",
                "iconPath": "mctsource/images/footerBar/actived-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/actived-selected_4.png"
            },
            {
                "text":"最佳推荐",
                "iconPath": "mctsource/images/footerBar/best-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/best-selected_4.png"
            },
            {
                "text":"分类",
                "iconPath": "mctsource/images/footerBar/classify-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/classify-selected_4.png"
            },
            {
                "text":"特惠专区",
                "iconPath": "mctsource/images/footerBar/odds-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/odds-selected_4.png"
            },
            {
                "text":"好物优选",
                "iconPath": "mctsource/images/footerBar/goods-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/goods-selected_4.png"
            },
            {
                "text":"发现",
                "iconPath": "mctsource/images/footerBar/find-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/find-selected_4.png"
            },
            {
                "text":"人气特卖",
                "iconPath": "mctsource/images/footerBar/hot-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/hot-selected_4.png"
            },
            {
                "text":"必买清单",
                "iconPath": "mctsource/images/footerBar/menu-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/menu-selected_4.png"
            },
            {
                "text":"品牌特卖",
                "iconPath": "mctsource/images/footerBar/brand-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/brand-selected_4.png"
            },
            {
                "text":"热卖商城",
                "iconPath": "mctsource/images/footerBar/mail-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/mail-selected_4.png"
            },
            {
                "text":"门店",
                "iconPath": "mctsource/images/footerBar/relation_old_4.png",
                "selectedIconPath": "mctsource/images/footerBar/relation_new_4.png"
            },
            {
                "text":"客服",
                "iconPath": "mctsource/images/footerBar/kefu-unselected_4.png",
                "selectedIconPath": "mctsource/images/footerBar/kefu-selected_4.png"
            }
        ],
        [//icon列表
            {
                "text":"首页",//标题
                "iconPath": "mctsource/images/footerBar/home-unselected_5.png",//未选中图片
                "selectedIconPath": "mctsource/images/footerBar/home-selected_5.png"//选中图片
            },
            {
                "text":"购物车",
                "iconPath": "mctsource/images/footerBar/car-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/car-selected_5.png"
            },
            {
                "text":"一键参团",
                "iconPath": "mctsource/images/footerBar/pintuan-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/pintuan-selected_5.png"
            },
            {
                "text":"我的",
                "iconPath": "mctsource/images/footerBar/my-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/my-selected_5.png"
            },
            {
                "text":"店铺活动",
                "iconPath": "mctsource/images/footerBar/actived-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/actived-selected_5.png"
            },
            {
                "text":"最佳推荐",
                "iconPath": "mctsource/images/footerBar/best-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/best-selected_5.png"
            },
            {
                "text":"分类",
                "iconPath": "mctsource/images/footerBar/classify-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/classify-selected_5.png"
            },
            {
                "text":"特惠专区",
                "iconPath": "mctsource/images/footerBar/odds-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/odds-selected_5.png"
            },
            {
                "text":"好物优选",
                "iconPath": "mctsource/images/footerBar/goods-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/goods-selected_5.png"
            },
            {
                "text":"发现",
                "iconPath": "mctsource/images/footerBar/find-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/find-selected_5.png"
            },
            {
                "text":"人气特卖",
                "iconPath": "mctsource/images/footerBar/hot-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/hot-selected_5.png"
            },
            {
                "text":"必买清单",
                "iconPath": "mctsource/images/footerBar/menu-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/menu-selected_5.png"
            },
            {
                "text":"品牌特卖",
                "iconPath": "mctsource/images/footerBar/brand-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/brand-selected_5.png"
            },
            {
                "text":"热卖商城",
                "iconPath": "mctsource/images/footerBar/mail-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/mail-selected_5.png"
            },
            {
                "text":"门店",
                "iconPath": "mctsource/images/footerBar/relation_old_5.png",
                "selectedIconPath": "mctsource/images/footerBar/relation_new_5.png"
            },
            {
                "text":"客服",
                "iconPath": "mctsource/images/footerBar/kefu-unselected_5.png",
                "selectedIconPath": "mctsource/images/footerBar/kefu-selected_5.png"
            }
        ],
        [//icon列表
            {
                "text":"首页",//标题
                "iconPath": "mctsource/images/footerBar/home-unselected_6.png",//未选中图片
                "selectedIconPath": "mctsource/images/footerBar/home-selected_6.png"//选中图片
            },
            {
                "text":"购物车",
                "iconPath": "mctsource/images/footerBar/car-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/car-selected_6.png"
            },
            {
                "text":"一键参团",
                "iconPath": "mctsource/images/footerBar/pintuan-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/pintuan-selected_6.png"
            },
            {
                "text":"我的",
                "iconPath": "mctsource/images/footerBar/my-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/my-selected_6.png"
            },
            {
                "text":"店铺活动",
                "iconPath": "mctsource/images/footerBar/actived-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/actived-selected_6.png"
            },
            {
                "text":"最佳推荐",
                "iconPath": "mctsource/images/footerBar/best-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/best-selected_6.png"
            },
            {
                "text":"分类",
                "iconPath": "mctsource/images/footerBar/classify-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/classify-selected_6.png"
            },
            {
                "text":"特惠专区",
                "iconPath": "mctsource/images/footerBar/odds-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/odds-selected_6.png"
            },
            {
                "text":"好物优选",
                "iconPath": "mctsource/images/footerBar/goods-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/goods-selected_6.png"
            },
            {
                "text":"发现",
                "iconPath": "mctsource/images/footerBar/find-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/find-selected_6.png"
            },
            {
                "text":"人气特卖",
                "iconPath": "mctsource/images/footerBar/hot-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/hot-selected_6.png"
            },
            {
                "text":"必买清单",
                "iconPath": "mctsource/images/footerBar/menu-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/menu-selected_6.png"
            },
            {
                "text":"品牌特卖",
                "iconPath": "mctsource/images/footerBar/brand-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/brand-selected_6.png"
            },
            {
                "text":"热卖商城",
                "iconPath": "mctsource/images/footerBar/mail-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/mail-selected_6.png"
            },
            {
                "text":"门店",
                "iconPath": "mctsource/images/footerBar/relation_old_6.png",
                "selectedIconPath": "mctsource/images/footerBar/relation_new_6.png"
            },
            {
                "text":"客服",
                "iconPath": "mctsource/images/footerBar/kefu-unselected_6.png",
                "selectedIconPath": "mctsource/images/footerBar/kefu-selected_6.png"
            }
        ],
        [//icon列表
            {
                "text":"首页",//标题
                "iconPath": "mctsource/images/footerBar/home-unselected_7.png",//未选中图片
                "selectedIconPath": "mctsource/images/footerBar/home-selected_7.png"//选中图片
            },
            {
                "text":"购物车",
                "iconPath": "mctsource/images/footerBar/car-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/car-selected_7.png"
            },
            {
                "text":"一键参团",
                "iconPath": "mctsource/images/footerBar/pintuan-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/pintuan-selected_7.png"
            },
            {
                "text":"我的",
                "iconPath": "mctsource/images/footerBar/my-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/my-selected_7.png"
            },
            {
                "text":"店铺活动",
                "iconPath": "mctsource/images/footerBar/actived-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/actived-selected_7.png"
            },
            {
                "text":"最佳推荐",
                "iconPath": "mctsource/images/footerBar/best-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/best-selected_7.png"
            },
            {
                "text":"分类",
                "iconPath": "mctsource/images/footerBar/classify-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/classify-selected_7.png"
            },
            {
                "text":"特惠专区",
                "iconPath": "mctsource/images/footerBar/odds-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/odds-selected_7.png"
            },
            {
                "text":"好物优选",
                "iconPath": "mctsource/images/footerBar/goods-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/goods-selected_7.png"
            },
            {
                "text":"发现",
                "iconPath": "mctsource/images/footerBar/find-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/find-selected_7.png"
            },
            {
                "text":"人气特卖",
                "iconPath": "mctsource/images/footerBar/hot-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/hot-selected_7.png"
            },
            {
                "text":"必买清单",
                "iconPath": "mctsource/images/footerBar/menu-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/menu-selected_7.png"
            },
            {
                "text":"品牌特卖",
                "iconPath": "mctsource/images/footerBar/brand-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/brand-selected_7.png"
            },
            {
                "text":"热卖商城",
                "iconPath": "mctsource/images/footerBar/mail-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/mail-selected_7.png"
            },
            //不要删除了  有用   by 邓钊 2018-7-24
            {
                "text":"门店",
                "iconPath": "mctsource/images/footerBar/relation_old_7.png",
                "selectedIconPath": "mctsource/images/footerBar/relation_new_7.png"
            },
            {
                "text":"客服",
                "iconPath": "mctsource/images/footerBar/kefu-unselected_7.png",
                "selectedIconPath": "mctsource/images/footerBar/kefu-selected_7.png"
            }
        ],
    ]
    $scope.selectList = [
        {'title':'请选择','type':''},
        {'title':'一键参团','type':'group'},
        {'title':'微页面及分类','type':'page'},
        {'title':'购物车','type':'cart'},
        {'title':'我的','type':'home'},
        {'title':'联系我们','type':'relation'},
        {'title':'客服','type':'wechat'}
    ]
    $scope.index = '';
    $scope.eachData = function(type){
        var flag = 0;
        angular.forEach($scope.tabBarList,function(val,key){
            if(val.urlTitle == type){
                flag++;
            }
        })
        return flag;
    }
    $scope.selectChange = function($index){
        $scope.index = $index;
        if($scope.tabBarList[$index]['grade'] == 'page'){
            var count = 0;
            angular.forEach($scope.tabBarList,function(val,key){
                if(val.pageId > 0){
                    count++;
                }
            })
            if(count >= 3){
                tipshow('最多只能添加三个微页面','warn');
                $scope.tabBarList[$index]['grade'] = '';
                return;
            }
            $scope.openPageModal($index);
        }
        if($scope.tabBarList[$index]['grade'] == 'group'){
            if($scope.eachData('一键参团') >= 1){
                tipshow('您已设置“一键参团”，请勿重复添加','warn');
                $scope.tabBarList[$index]['grade'] = '';
                return;
            }
            $scope.addNav(2);
        }
        if($scope.tabBarList[$index]['grade'] == 'cart'){
            if($scope.eachData('购物车') >= 1){
                tipshow('您已设置“购物车”，请勿重复添加','warn');
                $scope.tabBarList[$index]['grade'] = '';
                return;
            }
            $scope.addNav(3);
        }
        if($scope.tabBarList[$index]['grade'] == 'index'){
            if($scope.eachData('首页') >= 1){
                tipshow('您已设置“首页”，请勿重复添加','warn');
                $scope.tabBarList[$index]['grade'] = '';
                return;
            }
            $scope.addNav(4);
        }
        if($scope.tabBarList[$index]['grade'] == 'home'){
            if($scope.eachData('我的') >= 1){
                tipshow('您已设置“我的”，请勿重复添加','warn');
                $scope.tabBarList[$index]['grade'] = '';
                return;
            }
            layer.open({
              content: '选择此链接需要商家微信支付开通企业付款功能，请确认是否已开通'
              ,btn: ['确认', '取消']
              ,yes: function(index, layero){
                //按钮【按钮一】的回调
                // return true;
                $scope.addNav(5);

                layer.close(index)
              }
              ,cancel: function(){ 
                //右上角关闭回调
                //return false 开启该代码可禁止点击该按钮关闭
              }
            }); 
        }
        if($scope.tabBarList[$index]['grade'] == 'relation'){
            if($scope.eachData('联系我们') >= 1){
                tipshow('您已设置“联系我们”，请勿重复添加','warn');
                $scope.tabBarList[$index]['grade'] = '';
                return;
            }
            $scope.addNav(0);
        }
        //add by wdd 2018-6-22 增加客服功能
        if($scope.tabBarList[$index]['grade'] == 'wechat'){
            if($scope.eachData('客服') >= 1){
                tipshow('您已设置“客服”，请勿重复添加','warn');
                $scope.tabBarList[$index]['grade'] = '';
                return;
            }
            $scope.addNav(6);
        }
        //end
        $scope.tabBarList[$index]['grade'] = '';
    }
    /**
    * 添加导航 导航选择框数据
    * text 标题
    * type 类型   1 微页面 2 一键参团 3 购物车
    */
    $scope.navSelectData = {
        isNavSelectShow: false,//添加导航选择框是否显示
        top: 122,//弹框上移距离
        navList:[
            {
                title: "微页面及分类",
                type: 1,
            },
            {
                title: "一键参团",
                type: 2,
            },
            {
                title: "购物车",
                type: 3,
            },
        ]
    }
    /**
    * 小程序路径说明
    * pages/index/index 主页
    * pages/cart/cart 购物车
    * pages/member/index/index 会员中心
    * pages/micropage/index/index 微页面
    * pages/grouppurchase/groupOnekey/groupOnekey 一键参团
    * 
    * 修改链接说明：
    * 只有微页面可以修改链接
    *
    * 根据需求不同（与有赞相比）
    * 底部最多可设置三个微页面
    * 按照从左向右的顺序 分别  （新建三个微页面，为了实现改变微页面链接不用从新提交代码问题）
    * pages/micropage/index1/index 微页面1
    * pages/micropage/index2/index 微页面2
    * pages/micropage/index3/index 微页面3
    *
    * 初始数据  统一用微页面路径  提交时统一更换路径
    *
    * pageId  微页面 id  非微页面为0
    * id  导航id  添加为0 编辑为导航id
    * is_weixin 0否  1是  当前导航是否提交到微信
    */
    $scope.tabBarList = [//导航数据
        {
            "id":0,//导航id
            "text":"首页",//标题
            "pagePath":"pages/index/index",//页面路径
            "iconPath": "mctsource/images/footerBar/home-unselected.png",//未选中图片
            "selectedIconPath": "mctsource/images/footerBar/home-selected.png",//选中图片
            "urlTitle":"小程序主页",//链接名称
            "isCanReviseUrl":false,//能否修改url
            "isSyncWeixin":0,//当前导航是否提交到微信
            "pageId":0//微页面 id  非微页面为0
        }
        
    ];
    $scope.tabBarOriginArr = [];//原始导航集合
    $scope.tabBarIndex = -1;//当前选择添加图片的数据下边
    $scope.pageData = {
        searchTitle:"",//搜索内容
        list:[]//微页面列表
    }
    $scope.amend_a = null
    $scope.amend_b = null
    /**
     * func begin
     * @author wdd
     * @desc 显示修改选择框
     * @created 2017年4月10日09:08:58
     */
    $scope.showSelect = function($index){
        $scope.tabBarList[$index]['pagePath'] = '';
        $scope.tabBarList[$index]['pageId'] = 0;
        $scope.tabBarList[$index]['urlTitle'] = '';
    }
    /**
     * func begin
     * @author huoguanghui
     * @created 2017年12月13日09:08:58
     */
    /**
     * icon 弹框显示
     */

    $scope.iconModalShow=function(index){
        $("#model_icon").removeClass("hide");
        $("#modal_icon_show").addClass("hide");
        $scope.addIconImg = []
        $scope.tabBarIndex = index;
        $.ajax({
            url:'/merchants/marketing/getCustomFooterBarList',
            success:function (res) {
                if(res.status == 1){
                    angular.forEach(res.data.list,function(val,key){
                        $scope.$apply(function(){
                            $scope.addIconImg.push({
                                id: val.id,
                                text: val.text,
                                iconPath: val.iconPath,
                                selectedIconPath: val.selectedIconPath
                            })
                        })
                    })
                    $(".pic-modal").modal("show");
                }
            }
        })
    }
    $scope.delImg = function(id,e){
        $.ajax({
            url:'/merchants/marketing/delCustomFooterBar',
            data:{
                id:id
            },
            success:function (res) {
                if(res.status == 1){
                    tipshow('删除成功');
                    angular.forEach($scope.addIconImg,function(val,key){
                        $scope.safeApply(function () {
                            if(val.id == id){
                                $scope.addIconImg.splice(key,1)
                            }
                        });
                    })
                }
            }
        })
    }
    $scope.delId = null
    $scope.delShow = function(id){
        $scope.safeApply(function () {
            $scope.delId = id
        });
        console.log($scope.delId);
    }
    $scope.delHide = function(){
        $scope.delId = null
    }
    /**
     * 切换添加icon
     * @description  标题 icon 赋值
     */
    $scope.changeIcon = function(item){
        $scope.tabBarList[$scope.tabBarIndex].text = item.text;
        $scope.tabBarList[$scope.tabBarIndex].iconPath = item.iconPath;
        $scope.tabBarList[$scope.tabBarIndex].selectedIconPath = item.selectedIconPath;
        //隐藏弹框
        $(".pic-modal").modal("hide");
    }
    /**
     * 新增导航功能
     * @param type   1 微页面 2 一键参团 3 购物车 4 首页 5 我的 6 客服
     */
    $scope.addNav = function(type,event){
        if(event){
            event.stopPropagation();//阻止冒泡
        }
        switch (type) {
            case 0:
                $scope.tabBarList[$scope.index]['pagePath'] = "pages/relation/relation";
                $scope.tabBarList[$scope.index]['urlTitle'] = "联系我们";
                $scope.tabBarList[$scope.index]['pageId'] = 0;
                break;
            case 1:
                var item = {
                    "id":0,//导航id
                    "text":"",//标题
                    "pagePath":"pages/micropage/index/index",//页面路径
                    "iconPath": "",//未选中图片
                    "selectedIconPath": "",//选中图片
                    "urlTitle":"",//链接名称
                    "isCanReviseUrl":true,//能否修改url
                    "isSyncWeixin":0,
                    "pageId":0
                }
                $scope.tabBarList.splice(-1, 0, item);
                break;
            case 2:
                $scope.tabBarList[$scope.index]['pagePath'] = "pages/grouppurchase/groupOnekey/groupOnekey";
                $scope.tabBarList[$scope.index]['urlTitle'] = "一键参团";
                $scope.tabBarList[$scope.index]['pageId'] = 0;
                break;
            case 3:
                
                $scope.tabBarList[$scope.index]['pagePath'] = "pages/cart/cart";
                $scope.tabBarList[$scope.index]['urlTitle'] = "购物车";
                $scope.tabBarList[$scope.index]['pageId'] = 0;
                break;
            case 4:
                $scope.tabBarList[$scope.index]['pagePath'] = "pages/index/index";
                $scope.tabBarList[$scope.index]['urlTitle'] = "首页";
                $scope.tabBarList[$scope.index]['pageId'] = 0;
                break;
            case 5:
                $scope.$apply(function(){
                    $scope.tabBarList[$scope.index]['pagePath'] = "pages/member/index/index";
                    $scope.tabBarList[$scope.index]['urlTitle'] = "我的";
                    $scope.tabBarList[$scope.index]['pageId'] = 0;
                })
                break;
            case 6:
                $scope.tabBarList[$scope.index]['pagePath'] = "pages/common/kefu1/kefu1";
                $scope.tabBarList[$scope.index]['urlTitle'] = "客服";
                $scope.tabBarList[$scope.index]['pageId'] = 0;
                break;
            case 7:
                var item = {
                 "id":0,//导航id
                 "text":"",//标题
                 "pagePath":"",//页面路径
                 "iconPath": "mctsource/images/footerBar/car-unselected.png",//未选中图片
                 "selectedIconPath": "mctsource/images/footerBar/car-selected.png",//选中图片
                 "urlTitle":"",//链接名称
                 "isCanReviseUrl":true,//能否修改url
                 "isSyncWeixin":0,
                 "pageId":0
                }
                // $scope.tabBarList.splice(-1, 0, item);//新增数据
                $scope.tabBarList.push(item);
                break;
            default:
                // statements_def
                break;
        }
        console.log($scope.tabBarList);
        //添加数据成功后隐藏弹框
        $scope.navSelectData.isNavSelectShow = false;
    }
    /**
     * 隐藏选择导航弹框
     */
    $scope.navSelectHide = function(){
        $scope.navSelectData.isNavSelectShow = false;
    }
    /**
     * 添加导航 or 显示添加导航弹框
     * @desc 当导航存在购物车和一键参团时  直接添加微页面  
     * 否则  弹出弹框
     */
    $scope.addNavs = function(event){
        event.stopPropagation();//阻止冒泡
        if($scope.navSelectData.navList.length == 1){
            //添加微页面
            var item = {
                "id":0,//导航id
                "text":"",//标题
                "pagePath":"pages/micropage/index/index",//页面路径
                "iconPath": "",//未选中图片
                "selectedIconPath": "",//选中图片
                "urlTitle":"",//链接名称
                "isCanReviseUrl":true,//能否修改url
                "isSyncWeixin":0,
                "pageId":0
            }
            $scope.tabBarList.splice(-1, 0, item);
        }else if($scope.navSelectData.navList.length == 2){
            $scope.navSelectData.isNavSelectShow = true;//显示弹框
            $scope.navSelectData.top = 82;              //改变弹框距离父级距离
        }else if($scope.navSelectData.navList.length == 3){
            $scope.navSelectData.isNavSelectShow = true;
            $scope.navSelectData.top = 122;
        }
    }
    /**
     * 删除导航功能
     * @param item 当前导航数据  index 当前导航下标
     * @description  删除购物车或一键参团后
     */
    $scope.deleteNavBar = function(item,index){
        if(item.urlTitle === "购物车"){
            var item = {
                title: "购物车",
                type: 3,
            };
            $scope.navSelectData.navList.push(item)
        }else if(item.urlTitle === "一键参团"){
            var item = {
                title: "一键参团",
                type: 2,
            };
            $scope.navSelectData.navList.splice(0,0,item);
        }
        $scope.tabBarList.splice(index, 1);
    }
    /**
     * 打开微页面弹框
     * @param 当前导航下标
     */
    $scope.openPageModal = function(index){
        $scope.tabBarIndex = index;//下标复制
        $scope.pageData.list = [];//初始化数组
        $scope.pageData.searchTitle = "";//初始化搜索信息
        $.get('/merchants/xcx/micropage/select?page=1', function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageData.list.push({
                        "id":val.id,
                        "name":val.title,
                        "created_at":val.create_time
                    })
                })
            })
            
            var totalCount = data.total, showCount = 10,
            limit = data.pageSize;
            $('.page_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $.get('/merchants/xcx/micropage/select?page=' + page,function(response){
                        if(response.errCode == 0){
                            $scope.pageData.list = [];
                            angular.forEach(response.data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageData.list.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "created_at":val.create_time
                                    })
                                })
                            })
                        }
                    })
                }
            });
            $("#page_model").modal("show");//微页面弹框显示
        },'json')
    }
    /**
     * 搜索微页面
     */
    $scope.searchPage = function(){
        $scope.pageData.list = [];//初始化数组
        $.get('/merchants/xcx/micropage/select?page=1&title=' + $scope.pageData.searchTitle, function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageData.list.push({
                        "id":val.id,
                        "name":val.title,
                        "created_at":val.create_time
                    })
                })
            })
            
            var totalCount = data.total, showCount = 10,
            limit = data.pageSize;
            $('.page_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $.get('/merchants/xcx/micropage/select?page=' + page +"&title=" + $scope.pageData.searchTitle,function(response){
                        if(response.errCode == 0){
                            $scope.pageData.list = [];
                            angular.forEach(response.data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageData.list.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "created_at":val.create_time
                                    })
                                })
                            })
                        }
                    })
                }
            });
            $("#page_model").modal("show");//微页面弹框显示
        },'json')
    }
    /**
     * 选择微页面
     */
    $scope.choosePageLinkSure = function(item){
        $scope.tabBarList[$scope.tabBarIndex]["pageId"] = item.id;
        $scope.tabBarList[$scope.tabBarIndex]["urlTitle"] = item.name;
        $("#page_model").modal("hide");//隐藏弹框
    }
    //保存到数据库
    function saveDatabase(callback){
        $http.post("/merchants/marketing/SaveBar",{barList:$scope.tabBarList,type:1}).success(function (data) {
            $scope.btnSubmit = true;
            if(data.status == 1){//保存成功
                callback();
            }else{
                tipshow(data.info,"warn");
            }
        })
    }
    /**
     * 保存
     * 1.是否绑定微信小程序  （否 直接保存到数据库，是 通过）
     * 2.是否开启自动更新    （否 直接保存到数据库，跳转小程序设置页 是 通过）（加成判断，若无更改数据，直接保存，不跳转）
     * 3.是否更改非微页面数据（否 直接保存到数据库，是 用户选择更改小程序或者保存到数据库）
     */
    $scope.save = function(isValid){
        if(!$scope.btnSubmit){//交互过程中 不能再次提交
            return false;
        }
        if(!isValid){
            tipshow("请先编辑基本信息","warn")
            return false;
        }
        if($scope.tabBarList.length <= 1){
            tipshow('底部导航最少添加两个','warn');
            return;
        }
        $scope.btnSubmit = false;
        /**
         * 判断是否更改导航数据
         */
        var isNeedWeixin =  false;//是否需要微信交互
        var pageNum = 0;//重新设置微页面路径 
        if($scope.tabBarList.length !== $scope.tabBarOriginArr.length){
            isNeedWeixin = false;
        }
        /**
         * 判断数据是否相同 的同时  排除新增的数据  新增直接为0
         */
        angular.forEach($scope.tabBarList,function(val,index){
            if(val.isSyncWeixin == 0){
                isNeedWeixin = true;
            }
            if(!$scope.tabBarOriginArr[index] || val.text !== $scope.tabBarOriginArr[index].text){
                isNeedWeixin = true;
                val.isSyncWeixin = 0;
            }
            if(!$scope.tabBarOriginArr[index] || val.iconPath !== $scope.tabBarOriginArr[index].iconPath){
                isNeedWeixin = true;
                val.isSyncWeixin = 0;
            }
            if(val.pageId > 0){//大于0 就是微页面
                pageNum ++;
                val.pagePath = "pages/micropage/index"+pageNum+"/index";
            }
        })
        /** 
         * 是否绑定微信小程序
         */
        if($scope.isBinding == 0){//未绑定
            saveDatabase(function(){
                tipshow("保存成功");
                setTimeout(function(){
                    location.reload();
                },2000)
            })
            return false;
        }
        /**
         * 是否开启自动更新
         * @description  未开启直接保存到数据库
         */
        if($scope.is_auth_submit == 0){//未开启
            saveDatabase(function(){
                if(isNeedWeixin){//已更改导航数据
                    tipshow("保存成功,尚未开启自动更新功能,请自行提交");
                    setTimeout(function(){
                        window.location.href = "/merchants/marketing/liteappInfo"
                    },2000)
                }else{//无更改导航数据
                    tipshow("保存成功");
                }
            })
            return false;
        }
        /**
         * 判断导航是否更改
         * 1.原始数据数量 是否等与 现在导航数量 等于  不相等 弹出弹框（需要微信交互）
         * 2.数量相同时判断isSyncWexin是否相同 text 图片是否相同 相同直接交互数据库   不相同弹出弹框（需要微信交互）
         */
        
        saveDatabase(function(){
            tipshow("保存成功");
        })
    }
    /**
     * 刷新更新数据
    */
    $scope.refresh = function(){
        $http.get('/merchants/marketing/refresh_footerBar').success(function(data){
            window.location.reload()
        })
    }
    /**
     * 保存到数据库
     */
    $scope.saveData = function(){
        $scope.btnSubmit = false;
        saveDatabase(function(){
            tipshow("保存成功");
            $("#remindModal").modal("hide");
            setTimeout(function(){
                location.reload();
            },2000)
        })
    }
    /**
     * 保存并更新到微信
     */
    $scope.saveWeixin = function(){
        $scope.btnSubmit = false;
        $http.post("/merchants/marketing/SaveBar",{isSyncWeixin:true,barList:$scope.tabBarList}).success(function (data) {
            $scope.btnSubmit = true;
            if(data.status == 1){//保存成功
                tipshow("保存成功");
                $("#remindModal").modal("hide");
                setTimeout(function(){
                    window.location.href = "/merchants/marketing/liteappInfo"
                },2000)
            }else{
                tipshow(data.info,"warn");
            }
        })
    }
    /**
     * 页面编辑
     */
    $http.post("/merchants/marketing/getSyncSimpleBarDataList").success(function (data) {
        if(data.status == 1){
            $scope.is_auth_submit = data.data.is_auth_submit;//自动更新赋值
            $scope.isBinding = data.data.isBinding;//绑定小程序赋值
            var list = data.data.tabBar.list;
            if(list.length > 0){
                var arr = list;//用于深拷贝
                /* 对象数组 深拷贝函数 */
                var objDeepCopy = function (source) {
                    var sourceCopy = source instanceof Array ? [] : {};
                    for (var item in source) {
                        sourceCopy[item] = typeof source[item] === 'object' ? objDeepCopy(source[item]) : source[item];
                    }
                    return sourceCopy;
                }
                //编辑  导航列表复制
                $scope.tabBarList = list;
                //保留原始值 （深拷贝）
                $scope.tabBarOriginArr = objDeepCopy(list);
                /* 添加nav框限制 */
                angular.forEach($scope.tabBarList,function(val,index){
                    if(index == 0){
                        
                        $scope.tabBarList[index] = {
                            "id":0,//导航id
                            "text":"首页",//标题
                            "pagePath":"pages/index/index",//页面路径
                            "iconPath": val.iconPath,
                            "selectedIconPath": val.selectedIconPath,//选中图片
                            "urlTitle":"小程序主页",//链接名称
                            "isCanReviseUrl":false,//能否修改url
                            "isSyncWeixin":0,//当前导航是否提交到微信
                            "pageId":0//微页面 id  非微页面为0
                        }
                    }else{
                        val.isCanReviseUrl = true;
                    }
                  
                })
            }
        }else{
            console.log("请求数据失败")
        }
    })

    $scope.imgIndex = null
    $scope.tempUploadImage =[];


    //图片上传  2018-6-19
    $scope.addAdvs = function(num){
        $scope.amendNum = num
        $scope.uploadShow = false;
        $scope.eventKind=1;
        $scope.grounps = [];
        $scope.choosePosition = 1;//图片广告
        $http.get('/merchants/myfile/getClassify').success(function(data){
            console.log(data);
            angular.forEach(data.data,function(val,key){
                if(key == 0){
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(response){
                angular.forEach(response.data[0].data,function(val,key){
                    val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                var totalCount = response.data[0].total, showCount = 10,
                    limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                        })
                    }
                });
            })
            $("#iconModal").modal('hide')
            $("#myModal-adv").modal("show");
        })
        $scope.initchooseAdvImage();
    }
    $scope.initchooseAdvImage = function(){
        $scope.tempUploadImage =[];
        angular.forEach($scope.uploadImages,function(data,index){
            data.isShow = false;
        })
    }
    // 点击图片分组   2018-6-19
    $scope.chooseGroup = function(grounp){
        angular.forEach($scope.grounps,function(val,key){
            val.isactive = false;
        })
        var classifyId = grounp.id;
        $('input[name="classifyId"]').val(classifyId);
        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(data){
            angular.forEach(data.data[0].data,function(val,key){
                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
            })
            $scope.uploadImages = data.data[0].data;
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
            $('.ui-pagination').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(data){
                        angular.forEach(data.data[0].data,function(val,key){
                            val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];

                        })
                        $scope.uploadImages = data.data[0].data;
                    })
                }
            });
        })
        grounp.isactive = true;
    }
    // 选择图片 2018-6-19
    $scope.chooseImage = function(image,$index){
        $scope.tempUploadImage =[];
        if($scope.eventKind == 1){
            if(image.isShow==false){
                $scope.imgIndex = $index
                image.isShow=true;
                image['index'] = $index;
                image['chooseLink'] = false; //控制是否显示已经选过链接
                image['dropDown'] = false;
                image['linkName'] = '';
                image['pageCurrent'] = false;
                $scope.tempUploadImage.push(image);
            }else{
                image.isShow=false;
                $scope.imgIndex = null
                for(var i=0;i<$scope.tempUploadImage.length;i++){
                    if($scope.tempUploadImage[i]['index']==$index){
                        $scope.tempUploadImage.splice(i,1);
                    }
                }
            }
        }
    }

    //选择广告图片确定按钮 2018-06-19
    $scope.chooseAdvSureBtn = function(){
        if($scope.tempUploadImage.length>0){
            angular.forEach($scope.tempUploadImage,function(val,key){
                val.image_id = val.FileInfo.id;
            })
        }
        if($scope.eventKind == 1){
            for(var i=0;i<$scope.tempUploadImage.length;i++){
                $scope.safeApply(function(){
                    $scope.image = angular.copy($scope.tempUploadImage[i]);
                    if($scope.image['FileInfo']['path'].indexOf(imgUrl)>=0){
                        $scope.image['FileInfo']['path'] =  $scope.image['FileInfo']['path'];
                    }else{
                        $scope.image['FileInfo']['path'] =  imgUrl + $scope.image['FileInfo']['path'];
                    }
                    if($scope.amendNum == 1){
                        $scope.amend_a = []
                        $scope.amend_a.push($scope.image)
                    }else if($scope.amendNum == 2){
                        $scope.amend_b = []
                        $scope.amend_b.push($scope.image)
                    }
                })
            }
        }
            $('#myModal-adv').modal("hide")
            $("#iconModal").modal('show')
    }
    // 上传图片弹框位置 2018-06-19
    $scope.upload = function(){
        $scope.uploadShow = true;
        $('.webuploader-pick').next('div').css({
            'top': '19px',
            'width': '168px',
            'height': '44px',
            'left':'40%'
        })
    }
    //上传确定按钮 2018-06-19
    $scope.uploadSureBtn = function(){
        $scope.chooseAdvSureBtn();
        closeUploader();
    }
    // 返回选择图片 2018-06-19
    $scope.showImage = function(){
        $scope.uploadShow = false;
    }
    /*
    *删除图片 2018-06-19 @邓钊
     */
    $scope.close_img=function(num){
        if(num == 1){
            $scope.amend_a = null
        }else if(num ==2) {
            $scope.amend_b = null
        }

    }
    /*
    * 保存图片
    * @ 邓钊 2018-6-15
    * */
    $scope.icon_title = ''
    $scope.preserve_img=function(){
        if(!$scope.amend_a){
            tipshow('请添加一张普通图片','warn');
            return false
        }
        if(!$scope.amend_b){
            tipshow('请添加一张高亮图片','warn');
            return false
        }
        if(!$scope.icon_title){
            tipshow('请填写名称','warn');
            return false
        }
        $.ajax({
            url:"/merchants/marketing/addCustomFooterBar",
            data:{
                "text":$scope.icon_title,
                "iconPath": $scope.amend_a[0].FileInfo.path,
                "selectedIconPath": $scope.amend_b[0].FileInfo.path
            },
            success:function (res) {
                if(res.status == 1){
                    tipshow('保存成功');
                    setTimeout(function () {
                        $scope.iconModalShow($scope.tabBarIndex)
                    },2000)
                }
            }
        })
    }
    /*
    * 新建图标
    * @ 邓钊 2018-6-15
    * */
    $scope.add_icon=function(){
        $scope.amend_a = null
        $scope.amend_b = null
        $scope.icon_title = ''
        $("#model_icon").addClass("hide");
        $("#modal_icon_show").removeClass("hide");
    }
    /*
    * 图片上传成功之后返回的数据
    *
    * 2018-06-19
    * */
    uploader.on('uploadSuccess', function (file, response) {
        if (response.status == 1) {
            $scope.$apply(function () {
                response.data['FileInfo']['path'] = imgUrl + response.data['FileInfo']['path'];
                $scope.tempUploadImage.unshift(response.data);
            })
        }
    });
    /*
    * 监听tempUploadImage来切换确认按钮
    *
    * 2018-06-19
    * */
    $scope.$watch("tempUploadImage",function(newVal,oldVal){
        if($scope.tempUploadImage.length==0){
            $scope.chooseSureBtn = false;
        }else{
            $scope.chooseSureBtn = true;
        }
    },true)

    /*
    * 跟新angular 数据
    *
    * 2018-06-19
    * */
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
})