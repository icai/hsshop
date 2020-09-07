module.exports = {
  proxyList: {
    '/douban': {
      target: 'https://api.douban.com',
      changeOrigin: true,
      pathRewrite: {
        '^/douban': ''
      }
    },
    '/api': {
      target: 'http://hsshop.myapp.com',
      changeOrigin: true,
      pathRewrite: {
        '^/api': ''
      }
    },
    '/hsapp': {
      target: 'https://hsshop3.huisou.cn',
      changeOrigin: true,
      pathRewrite: {
        '^/hsapp': ''
      }
    },
    '/list': {
        //线上地址
//        target: 'http://192.168.27.113:8087/list',
//         target: 'http://localhost:8080',
          target: 'http://192.168.0.232:8087/list',
        changeOrigin: true,
        pathRewrite: {
          '^/list': ''
        }
    },
    '/kefuapi': {
      //线上地址
      // target: 'https://www.huisou.cn/api',
      target: 'https://hsshop.huisou.cn/api',
      // target: 'http://localhost:8080',
      changeOrigin: true,
      pathRewrite: {
        '^/api': ''
      }
    },
  }
}
