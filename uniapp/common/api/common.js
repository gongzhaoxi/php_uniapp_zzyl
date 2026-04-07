const { http } = uni.$u
// 获取菜单

export const apiUpload = (filePath, config = {}) => http.upload('/common/upload', {
    params: {}, /* 会加在url上 */
    // #ifdef APP-PLUS || H5
    files: [], // 需要上传的文件列表。使用 files 时，filePath 和 name 不生效。App、H5（ 2.6.15+）
    // #endif
    // #ifdef MP-ALIPAY
    fileType: 'image/video/audio', // 仅支付宝小程序，且必填。
    // #endif
    filePath: filePath, // 要上传文件资源的路径。
    name: 'file', // 文件对应的 key , 开发者在服务器端通过这个 key 可以获取到文件二进制内容
    // #ifdef H5 || APP-PLUS
    timeout: 60000, // H5(HBuilderX 2.9.9+)、APP(HBuilderX 2.9.9+)
    // #endif
    header: {},  /* 会与全局header合并，如有同名属性，局部覆盖全局 */
    formData: {}, // HTTP 请求中其他额外的 form data
})
