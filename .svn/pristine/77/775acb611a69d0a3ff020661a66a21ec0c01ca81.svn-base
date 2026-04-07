<template>
	<view class="content">
		<view class="logout">
			<view class="view">
				<u-button @click="logout" text="退出登录" size="mini" icon="reload" type="error" ></u-button>
			</view>			
		</view>
		<u--form labelPosition="top" labelWidth="300" :model="form" ref="uForm">
			<u-form-item label="1.产品编码" :required="true" :borderBottom="false">
				<u--input @confirm="getData()" @blur="getData()" v-model="form.produce_sn"  border="bottom"></u--input>
				<u-icon @click="scanSn('produce_sn')" slot="right" size="30" name="scan"></u-icon>
			</u-form-item>
			<u-form-item label="2.工序选择" :required="true">
				<u-radio-group v-model="form.process_id">
					<view class="radio_item w1" v-for="(item, index) in process">
						<u-radio :key="index" :label="item.name" :name="item.id">
						</u-radio>
					</view>
				</u-radio-group>
			</u-form-item>
			<u-form-item label="3.进度结果" :required="true">
				<u-radio-group v-model="form.type">
					<view class="radio_item w2" v-for="(item, index) in type">
						<u-radio :key="index" :label="item" :name="index">
						</u-radio>
					</view>
				</u-radio-group>
			</u-form-item>
			<u-form-item label="4.物料名称" :required="true" :borderBottom="false" v-if="form.type == 2">
				<u--input  v-model="form.material_name"  border="bottom"></u--input>
				<u-icon @click="scanSn('material_sn')" slot="right" size="30" name="scan"></u-icon>
			</u-form-item>
			<u-form-item label="5.报缺数量" :required="true" :borderBottom="false" v-if="form.type == 2">
				<u--input v-model="form.lack_num"  border="bottom"></u--input>
			</u-form-item>			
			<u-form-item label="6.报缺原因" :required="true" :borderBottom="false" v-if="form.type == 2">
				<u--input v-model="form.error"  border="bottom"></u--input>
			</u-form-item>			
			<u-form-item @click="showCheckType=true;" label="4.检验类型" :required="true" :borderBottom="false" v-if="form.type == 3">
				<u--input v-model="form.check_type" readonly border="bottom"></u--input>
				<u-icon slot="right" name="arrow-right"></u-icon>
			</u-form-item>				
			<u-form-item label="5.不良说明" :required="true" :borderBottom="false" v-if="form.type == 3">
				<u--input v-model="form.error"  border="bottom"></u--input>
			</u-form-item>
			<u-form-item label="6.附图" :required="true" :borderBottom="false" v-if="form.type == 3">
				<upload v-on:cb="photoChange" :fileList="photo" ref="upload"></upload>
			</u-form-item>
		</u--form>
		<u-button v-if="form.type != 1" type="primary" text="提交" customStyle="margin-top: 20px" @click="submit"></u-button>
		<u-button v-if="form.type == 1" type="primary" text="下一步" customStyle="margin-top: 20px" @click="submit"></u-button>
		<u-picker ref="uPicker3" @cancel="showCheckType=false" @confirm="confirmCheckType" :show="showCheckType" :columns="check_type" ></u-picker>
	</view>
</template>

<script>
	import {
		apiList,
		apiErrorAdd
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
					process_id: '',
					error: '',
					type: '1',
					photo: [],
					material_sn:'',
					material_name:'',
					lack_num:'',
					check_type:'',
				},
				type: [],
				photo: [],
				terminal: '',
				check_type:[],
				showCheckType:false
			}
		},
		onLoad() {
			this.terminal = clientUtil.fetchClient();
		},
		onShow() {
			var produce_sn = uni.getStorageSync('scan_produce_sn');
			if (produce_sn) {
				this.form.produce_sn = produce_sn;
				uni.removeStorageSync('scan_produce_sn');
			}
			var material_sn = uni.getStorageSync('scan_material_sn');
			if (material_sn) {
				this.form.material_sn = material_sn;
				uni.removeStorageSync('scan_material_sn');
			}		
			this.getData();
		},
		methods: {
			logout(){
				var that = this;
				uni.showModal({
					title: '提示',
					content: '确认退出登录？',
					success: function (res) {
						if (res.confirm) {
							that.$store.commit('logout')
							uni.reLaunch({
								url: '/pages/index/login'
							});
						}
					}
				});
			},
			getData(){
				apiList({produce_sn:this.form.produce_sn,material_sn:this.form.material_sn}).then(res => {
					this.process = res.data.list;
					this.type = res.data.type;
					this.check_type = [res.data.check_type];
					
					if(res.data.material && res.data.material.id){
						this.form.material_name = res.data.material.name;
					}else if(this.form.material_sn){
						this.form.material_sn = '';
						this.$u.toast('没有此物料');
					}
				})
			},
			photoChange(file, fileList) {
				this.form.photo = file;
			},
			confirmCheckType(e){
				this.form.check_type = e.value[0]
				this.showCheckType = false
			},
			submit() {
				if (this.form.type == 1) {
					if (this.$u.test.isEmpty(this.form.produce_sn) == true) {
						this.$u.toast('产品编码不能为空');
						return;
					}
					if (this.$u.test.isEmpty(this.form.process_id) == true) {
						this.$u.toast('请选择工序');
						return;
					}
				}
				if (this.form.type == 2) {
					if (this.$u.test.isEmpty(this.form.material_name) == true) {
						this.$u.toast('物料名称不能为空');
						return;
					}
					if (this.$u.test.isEmpty(this.form.lack_num) == true) {
						this.$u.toast('报缺数量不能为空');
						return;
					}
					if (this.$u.test.isEmpty(this.form.error) == true) {
						this.$u.toast('报缺原因不能为空');
						return;
					}
				}
				if (this.form.type == 3) {
					if (this.$u.test.isEmpty(this.form.produce_sn) == true) {
						this.$u.toast('产品编码不能为空');
						return;
					}
					if (this.$u.test.isEmpty(this.form.check_type) == true) {
						this.$u.toast('请选择校验类型');
						return;
					}					
					if (this.$u.test.isEmpty(this.form.error) == true) {
						this.$u.toast('不良说明不能为空');
						return;
					}
					if (this.form.photo.length == 0) {
						this.$u.toast('请上传附图');
						return;
					}
				}
				if (this.form.type == 1) {
					uni.navigateTo({
						url: '/pages/index/process?produce_sn=' + this.form.produce_sn + '&process_id=' + this.form.process_id,
					})
				}else{
					apiErrorAdd(this.form).then(res => {
						this.$u.toast(res.msg);
						if (res.code == 200) {
							setTimeout(function() {
								uni.reLaunch({
									url: '/pages/index/index',
								});
							}, 3000)
						}
					})
				}
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
						that.getData();
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
							that.getData();
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
	.logout {
		text-align: right;
		margin:0 0 0rpx 0;
		.view{
			width:200rpx;
			display: inline-block;
		}
	}
	
	.content {
		padding: 0rpx 30rpx;

		.radio_item {
			display: inline-block;
			margin-bottom: 8px;
		}

		.w1 {
			width: 50%;
		}

		.w2 {
			width: 33.33%;
		}

		.u-radio-group--row {
			display: block;
		}
	}
</style>