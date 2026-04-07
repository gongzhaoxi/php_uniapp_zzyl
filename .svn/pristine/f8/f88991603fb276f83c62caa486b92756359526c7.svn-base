let baseUrl	= 'https://zcyl.ecloudm.com/index';
//let baseUrl	= 'http://www.zhongchuang.cc/index';
// #ifdef H5
if(window.location.host.substr(0,9) != 'localhost'){
	baseUrl = window.location.protocol +'//'+ window.location.host+'/index';
}
// #endif

module.exports = {
	//接口根域名
    baseUrl: baseUrl,
	// 公众号appid
	wechatAppId: 'wx8dcae19c9b23cbda',
	// 公众号授权方式snsapi_base或snsapi_userinfo
	wechatScope: 'snsapi_userinfo',
	noNeedLogin:['/login/login','/login/oaLogin','/login/mnpLogin','/index/web']
}
