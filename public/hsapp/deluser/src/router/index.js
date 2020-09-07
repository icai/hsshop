import Vue from 'vue';
import Router from 'vue-router';
import Index from '@/views/Index';
import DelError from '@/views/DelError';
import Agreement from '@/views/Agreement';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'index',
      component: Index,
    },
    {
      path: '/delError',
      name: 'delError',
      component: DelError,
    },
    {
      path: '/agreement',
      name: 'agreement',
      component: Agreement,
    },
  ],
});
