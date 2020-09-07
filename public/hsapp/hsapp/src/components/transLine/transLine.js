let Option = {
  grid: {
    top: 5,
    bottom: 25,
    left: 24,
    right: 24,
    containLabel: true
  },
  xAxis: [{
    position: 'bottom',
    offset: 0,
    boundaryGap: 0,
    axisLine: {
      onZero: false,
      lineStyle: {
        color: '#fff'
      }
    },
    axisTick: {
      show: false
    },
    axisLabel: {
      color: '#fff',
      interval: 0
    },
    splitLine: {
      show: true,
      lineStyle: {
        color: '#fff'
      }
    },
    //日期
    data: ['2-10', '', '', '', '', '', '2-17']
  }, {
    type: 'category',
    position: 'top',
    boundaryGap: false,
    axisLine: {
      onZero: false,
      lineStyle: {
        color: '#fff'
      }
    },
    axisTick: {
      show: false
    }
  }],
  yAxis: [{
    type: 'value',
    boundaryGap: false,
    axisLine: {
      onZero: false,
      lineStyle: {
        color: '#fff'
      }
    },
    axisTick: {
      show: false,
      length: 2
    },
    axisLabel: {
      show: false
    },
    splitLine: {
      show: false
    }
  }],
  color: ['#fff']
}
export default Option
