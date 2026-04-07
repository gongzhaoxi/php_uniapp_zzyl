import Vue from 'vue'
import App from './App'

// vuex
import store from './store'

// 引入全局uView
import uView from '@/uni_modules/uview-ui'

import mixin from './common/mixin'

Vue.prototype.$store = store

Vue.config.productionTip = false

App.mpType = 'app'
Vue.use(uView)

// #ifdef MP
// 引入uView对小程序分享的mixin封装
const mpShare = require('@/uni_modules/uview-ui/libs/mixin/mpShare.js')
Vue.mixin(mpShare)
// #endif

Vue.mixin(mixin)

const app = new Vue({
    store,
    ...App
})

// 引入请求封装
require('./util/request/index')(app)

// 微信SDK
// #ifdef H5
import weixin from '@/util/weixin/jwx.js'
Vue.use(weixin, app)
// #endif


Vue.prototype.routeTo = function(link,params={},type='navigateTo') {
	if (!link) {
		return false;
	}
	if(link.substr(0, 4) == 'http'){
		uni.navigateTo({
			url: '/pages/index/web?link='+encodeURIComponent(link)
		});
		return;
	}
	let tabBarLinks = [
		'pages/index/index',
		'pages/index/backlog',
		'pages/index/my'
	];
	let check_str = link.substr(0, 1) != '/' ? link : link.substr(1);
	if (tabBarLinks.indexOf((check_str.split('?')[0])) > -1) {
		type = 'switchTab';
	}
	
	uni.$u.route({
		type:type,
		url: link,
		params: params
	})
}

app.$mount()
