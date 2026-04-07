<script>

	export default {
		onLaunch: function() {
			uni.$u.http.post('/index/web').then((res) => {
				this.$store.commit('setWebConfig', res.data)
			}).catch(() =>{
				
			})
		},
		onShow: function() {
			
		},
		onHide: function() {
			
		}
	}
</script>

<style lang="scss">
	/*每个页面公共css */
	@import "@/uni_modules/uview-ui/index.scss";
	@import "common/common.scss";
	
</style>
