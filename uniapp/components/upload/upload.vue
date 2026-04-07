<template name="upload">
	<view>
		<u-upload :fileList="fileList" accept="image" :previewFullImage="true" @afterRead="afterRead"
			@delete="deletePic" name="fileList" multiple :maxCount="5"></u-upload>
		<helang-compress ref="helangCompress"></helang-compress>
	</view>
</template>
<script>
	import helangCompress from '@/components/helang-compress/helang-compress.vue';
	import {
		apiUpload
	} from "@/common/api/common.js"
	
	export default {
		components: {
			helangCompress
		},
		name: "upload",
		//属性
		props: {
			fileList: {
				type: Array,
				default () {
					return []
				}
			},
			compress: {
				type: Boolean,
				default: true
			},
		},
		data() {
			return {
				file: []
			}
		},
		//组件生命周期
		created: function(e) {

		},
		watch: {},
		methods: {
			// 删除图片
			deletePic(event) {
				this['fileList'].splice(event.index, 1);
				this['file'].splice(event.index, 1)
			},
			// 新增图片
			async afterRead(event) {
				// 当设置 multiple 为 true 时, file 为数组格式，否则为对象格式
				let lists = [].concat(event.file)
				let fileListLen = this[`${event.name}`].length
				lists.map((item) => {
					this[`${event.name}`].push({
						...item,
						status: 'uploading',
						message: '上传中'
					})
				})
				for (let i = 0; i < lists.length; i++) {
					let result;
					if(this.compress == true){
						let compressResult  = await this.compressPromise(lists[i].url);
						result = await apiUpload(compressResult[0])
					}else{
						result = await apiUpload(lists[i].url)
					}
					
					let item = this[`${event.name}`][fileListLen]
					this[`${event.name}`].splice(fileListLen, 1, Object.assign(item, {
						status: 'success',
						message: '',
						url: result.data.thumb
					}))
					this['file'].splice(fileListLen, 1, result.data.src)
					fileListLen++
				}

				this.$emit("cb",this['file'],this['fileList'])	
			},

			compressPromise(url) {
				return this.$refs.helangCompress.batchCompress({
					batchSrc: [url],
					maxSize: 1024,
					fileType: 'jpg',
					quality: 0.85,
					progress: (res) => {
				
					}
				})
			},

			dataURLtoBlob(dataurl) {
				console.log(dataurl);
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