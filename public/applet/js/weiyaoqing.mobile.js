document.write(unescape("%3Cscript src='https://res.wx.qq.com/open/js/jweixin-1.0.0.js' type='text/javascript'%3E%3C/script%3E"));

var wyq_Service_api = 'https://service.wyaoqing.com';
var wx_js_run = false;
var wx_Interval = null;
var wx_Tickt = null;
        // 微信分享
        $(function(){   
            var $body  = $('body'),
                title  = $body.attr('title'),
                imgUrl = $body.attr('icon'),
                link   = location.href.split('#').toString();
                desc   = $body.attr('desc') || ' ';    
            var url = location.href.split('#').toString();
            $.get("/shop/weixin/getWeixinSecretKey",{"url": url},function(data){
                if(data.errCode == 0){
                    wx.config({
                        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                        appId: data.data.appId, // 必填，公众号的唯一标识
                        timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                        nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                        signature: data.data.signature,// 必填，签名，见附录1
                        jsApiList: [
                            'checkJsApi',
                            'onMenuShareTimeline',
                            'onMenuShareAppMessage',
                            'onMenuShareQQ',
                            'chooseWXPay'
                        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                    });
                    
                }
            })      
            wx.ready(function () {  

                //分享到朋友圈
                wx.onMenuShareTimeline({  
                    title: title, // 分享标题  
                    link: link, // 分享链接,将当前登录用户转为puid,以便于发展下线  
                    imgUrl: imgUrl, // 分享图标  
                    success: function () {   
                        // 用户确认分享后执行的回调函数  
                        //alert('分享成功');  
                    },  
                    cancel: function () {   
                        // 用户取消分享后执行的回调函数  
                    }  
                });  

                //分享给朋友 
                wx.onMenuShareAppMessage({  
                    title: title, // 分享标题  
                    desc: desc, // 分享描述  
                    link: link, // 分享链接  
                    imgUrl: imgUrl, // 分享图标  
                    type: '', // 分享类型,music、video或link，不填默认为link  
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空  
                    success: function () {   
                        // 用户确认分享后执行的回调函数  
                    },  
                    cancel: function () {   
                        // 用户取消分享后执行的回调函数  
                    }  
                });

                //分享到QQ
                wx.onMenuShareQQ({
                    title: title, // 分享标题
                    desc: desc, // 分享描述
                    link: link, // 分享链接
                    imgUrl: imgUrl, // 分享图标
                    success: function () { 
                       // 用户确认分享后执行的回调函数
                    },
                    cancel: function () { 
                       // 用户取消分享后执行的回调函数
                    }
                });

                //分享到腾讯微博
                wx.onMenuShareWeibo({
                    title: title, // 分享标题
                    desc: desc, // 分享描述
                    link: link, // 分享链接
                    imgUrl: imgUrl, // 分享图标
                    success: function () { 
                       // 用户确认分享后执行的回调函数
                    },
                    cancel: function () { 
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.error(function(res){  
                    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。  
                    //alert("errorMSG:"+res);  
                });  
            });
        })
// function js_weixin(){
//     if(typeof wx == "undefined" || wx_js_run == true ){
//         return;
//     } else {
//         wx_js_run = true;
//         clearInterval(wx_Interval);
//     }
//     var url = location.href.split('#').toString();
//     $.get("/applet/weixin/getWeixinSecretKey",{"url": url},function(data){
//         if(data.errCode == 0){
//             wx.config({
//                 debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
//                 appId: data.data.appId, // 必填，公众号的唯一标识
//                 timestamp: data.data.timestamp, // 必填，生成签名的时间戳
//                 nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
//                 signature: data.data.signature,// 必填，签名，见附录1
//                 jsApiList: [
//                     'checkJsApi',
//                     'onMenuShareTimeline',
//                     'onMenuShareAppMessage',
//                     'onMenuShareQQ',
//                     'chooseWXPay'
//                 ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
//             });
            
//         }
//     })  
//     wx.ready(share_param_get);
// }

function isExitsFunction(funcName) {
    try {
        if (typeof(eval(funcName)) == "function") {
            return true;
        }
    } catch(e) {}
    return false;
}

// function getJsTicket(){
//     var url = window.location.href;
//     $.get(wyq_Service_api+'/api/weixinapi/getJsTicket',{url:url},function(msg){
//         wx_Tickt = msg.data;
//         if(msg.code == 1){
//             js_weixin();
//             wx_Interval =setInterval(js_weixin,500);
//         }
//     });

// }
// getJsTicket();

function statics(type){
    var url = window.location.href;
    console.log(campaign_id);
    // $.post(wyq_Service_api+'/api/statics/campaign',{url:url,campaign_id:campaign_id,type:type},function(msg){
    //     console.log(msg);
    // });
}