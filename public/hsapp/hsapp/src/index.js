// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './views/App'
import router from './router'
// import axios from 'axios'

import './components'
import './plugins'
import './assets/css/index.styl'
// import JsEncrypt from 'jsencrypt/bin/jsencrypt'

Vue.config.productionTip = false

// Vue.prototype.$JsEncrypt = JsEncrypt

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
