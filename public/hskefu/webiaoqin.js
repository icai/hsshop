// 原 swipe.js文件
  window.Swipe = function(element, options) {
    if (!element) return null;
    var _this = this;
    this.options = options || {};
    this.index = this.options.startSlide || 0;
    this.speed = this.options.speed || 300;
    this.callback = this.options.callback ||
    function() {};
    this.container = element;
    this.element = this.container.children[0];
    this.element.style.listStyle = 'none';
    this.setup();
    this.begin();
    if (this.element.addEventListener) {
      this.element.addEventListener('mousedown', this, false);
      this.element.addEventListener('touchstart', this, false);
      this.element.addEventListener('touchmove', this, false);
      this.element.addEventListener('touchend', this, false);
      this.element.addEventListener('webkitTransitionEnd', this, false);
      this.element.addEventListener('msTransitionEnd', this, false);
      this.element.addEventListener('oTransitionEnd', this, false);
      this.element.addEventListener('transitionend', this, false);
      if (!this.unresize) {
        window.addEventListener('resize', this, false)
      }
    }
  };
  Swipe.prototype = {
    setup: function() {
      this.slides = this.element.children;
      this.length = this.slides.length;
      if (this.length < 2) return null;
      this.width = this.container.getBoundingClientRect().width || this.width;
      this.width = 320;
      if (!this.width) return null;
      this.container.style.visibility = 'hidden';
      this.element.style.width = (this.slides.length * this.width) + 'px';
      var index = this.slides.length;
      while (index--) {
        var el = this.slides[index];
        el.style.width = this.width + 'px';
        el.style.display = 'table-cell';
        el.style.verticalAlign = 'top'
      }
      this.slide(this.index, 0);
      this.container.style.visibility = 'visible'
    },
    slide: function(index, duration) {
      var style = this.element.style;
      if (duration == undefined) {
        duration = this.speed
      }
      style.webkitTransitionDuration = style.MozTransitionDuration = style.msTransitionDuration = style.OTransitionDuration = style.transitionDuration = duration + 'ms';
      style.MozTransform = style.webkitTransform = 'translate3d(' + -(index * this.width) + 'px,0,0)';
      style.msTransform = style.OTransform = 'translateX(' + -(index * this.width) + 'px)';
      this.index = index
    },
    getPos: function() {
      return this.index
    },
    prev: function(delay) {
      this.delay = delay || 0;
      clearTimeout(this.interval);
      if (this.index) this.slide(this.index - 1, this.speed)
    },
    next: function(delay) {
      this.delay = delay || 0;
      clearTimeout(this.interval);
      if (this.index < this.length - 1) this.slide(this.index + 1, this.speed);
      else this.slide(0, this.speed)
    },
    begin: function() {
      var _this = this;
      this.interval = (this.delay) ? setTimeout(function() {
        _this.next(_this.delay)
      }, this.delay) : 0
    },
    stop: function() {
      this.delay = 0;
      clearTimeout(this.interval)
    },
    resume: function() {
      this.delay = this.options.auto || 0;
      this.begin()
    },
    handleEvent: function(e) {
      var that = this;
      if (!e.touches) {
        e.touches = new Array(e);
        e.scale = false
      }
      switch (e.type) {
      case 'mousedown':
        (function() {
          that.element.addEventListener('mousemove', that, false);
          that.element.addEventListener('mouseup', that, false);
          that.element.addEventListener('mouseout', that, false);
          that.onTouchStart(e)
        })();
        break;
      case 'mousemove':
        this.onTouchMove(e);
        break;
      case 'mouseup':
        (function() {
          that.element.removeEventListener('mousemove', that, false);
          that.element.removeEventListener('mouseup', that, false);
          that.element.removeEventListener('mouseout', that, false);
          that.onTouchEnd(e)
        })();
        break;
      case 'mouseout':
        (function() {
          that.element.removeEventListener('mousemove', that, false);
          that.element.removeEventListener('mouseup', that, false);
          that.element.removeEventListener('mouseout', that, false);
          that.onTouchEnd(e)
        })();
        break;
      case 'touchstart':
        this.onTouchStart(e);
        break;
      case 'touchmove':
        this.onTouchMove(e);
        break;
      case 'touchend':
        this.onTouchEnd(e);
        break;
      case 'webkitTransitionEnd':
      case 'msTransitionEnd':
      case 'oTransitionEnd':
      case 'transitionend':
        this.transitionEnd(e);
        break;
      case 'resize':
        this.setup();
        break
      }
    },
    transitionEnd: function(e) {
      e.preventDefault();
      if (this.delay) this.begin();
      this.callback(e, this.index, this.slides[this.index])
    },
    onTouchStart: function(e) {
      this.start = {
        pageX: e.touches[0].pageX,
        pageY: e.touches[0].pageY,
        time: Number(new Date())
      };
      this.isScrolling = undefined;
      this.deltaX = 0;
      this.element.style.MozTransitionDuration = this.element.style.webkitTransitionDuration = 0
    },
    onTouchMove: function(e) {
      if (e.touches.length > 1 || e.scale && e.scale !== 1) return;
      this.deltaX = e.touches[0].pageX - this.start.pageX;
      if (typeof this.isScrolling == 'undefined') {
        this.isScrolling = !! (this.isScrolling || Math.abs(this.deltaX) < Math.abs(e.touches[0].pageY - this.start.pageY))
      }
      if (!this.isScrolling) {
        e.preventDefault();
        clearTimeout(this.interval);
        this.deltaX = this.deltaX / ((!this.index && this.deltaX > 0 || this.index == this.length - 1 && this.deltaX < 0) ? (Math.abs(this.deltaX) / this.width + 1) : 1);
        this.element.style.MozTransform = this.element.style.webkitTransform = 'translate3d(' + (this.deltaX - this.index * this.width) + 'px,0,0)'
      }
    },
    onTouchEnd: function(e) {
      var isValidSlide = Number(new Date()) - this.start.time < 250 && Math.abs(this.deltaX) > 20 || Math.abs(this.deltaX) > this.width / 2,
        isPastBounds = !this.index && this.deltaX > 0 || this.index == this.length - 1 && this.deltaX < 0;
      if (!this.isScrolling) {
        this.slide(this.index + (isValidSlide && !isPastBounds ? (this.deltaX < 0 ? 1 : -1) : 0), this.speed)
      }
    }
  };
  var emoji_data = ["\[Smile\]", "\[Grimace\]", "\[Drool\]", "\[Scowl\]", "[CoolGuy]", "[Sob]", "[Shy]", "[Silent]", "[Sleep]", "[Cry]", "[Awkward]", "[Angry]", "[Tongue]", "[Grin]", "[Surprise]", "[Frown]", "[Ruthless]", "[Blush]", "[Scream]", "[Puke]", "[Chuckle]", "[Joyful]", "[Slight]", "[Smug]", "[Hungry]", "[Drowsy]", "[Panic]", "[Sweat]", "[Laugh]", "[Commando]", "[Determined]", "[Scold]", "[Shocked]", "[Shhh]", "[Dizzy]", "[Tormented]", "[Toasted]", "[Skull]", "[Hammer]", "[Wave]", "[Speechless]", "[NosePick]", "[Clap]", "[Shame]", "[Trick]", "[Bah！L]", "[Bah！R]", "[Yawn]", "[Pooh-pooh]", "[Shrunken]", "[TearingUp]", "[Sly]", "[Kiss]", "[Wrath]", "[Whimper]", "[Cleaver]", "[Watermelon]", "[Beer]", "[Basketball]", "[PingPong]", "[Coffee]", "[Rice]", "[Pig]", "[Rose]", "[Wilt]", "[Lips]", "[Heart]", "[BrokenHeart]", "[Cake]", "[Lightning]", "[炸弹]", "[刀]", "[足球]","[瓢虫]","[便便]","[月亮]","[太阳]","[礼物]","[拥抱]","[强]","[弱]","[握手]","[胜利]","[抱拳]","[勾引]","[拳头]","[差劲]","[爱你]","[NO]","[OK]","[爱情]","[绯闻]","[跳跳]","[发抖]","[怄火]","[转圈]","[磕头]","[回头]","[跳绳]","[挥手]"].slice(0, -7);
