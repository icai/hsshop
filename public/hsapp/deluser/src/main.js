import Vue from 'vue';
import FastClick from 'fastclick';
import VueRouter from 'vue-router';
import axios from 'axios';
import { ConfirmPlugin, ToastPlugin } from 'vux';
import App from './App';
import router from './router/index';

Vue.use(ConfirmPlugin);
Vue.use(ToastPlugin);
Vue.use(VueRouter);
FastClick.attach(document.body);
Vue.prototype.$axios = axios;
Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue({
  router,
  render: h => h(App),
}).$mount('#app-box');
