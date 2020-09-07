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
    offset: 10,
    boundaryGap: 0,
    axisLine: {
      show: false
      // lineStyle: {
      //   color: '#fff'
      // }
    },
    axisTick: {
      show: false
    },
    axisLabel: {
      show: false,
      color: '#fff'
    },
    splitLine: {
      show: false
    },
    data: ['2月1号', '', '', '', '', '', '2月8号']
  }],
  yAxis: [{
    type: 'value',
    // boundaryGap: true,
    // offset: '20',
    // splitNumber: 3,
    axisLine: {
      show: false
    },
    axisTick: {
      show: false
    },
    axisLabel: {
      show: false
    },
    splitLine: {
      show: true,
      lineStyle: {
        type: 'dotted',
        color: '#fff'
      }
    }
  }],
  color: ['#fff']
}
export default Option