// 原 input.js文件
  var myInput = (function() {
    var mi = function() {
        this.maxLength = 500, this.currentLength = 0
      }
    function getCursortPosition (textDom) {
        var cursorPos = 0;
        if (document.selection) {
        // IE Support
            textDom.focus();
            var selectRange = document.selection.createRange();
            selectRange.moveStart ('character', -textDom.value.length);
            cursorPos = selectRange.text.length;
         }else if (textDom.selectionStart || textDom.selectionStart == '0') {
          // Firefox support
            cursorPos = textDom.selectionStart;
         }
         return cursorPos;
    }
    mi.prototype = {
      listen: function(thi, evt) {
        var that = this;
        if ("/:del" == evt.value) { 
          // alert(getCursortPosition(document.getElementsByClassName('js-inputer-txta')[0]))
          // return;
          if(evt.vue.inputWords == ''){
            return;
          }
          var pre_str = evt.vue.inputWords.substr(0,getCursortPosition(document.getElementsByClassName('js-inputer-txta')[0]));
          var last_str = evt.vue.inputWords.substr(getCursortPosition(document.getElementsByClassName('js-inputer-txta')[0]),evt.vue.inputWords.length);
          if(pre_str.substr(pre_str.length - 1,1) == ']'){
            var emoji = pre_str.substr(pre_str.lastIndexOf('['),pre_str.length);
            if(emoji_data.indexOf(emoji) !== -1){
              pre_str = pre_str.substr(0,pre_str.length - emoji.length);
            }else{
              pre_str = pre_str.substr(0,pre_str.length - 1);
            }
          }else{
            pre_str = pre_str.substr(0,pre_str.length - 1);
          }
          evt.vue.inputWords = pre_str + last_str;
          // thi = evt.srcElement;
          // var imgs = thi.querySelectorAll("img");
          // if (imgs.length) {
          //   imgs[imgs.length - 1].remove()
          // }
          return
        }
        if (evt.keyCode && -10 == evt.keyCode) {
          if (evt.value.length > (that.maxLength - that.currentLength)) {
            return that
          }
          console.log(evt)
          // alert(2)
          // console.log(document.getElementsByClassName('js-inputer-txta')[0].value)
          evt.vue.inputWords = evt.vue.inputWords + evt.value; 
          // var img = new Image();
          // img.src = evt.imgUrl;
          // img.innerHTML = evt.value;
          // img.setAttribute("data-innerHTML", evt.value);
          // thi.appendChild(img)
        }
      }
    }
    return new mi()
  })();
