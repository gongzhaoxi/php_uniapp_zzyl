import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex) // vue的插件机制

var $token = uni.getStorageSync('$token');
var $user = uni.getStorageSync('$user');	
var $web = uni.getStorageSync('$web');

// Vuex.Store 构造器选项
const store = new Vuex.Store({
    // 为了不和页面或组件的data中的造成混淆，state中的变量前面建议加上$符号
    state: {
        // 用户信息
        $user: $user?$user:{},
		$token: $token?$token:'',
		$web: $web?$web:[]
    },
	mutations: {
		// payload为用户传递的值，可以是单一值或者对象
		login(state, payload) {
			state.$token = payload.token;
			state.$user = payload.user;
			uni.setStorageSync('$token', payload.token)	;
			uni.setStorageSync('$user', payload.user);				
		},
		setWebConfig(state, payload) {			
			state.$web = payload;
			uni.setStorageSync('$web', payload);
		},
		logout(state, payload) {
			state.$token = '';
			state.$user = {};
			uni.removeStorageSync('$token')	;
			uni.removeStorageSync('$user');				
		},
	}
})

export default store
