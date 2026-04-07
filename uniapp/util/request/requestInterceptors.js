/**
 * 请求拦截
 * @param {Object} http
 */
import configs from '@/common/config'

module.exports = (vm) => {
    uni.$u.http.interceptors.request.use((config) => { // 可使用async await 做异步操作
        // 初始化请求拦截器时，会执行此方法，此时data为undefined，赋予默认{}
        
		config.data = config.data || {}
        // 可以在此通过vm引用vuex中的变量，具体值在vm.$store.state中
        // console.log(vm.$store.state);
		var token = vm.$store.state.$token;
		if(!token && configs.noNeedLogin.indexOf(config.url) == -1){
			uni.reLaunch({
				url: '/pages/index/login'
			});
			return Promise.reject(config)
		}

		config.header.token = token

        return config
    }, (config) => // 可使用async await 做异步操作
        Promise.reject(config))
}
