<template name="upload">
	<view>
		<helang-compress ref="helangCompress"></helang-compress>
	</view>
</template>
<script>
	import helangCompress from '@/components/helang-compress/helang-compress.vue';

	export default {
		components: {
			helangCompress
		},
		name: "upload",
		//属性
		props: {

		},
		data() {
			return {

			}
		},
		//组件生命周期
		created: function(e) {

		},
		watch: {},
		methods: {
			upload(count) {
				return new Promise((resolve, reject) => {
					uni.chooseImage({
						//count: count,
						sizeType: ['original', 'compressed'],
						sourceType: ['album'],
						success: async (res) => {							
							if(res.tempFilePaths.length > count){
								this.$u.toast('最多只能上传'+count+'张图');
								return false;
							}
							
							this.$refs.helangCompress.batchCompress({
							    batchSrc:res.tempFilePaths,
							    maxSize:1024,
							    fileType:'jpg',
							    quality:0.85,
							    progress:(res)=>{
							      
							    }
							}).then((result)=>{
							    // 压缩成功回调
								//console.log(result);								
								this.$u.manageApi.getUploaderOption({
									act: 'image-upyun'
								}).then(uploaderOption => {
									// #ifdef H5
									
									// #endif
									this.getUploadFile(uploaderOption,result).then(res => {
										resolve(res);
									})
								})					
							}).catch((err)=>{
							    // 压缩成功回调
							})
						},
						fail: (res) => {
							console.log(res);
						}
					});
				})
			},
			getUploadFile(uploaderOption,result){
				let files = [];
				return new Promise(async (resolve, reject) => {
					for (let i = 0; i < result.length; i++) {
						let file = await this.uploadFile(uploaderOption,result[i]);						
						files.push(file);
						if (files.length === result.length) {
							resolve(files);
						}
					}
				})
			},
			uploadFile(uploaderOption,file){
				return new Promise((resolve, reject) => {					
					uni.uploadFile({
						url: uploaderOption['upload_url'], //仅为示例，非真实的接口地址
						file: file,
						filePath:file,
						name: 'file',
						formData: uploaderOption,
						success: (uploadFileRes) => {
							var data = this.getUploadResult(uploaderOption, 'image-upyun', JSON.parse(uploadFileRes.data));
							if(data.error == ''){
								resolve(data);
							}else{
								this.$u.toast(data.error);
							}
						},
						fail: (error) => {
							console.log('error', error);
						},
					});
				})
			},
			
			getUploadResult(uploaderOption, act, res) {
				let arr = act.split("-");
				let result = {
					error: '',
					file: '',
					file_link: ''
				};

				if (arr[1] == 'local') {
					if (res.code == 1) {
						result.file = res.file;
					} else {
						result.error = res.msg;
					}
				} else if (arr[1] == 'upyun') {
					if (res.code == 200) {
						result.file = res.url;
					} else {
						result.error = res.message;
					}
				} else if (arr[1] == 'ali') {

				} else if (arr[1] == 'qiniu') {
					if (res.key) {
						result.file = res.key;
					} else {
						result.error = res.error;
					}
				}
				if (result.file) {
					result.file_link = uploaderOption['browse_url'] + result.file;
				}
				return result;
			},
			dataURLtoBlob(dataurl) {
				var arr = dataurl.split(','),
					mime = arr[0].match(/:(.*?);/)[1],
					bstr = atob(arr[1]),
					n = bstr.length,
					u8arr = new Uint8Array(n) //8位无符号整数，长度1个字节
				while (n--) {
					u8arr[n] = bstr.charCodeAt(n)
				}
				return new Blob([u8arr], {
					type: mime,
				})
			},
		},
	}
</script>

<style>


</style>
