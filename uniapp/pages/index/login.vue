<template>
	<view class="content">
		<view class="logo">
			<image lazy-load="true" mode="widthFix" :src="$store.state.$web.logo" />
		</view>
		<view class="form">
			<u-form ref="uForm" :model="form">
				<u-form-item left-icon="phone" :left-icon-style="{'color': '#999999', 'font-size': '36rpx'}">
					<u-input v-model="form.mobile" type="text" placeholder="请输入手机号码" />
				</u-form-item>
				<u-form-item left-icon="lock" :left-icon-style="{'color': '#999999', 'font-size': '36rpx'}">
					<u-input v-model="form.sn" type="text" placeholder="请输入身份识别码" />
				</u-form-item>
			</u-form>
			<u-button :customStyle="{marginTop:'20rpx'}" type="primary" @click="onSaLogin()">登录</u-button>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		apiLogin,
		apiOaLogin,
		apiMnpLogin
	} from "@/common/api/login.js"
	import clientUtil from '@/util/clientUtil.js';
	import {
		ClientEnum
	} from '@/common/enums/clientEnum'
	export default {
		data() {
			return {
				form: {
					code: '',
					mobile: '',
					sn: '',
					terminal: '',
					wechat_token: ''
				}
			}
		},
		onLoad() {
			this.form.terminal = clientUtil.fetchClient();
			console.log(this.form);
			if (this.form.terminal == ClientEnum.OA_WEIXIN) {
				apiOaLogin(this.form).then(res => {
					if (res.code == 200) {
						this.loginSuccsss(res.data);
					}
				})
			}
			if (this.form.terminal == ClientEnum.MP_WEIXIN) {
				apiMnpLogin(this.form).then(res => {
					if (res.code == 200) {
						this.loginSuccsss(res.data);
					}
				})
			}
		},
		methods: {
			loginSuccsss(data) {
				this.$store.commit('login', data)
				uni.reLaunch({
					url: '/pages/index/index',
					success: function() {

					}
				});
			},
			onSaLogin() {
				if (uni.$u.test.isEmpty(this.form.mobile)) {
					return uni.$u.toast('手机号码不能为空')
				}
				if (!uni.$u.test.mobile(this.form.mobile)) {
					return uni.$u.toast('手机号码格式错误')
				}				
				if (uni.$u.test.isEmpty(this.form.sn)) {
					return uni.$u.toast('身份识别码不能为空')
				}
				apiLogin(this.form).then(res => {
					if (res.code == 200) {
						this.loginSuccsss(res.data);
					} else {
						uni.$u.toast(res.msg)
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	page {}

	.content {
		padding-top: 50rpx;

		.logo {
			margin: 0 auto;
			padding: 6rpx;
			text-align: center;

			image {
				max-width: 150rpx;
				max-height: 150rpx;
			}
		}

		.form {
			margin: 30px 20px 0;
			padding: 60rpx 60rpx 60rpx 60rpx;
			border-radius: 14rpx;
			background-color: #ffffff;
			box-shadow: 0 2px 14px 0 rgba(0, 0, 0, 8%);
		}
	}
</style>