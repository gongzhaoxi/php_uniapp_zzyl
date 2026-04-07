<template>
	<view class="content">
		<u--form labelPosition="top" labelWidth="300" :model="form" ref="uForm">
			<u-form-item label="1.产品出仓（发货）单号" :required="true" :borderBottom="false">
				<u--input v-model="form.shipping_sn" @blur="getProduce" border="bottom"></u--input>
				<u-icon @click="scanSn('shipping_sn')" slot="right" size="30" name="scan"></u-icon>
			</u-form-item>
			<u-form-item label="">
				<view class=" u-flex u-row-between" style="width:100%;">
					<view class="">2.产品出仓（发货）单</view>
					<view @click="detail" class="detail">【查看明细】</view>
				</view>
			</u-form-item>
			<u-form-item label="3.物流单据" :required="true" :borderBottom="false">
				<upload v-on:cb="photoChange" :fileList="photo" ref="upload"></upload>
			</u-form-item>
			<u-form-item label="4.物流单号" :required="true" :borderBottom="false">
				<u--input v-model="form.shipping_num" border="bottom"></u--input>
			</u-form-item>
		</u--form>
		<u-button type="primary" text="提交" customStyle="margin-top: 20px" @click="submit"></u-button>
		<u-popup :show="show" mode="bottom" :closeable="true" @close="show=false" :closeOnClickOverlay="true">
			<view class="produce">
				<view v-for="(item,index) in produce" :key="index" class="item">
					<view class="index">{{index+1}}.</view>
					<view class="label">销售合同号</view>
					<view class="value">{{item.order.order_sn}}</view>
					<view class="label">客户名称/订货单位</view>
					<view class="value">{{item.order.customer_name}}</view>
					<view class="label">收获地址</view>
					<view class="value">{{item.order.address}}</view>
					<view class="label">产品编号</view>
					<view class="produce_sn u-flex">
						<view class="button" v-for="(item1,index2) in item.list" :key="index">
							<u-button :text="item1.produce_sn" size="mini" type="primary"></u-button>
						</view>
					</view>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import {
		apiPdoduce,
		apiConfirm
	} from "@/common/api/shipping.js"
	import upload from '@/components/upload/upload.vue';
	import clientUtil from '@/util/clientUtil.js';
	import {
		ClientEnum
	} from '@/common/enums/clientEnum'

	export default {
		components: {
			upload
		},
		data() {
			return {
				produce: [],
				form: {
					shipping_sn: '',
					shipping_num: '',
					shipping_photo: []
				},
				type: [],
				photo: [],
				terminal: '',
				show: false
			}
		},
		onLoad() {
			this.terminal = clientUtil.fetchClient();
		},
		onShow() {
			var shipping_sn = uni.getStorageSync('scan_shipping_sn');
			if (shipping_sn) {
				this.form.shipping_sn = shipping_sn;
				uni.removeStorageSync('scan_shipping_sn');
				this.getProduce();
			}
		},
		methods: {
			photoChange(file, fileList) {
				this.form.shipping_photo = file;
			},
			submit() {
				if (this.$u.test.isEmpty(this.form.shipping_sn) == true) {
					this.$u.toast('产品出仓（发货）单号不能为空');
					return;
				}
				if (this.$u.test.isEmpty(this.form.shipping_num) == true) {
					this.$u.toast('物流单号不能为空');
					return;
				}
				if (this.form.shipping_photo.length == 0) {
					this.$u.toast('请上传物流单据');
					return;
				}
				apiConfirm(this.form).then(res => {
					this.$u.toast(res.msg);
					if (res.code == 200) {
						setTimeout(function() {
							uni.reLaunch({
								url: '/pages/index/shipping',
							});
						}, 3000)
					}
				})
			},
			getProduce() {
				apiPdoduce(this.form).then(res => {
					this.produce = res.data;
				})
			},
			detail() {
				this.show = true;
			},
			scanSn: function(key) {
				var that = this;
				//#ifdef H5
				if (this.terminal == ClientEnum.OA_WEIXIN) {
					this.$u.wx.scanQRCode(function(res) {
						var arr = (res.resultStr).split(',');
						var str = '';
						if(arr.length == 1){
							str = arr[0];
						}else{
							str = arr[1];
						}
						that.form[key] = str;
						that.getProduce();
					})
				} else {
					uni.navigateTo({
						url: '/pages/scan/scan?key=scan_' + key,
					})
				}
				return;
				//#endif
				//#ifndef H5	
				wx.scanCode({
					onlyFromCamera: true,
					success: function(res) {
						if (res.result != '') {
							that.form[key] = res.result;
							that.getProduce();
						} else {
							uni.showToast({
								title: '没有数据！',
							})
						}
					}
				})
				//#endif
			},
		}
	}
</script>

<style lang="scss">
	.content {
		padding: 20rpx 30rpx;

		.detail {
			color: rgb(24, 144, 255);
		}

		.produce {
			padding: 20rpx;

			.item {
				margin-bottom: 20rpx;

				.index {}

				.label {
					color: rgb(153, 153, 153);
					margin: 20rpx 0;
					font-size: 14px;
				}

				.value {
					color: rgb(51, 51, 51);
					font-size: 14px;
				}
				.produce_sn {
					flex-direction: row;
					flex-wrap: wrap;
					align-items: center;
					.button{
						margin: 0 15px 15px 0;
					}
				}
			}
		}
	}

</style>