    $.ajaxSettings = $.extend($.ajaxSettings, {
    beforeSend: beforeSend,
    complete:complete,
    });
    // alert(444)
    function complete(xhr, status){
    // window.location.href="http://www.baidu.com"
    console.log(xhr.responseText)
    if(xhr.responseText.code && xhr.responseText.code == 40004){
        window.location.href = "/aliapp/authorization/login"
    }
    }
    function getQueryString(name) { 
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
    var r = window.location.search.substr(1).match(reg); 
    if (r != null) return unescape(r[2]); 
    return null; 
    }
    var aliToken = getQueryString('aliToken');
    if(aliToken){
    window.localStorage.setItem('aliToken',aliToken);
    }else{
    aliToken = window.localStorage.getItem('aliToken');
    }
    function beforeSend(xhr, settings) {
    // console.log(xhr)
    xhr.setRequestHeader("aliToken", aliToken);
    // var context = settings.context
    // console.log(44224)
    // if (settings.beforeSend.call(context, xhr, settings) === false ||
    //     triggerGlobal(settings, context, 'ajaxBeforeSend', [xhr, settings]) === false)
    //   return false

    // triggerGlobal(settings, context, 'ajaxSend', [xhr, settings])
    }
    var url = location.href.split('#').toString();
    if(window.location.search){
    url += '&_pid_='+ mid;
    }else{
    url += '?_pid_='+ mid;
    }
    var xcx_share_url = url;
    my.postMessage({share_title:'',share_desc:'',share_url:xcx_share_url});
