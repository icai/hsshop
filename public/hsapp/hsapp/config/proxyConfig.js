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
    }
  }
}