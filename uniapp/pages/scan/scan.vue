<template>
	<view class="" v-if="key">
		<mumu-get-qrcode @success='qrcodeSucess' @error="qrcodeError"></mumu-get-qrcode>
	</view>
</template>

<script>
	import mumuGetQrcode from '@/components/mumu-getQrcode/mumu-getQrcode.vue'
	export default {
		components: {
			mumuGetQrcode
		},
		data() {
			return {
				key: 'scar_str'
			}
		},
		onLoad(option) {
			this.key = option.key
			uni.removeStorageSync(this.key);
			//uni.setStorageSync(this.key, 'ZSB-A-000');
		},
		methods: {
			qrcodeSucess(data) {
				uni.setStorageSync(this.key, data);
				uni.navigateBack({})
			},
			qrcodeError(err) {
				console.log(err)
				uni.showModal({
					title: '摄像头授权失败',
					content: '摄像头授权失败，请检测当前浏览器是否有摄像头权限。',
					success: () => {
						uni.navigateBack({})
					}
				})
			}
		}
	}
</script>

<style>

</style>