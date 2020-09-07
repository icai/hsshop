import VueRouter from 'vue-router'
import routes from './routes'

// 封装一下router 返回一个实例
const router = new VueRouter({
  routes,
  mode: 'history'
})
router.beforeEach((to, from, next) => {
  document.title = to.query.title || to.meta.title || '会搜app'
  next()
})
export default router
