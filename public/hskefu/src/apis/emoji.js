import data from '../data/emoji-data.js'
import values from 'object.values'
let emojiData = {}
values(data).forEach(item => {
//	console.log(item)
	for (let x in item) {
//		console.log(x)
		if(!emojiData[x]){
			emojiData[x] = item[x]
		}
	}
//emojiData = { ...emojiData, ...item }
})
//console.log(emojiData)

/**
 *
 *
 * @export
 * @param {string} value
 * @returns {string}
 */

export function emoji (value) {
  if (!value) return
    // console.log(Object.keys(emojiData))
  Object.keys(emojiData).forEach(item => {
    // console.log(item)
    value = value.replace(new RegExp(item, 'g'), createIcon(item))
  })
  return value
}

function createIcon (item) {
  const value = emojiData[item]
  const path = '../../static/emoji/'
  return `<img class="emoji" src=${path}${value} width="20px" height="20px">`
}
