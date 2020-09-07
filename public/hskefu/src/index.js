// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './views/App'
import router from './router'
// import axios from 'axios'
import store from './store/'
import './components'
import './plugins'
import './assets/css/index.styl'
// import JsEncrypt from 'jsencrypt/bin/jsencrypt'
import io from '../socket.io.min.js'
Vue.config.productionTip = false
// import { emoji } from './api/emoji.js'

// Vue.prototype.emoji = emoji;

Vue.prototype.globalClick = function (callback) {   //页面全局点击
    document.onclick = callback;
}

import { emoji } from './apis/emoji.js'

import  { ToastPlugin,AlertPlugin,LoadingPlugin  } from 'vux'

Vue.prototype.emoji = emoji;
//测试环境
// var userIo = 'https://kf.huisou.cn:8082/im/user';
// var custIo = 'https://kf.huisou.cn:8082/im/agent';
// var userIo = 'https://183.129.196.178:8082/im/user';
// var custIo = 'https://183.129.196.178:8082/im/agent';
////线上环境
// var userIo = 'https://hsim.huisou.cn:8082/im/user';
var custIo = 'https://hsim.huisou.cn:8082/im/agent';
   // var userIo = 'http://localhost:8082/im/user';
   // var custIo = 'http://localhost:8082/im/agent';

// 路由拦截
var socket = io.connect(custIo);
Vue.prototype.socket = socket;
Vue.use(ToastPlugin)
Vue.use(AlertPlugin)
Vue.use(LoadingPlugin)
// socket.on('disconnect', function() {
//   alert(333)
// })
socket.on('onCustJoin',(res)=>{
  console.log("--------onCustJoin")
    socket.emit('custJoin', {shopId:store.getters.userList.shopId,custId:store.getters.userList.custId,custStatus:'online'});
})

socket.on('status',(res)=>{
  Vue.$vux.alert.show({
    title: '提示',
    content: '您的账号在其它设备登录',
    onShow () {
      console.log('Plugin: I\'m showing')
      // window.location.href="/index"
    },
    onHide () {
      window.location.href="/index"
    }
  })
})
router.beforeEach((to, from, next) => {
    // var socket = io.connect(userIo);
    // Vue.prototype.socket = socket;
    next()
    // if (to.meta.requireAuth) {  // 判断该路由是否需要登录权限
    //     if (store.state.token) {  // 通过vuex state获取当前的token是否存在
    //         next();
    //     }
    //     else {
    //         next({
    //             path: '/login',
    //             query: {redirect: to.fullPath}  // 将跳转的路由path作为参数，登录成功后跳转到该路由
    //         })
    //     }
    // }else {
    //     next();
    // }
})
// Vue.prototype.$JsEncrypt = JsEncrypt
localStorage.custJoinWay = 'APP';
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
