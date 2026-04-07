/**
 * 响应拦截
 * @param {Object} http 
 */
module.exports = (vm) => {
    uni.$u.http.interceptors.response.use((response) => { /* 对响应成功做点什么 可使用async await 做异步操作*/
        const data = response.data;
		const custom = response.config?.custom
		//console.log(custom);
		if(data.code == 999){
			// 如果没有显式定义custom的toast参数为false的话，默认对报错进行toast弹出提示
			if (custom.toast !== false) {
				vm.$store.commit('logout')
			    uni.$u.toast(data.message);
				uni.reLaunch({
					url: '/pages/index/login'
				});
				return ;
			}
			// 如果需要catch返回，则进行reject
			if (custom?.catch) {
			    return Promise.reject(data)
			} else {
			    // 否则返回一个pending中的promise
			    return new Promise(() => { })
			}
		}
		return data;
    }, (response) => { /*  对响应错误做点什么 （statusCode !== 200）*/
        return Promise.reject(response)
    })
}