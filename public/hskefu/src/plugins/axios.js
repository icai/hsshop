import axios from 'axios'
import Vue from 'vue'
import {Alert, Confirm, Toast} from 'wc-messagebox'
import 'wc-messagebox/style.css'

Vue.use(Toast)
// 引入解密的私钥
// import KEY from '../utils/jsencrypt'
// import jsencrypt from 'jsencrypt/bin/jsencrypt'
// import Base64 from '../utils/base64'
let vue = new Vue()
// 设置请求拦截器
axios.interceptors.request.use((config) => {
  config.headers['X-Requested-With'] = 'XMLHttpRequest'
  return config
})

// 设置响应的拦截器
axios.interceptors.response.use((response) => {
  // 这里获取到的数据是经过加密的 所以需要前端解密
  let data = response.data
  switch (data.errCode) {
    case 0:
    case 40000:
      return data
    case -40001:
    vue.$toast(data.errMsg)

    case -40005:
    vue.$toast(data.errMsg)

    case -40006:
    vue.$toast(data.errMsg)
    // 后面在这里统一处理错误
    case 'SHIRO_E5001':
      // 可以在这里设置未登录的跳转等
      break

    default:
  }
  switch (parseInt(data.code)) {
    case 0:
    case 100:
      return response
    case 404:
    return response

    case -40005:
    vue.$toast(data.errMsg);
    
    case -40006:
    vue.$toast(data.errMsg);
    // 后面在这里统一处理错误
    case 'SHIRO_E5001':
      // 可以在这里设置未登录的跳转等
      break

    default:
  }
  let err = null;
  if(data.errCode){
    err = new Error(data.errMsg)
  }else{
    err = new Error(data.msg)
  }
  err.data = data
  err.response = response

  throw err
}, (err) => {
  if (err && err.response) {
    switch (err.response.status) {
      case 401:
        err.message = '请登陆'
        break
      case 404:
        err.message = '请求地址出错'
        break
      case 500:
      case 503:
        err.message = '服务器错误'
        break
      case 505:
        err.message = '请求数据超时，请刷新页面重试'
        break

      default:
    }
  }
  return Promise.reject(err)
})

axios.install = (Vue) => {
  Vue.prototype.$axios = axios
}

export default axios
