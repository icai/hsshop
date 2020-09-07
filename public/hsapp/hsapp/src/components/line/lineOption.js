let Option = {
  grid: {
    top: 0,
    bottom: 25,
    left: 24,
    right: 24,
    containLabel: true
  },
  xAxis: [{
    type: 'category',
    position: 'bottom',
    boundaryGap: false,
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
      interval: 0,
      inside: false
    },
    splitLine: {
      show: true,
      lineStyle: {
        color: '#fff'
      }
    },
    data: ['2月1号', '', '', '', '', '', '2月8号']
  }, {
    type: 'category',
    position: 'top',
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
    axisLine: {
      lineStyle: {
        color: '#fff'
      }
    },
    axisTick: {
      show: false
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
