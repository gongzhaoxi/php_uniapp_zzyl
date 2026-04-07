<template>
	<view class="content">
		<view class="produce" v-if="produce&&produce.id">
			<u-row gutter="16">
				<u-col span="12">
					<view class="item" v-if="follow&&follow.id">
						生成地点：{{follow.address}}
					</view>
				</u-col>
			</u-row>
			<u-row gutter="16">
				<u-col span="12">
					<view class="item" v-if="follow&&follow.id">
						生成依据：{{follow.according}}
					</view>
				</u-col>
			</u-row>
			<u-row gutter="16">
				<u-col span="6">
					<view class="item" >产品型号：{{produce.order_product.product_model}}{{produce.order_product.product_specs}}</view>
				</u-col>
				<u-col span="6">
					<view class="item">机器唯一码：{{produce.produce_sn}}</view>
				</u-col>
			</u-row>
			<u-row gutter="16">
				<u-col span="6">
					<view class="item" v-if="produce.order">销售合同号：{{produce.order.order_sn}}</view>
				</u-col>
				<u-col span="6">
					<view class="item" v-if="produce.order">生产数量：{{produce.order.order_product_num}}</view>
				</u-col>
			</u-row>
			<u-row gutter="16">
				<u-col span="6">
					<view class="item">上线日期：{{produce.create_time.substr(0,10)}}</view>
				</u-col>
				<u-col span="6">
					<view class="item" v-if="produce.order">客户名称：{{produce.order.customer_name}}</view>
				</u-col>
			</u-row>
			<view style="font-size: 12px;margin:20rpx 0;line-height:22px;" v-if="produce.order_product">
				<rich-text :nodes="produce.order_product.project_html"></rich-text>
			</view>
		</view>
		<u--form labelPosition="left" labelWidth="80" :model="form" ref="uForm">
			<u-divider text="产品名称" textPosition="left" v-if="follow_product.length>0"></u-divider>
			<view class="product">
				<view class="item u-flex  " v-for="(item, index) in follow_product">
					<image mode="aspectFit" :src="item.image"></image>
					<view class="view">
						<view class="title u-flex u-row-between">
							<view>{{item.title}}</view>
							<u-checkbox label="" @change="changeCheck($event,item)" :name="item.id"
								v-model="item.checked" :checked="item.checked"> </u-checkbox>
						</view>
						<view class="u-flex form" v-if="item.is_num == 1">
							<view class="label">编号</view>
							<view>
								<u--input :customStyle="inputStyle" v-model="item.num" placeholder="请输入">
									<template slot="suffix">
										<u-icon @tap="scanSn('follow_product','num',index)" name="scan"></u-icon>
									</template>
								</u--input>
							</view>
						</view>
						<view class="u-flex form" v-if="item.is_num == 1">
							<view class="label">换后编号</view>
							<view>
								<u--input :customStyle="inputStyle" v-model="item.after_num" placeholder="请输入">
									<template slot="suffix">
										<u-icon @tap="scanSn('follow_product','after_num',index)" name="scan"></u-icon>
									</template>
								</u--input>
							</view>
						</view>
						<view class="u-flex form">
							<view class="label">备注</view>
							<view>
								<u--input :customStyle="inputStyle" v-model="item.remark" placeholder="请输入">
									<template slot="suffix">
										<view style="width:30rpx;"></view>
									</template>
								</u--input>
							</view>
						</view>
					</view>
				</view>
			</view>
			<u-divider text="工序要求" v-if="follow_process.length>0" textPosition="left"></u-divider>
			<view class="product">
				<view class="item u-flex  " v-for="(item, index) in follow_process">
					<image mode="aspectFit" :src="item.image"></image>
					<view class="view">
						<view class="title u-flex u-row-between">
							<view>{{item.title}}</view>
							<u-checkbox label="" @change="changeCheck($event,item)" :name="item.id"
								v-model="item.checked" :checked="item.checked"> </u-checkbox>
						</view>
						<view class="u-flex form" v-if="item.is_num == 1">
							<view class="label">编号</view>
							<view>
								<u--input :customStyle="inputStyle" v-model="item.num" placeholder="请输入">
									<template slot="suffix">
										<u-icon @tap="scanSn('follow_process','num',index)" name="scan"></u-icon>
									</template>
								</u--input>
							</view>
						</view>
						<view class="u-flex form" v-if="item.is_num == 1">
							<view class="label">换后编号</view>
							<view>
								<u--input :customStyle="inputStyle" v-model="item.after_num" placeholder="请输入">
									<template slot="suffix">
										<u-icon @tap="scanSn('follow_process','after_num',index)" name="scan"></u-icon>
									</template>
								</u--input>
							</view>
						</view>
						<view class="u-flex form">
							<view class="label">备注</view>
							<view>
								<u--input :customStyle="inputStyle" v-model="item.remark" placeholder="请输入">
									<template slot="suffix">
										<view style="width:30rpx;"></view>
									</template>
								</u--input>
							</view>
						</view>
					</view>
				</view>
			</view>
		</u--form>
		<view class="btn" v-if="produce&&produce.id">
			<view class="u-flex u-row-right">
				<view class="button-item"><u-button type="success" text="完成工序" @click="submit1"></u-button></view>
				<view class="button-item" v-if="follow_product.length>0 || follow_process.length>0"><u-button type="primary" text="保存随工单" @click="submit2"></u-button></view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		apiFollowSave,
		apiAdd,
		apiFollow
	} from "@/common/api/process.js"
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
				process: [],
				form: {
					produce_sn: '',
					bom_sn: '',
					process_id: '',
					error: '',
					type: '1',
					photo: []
				},
				terminal: '',
				produce: {},
				follow: {},
				follow_product: [],
				follow_process: [],
				inputStyle: {
					background: '#fff',
					height: '20px'
				},
				scanField: '',
				scanIndex: '',
				scanKey: ''
			}
		},
		onLoad(option) {
			this.form.produce_sn = option.produce_sn;
			this.form.process_id = option.process_id;
			this.terminal = clientUtil.fetchClient();
			this.getData();
		},
		onShow() {
			if (this.scanField !== '' && this.scanKey !== '' && this.scanIndex !== '') {
				var key = 'scan_' + this.scanKey + this.scanField + this.scanIndex;
				var value = uni.getStorageSync(key);
				if (value) {
					this[this.scanKey][this.scanIndex][this.scanField] = value;
				}
				uni.removeStorageSync(key);
			}
			this.scanField = '';
			this.scanKey = '';
			this.scanIndex = '';
		},
		methods: {
			getData() {
				apiFollow({
					produce_sn: this.form.produce_sn,
					process_id: this.form.process_id
				}).then(res => {
					if(res.code == 200){
						this.produce = res.data.produce;
						this.follow = res.data.follow;
						this.follow_product = res.data.follow_product;
						this.follow_process = res.data.follow_process;
						if(res.data.follow&&res.data.follow.id){
							uni.setNavigationBarTitle({
								title: res.data.follow.name
							});
						}
					}else{
						this.$u.toast(res.msg);
					}
				})
			},
			changeCheck(e, item) {
				item.checked = e;
			},
			submit1() {
				if (this.$u.test.isEmpty(this.form.produce_sn) == true) {
					this.$u.toast('产品编码不能为空');
					return;
				}
				if (this.$u.test.isEmpty(this.form.process_id) == true) {
					this.$u.toast('请选择工序');
					return;
				}
				apiAdd(this.form).then(res => {
					this.$u.toast(res.msg);
					if (res.code == 200) {
						setTimeout(function() {
							uni.reLaunch({
								url: '/pages/index/index',
							});
						}, 3000)
					}
				})
			},
			submit2() {
				var data  = [];
	
				for (let i = 0; i < this.follow_product.length; i++) {
					if (this.follow_product[i]['checked']) {
						if(this.follow_product[i]['is_num'] == 1){
							if (this.$u.test.isEmpty(this.follow_product[i]['num']) == true) {
								//this.$u.toast(this.follow_product[i]['title'] + '编号不能为空');
								//return;
							}
							if (this.$u.test.isEmpty(this.follow_product[i]['after_num']) == true) {
								//this.$u.toast(this.follow_product[i]['title'] + '换后编号不能为空');
								//return;
							}
						}					
						data.push(this.follow_product[i]);
					}
				}
				for (let i = 0; i < this.follow_process.length; i++) {
					if (this.follow_process[i]['checked']) {
						if(this.follow_product[i]['is_num'] == 1){
							if (this.$u.test.isEmpty(this.follow_process[i]['num']) == true) {
								//this.$u.toast(this.follow_process[i]['title'] + '编号不能为空');
								//return;
							}
							if (this.$u.test.isEmpty(this.follow_process[i]['after_num']) == true) {
								//this.$u.toast(this.follow_process[i]['title'] + '换后编号不能为空');
								//return;
							}
						}
						data.push(this.follow_process[i]);
					}
				}
				
				if (this.$u.test.isEmpty(data) == true) {
					this.$u.toast('请选择数据');
					return;
				}			
				apiFollowSave({
					data: data
				}).then(res => {
					this.$u.toast(res.msg);
					this.getData();
				})
			},
			scanSn: function(key, field, index) {
				this.scanKey = key;
				this.scanField = field;
				this.scanIndex = index;
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
						that[key][index][field] = str;
					})
				} else {
					uni.navigateTo({
						url: '/pages/scan/scan?key=scan_' + key + field + index,
					})
				}
				return;
				//#endif
				//#ifndef H5	
				wx.scanCode({
					onlyFromCamera: true,
					success: function(res) {
						if (res.result != '') {
							that[key][index][field] = res.result;
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
	.produce {
		margin-top: 20rpx;

		.item {
			font-size: 12px;
		}
	}

	.product {
		.item {
			margin-bottom: 20rpx;
			background: rgb(242, 242, 242);
			max-height: 350rpx;

			.title {
				font-size: 32rpx;
				font-weight: 600;
			}

			image {
				width: 200rpx;
				margin: 0 20rpx;
			}

			.form {
				margin: 10rpx 10rpx 0rpx 10rpx;
				font-size: 26rpx;

				.label {
					width: 150rpx;
				}
			}
		}
	}

	.btn {
		position: fixed;
		bottom: 0;
		background: #fff;
		width: 690rpx;
		padding: 20rpx 0;

		.button-item {
			margin-left: 20rpx;
		}
	}

	.content {
		padding: 0rpx 30rpx 130rpx 30rpx;
	}
</style>