// 原 dialog_min.js文件
  var iDialog = (function() {
    var a = function() {
        this.options = {
          id: "dialogWindow_",
          classList: "",
          type: "",
          wrapper: "",
          title: "",
          close: "",
          content: "",
          cover: true,
          btns: []
        }
      };
    return a
  })();
  var iTemplate = (function() {
    var a = function() {};
    a.prototype = {
      makeList: function(e, j, i) {
        var g = [],
          h = [],
          c = /{(.+?)}/g,
          d = {},
          f = 0;
        for (var b in j) {
          if (typeof i === "function") {
            d = i.call(this, b, j[b], f++) || {}
          }
          g.push(e.replace(c, function(k, l) {
            return (l in d) ? d[l] : (undefined === j[b][l] ? j[b] : j[b][l])
          }))
        }
        // console.log(g.join(""))
        return g.join("")
      }
    };
    return new a()
  })();
// 原index.html文件内部js
  // $().ready(function() {
  //   form_emotion.rend();
  //   myInput.maxLength = 500
  // });
  // console.log(111)
  var form_emotion = (function() {
    var fe = function() {
        this.values = emoji_data
        this.spearate = 100
      }
    fe.prototype = {
      rend: function(data) {
        var that = this;
        // console.log(k)
        var TPL = '{seprateDiv}<dd><span data-key="{k}_{page}_{v}" style="background-position:{xPos}px 0;"></span></dd>{delHTML}';
        console.log(that.values)
        var res = iTemplate.makeList(TPL, that.values, function(k, v) {
          // console.log(k,v)
          return {
            k: k,
            v: v,
            page: Math.floor(k / that.spearate),
            xPos: -24 * k,
            seprateDiv: (0 == k % that.spearate && 0 != k && k != that.values.length) ? "</div><div>" : "",
            delHTML: (19 == k % that.spearate || k == (that.values.length - 1)) ? '' : ''
          }
        });
        console.log(document.getElementById('page_emotion'));
        document.getElementById('list_emotion').innerHTML = '<div>' + res + '</div>';
        var nav_span = new Array(Math.ceil(that.values.length / that.spearate));
        document.getElementById('nav_emotion').innerHTML = '<span class="on">' + nav_span.join("</span><span>" + '</span>');
        that.bind(data);
        window.swiper = new Swipe(document.getElementById('page_emotion'), {
          speed: 500,
          callback: function() {
            // $("#nav_emotion span").removeClass("on").eq(this.index).addClass("on")
            var list = document.getElementById('nav_emotion').children;
            for(var i = 0;i<list.length;i++){
              list[i].classList.remove("on");
            }
            document.getElementById('nav_emotion').children[this.index].classList.add("on");
          }
        });
        return that
      },
      bind: function(data) {
        document.getElementById('list_emotion').addEventListener("click", function(evt) {
          if ("SPAN" == evt.target.tagName) {
            var val = evt.target.getAttribute("data-key").split('_');
            myInput.listen(this, {
              keyCode: -10,
              srcElement: document.getElementById("form_article"),
              value: val[2],
              imgUrl: '../static/images/emoji/' + val[0] + ".gif",
              vue:data
            });
            this.focus()
          }
        },false)
      }
    }
    return new fe()
  })();