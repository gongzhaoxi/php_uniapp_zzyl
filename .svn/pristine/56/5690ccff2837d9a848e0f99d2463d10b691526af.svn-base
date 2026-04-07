<?php
namespace app\common\util;
use app\common\model\AdminPhoto;
use think\exception\ValidateException;

class Upload
{

    //通用上传
    public static function putFile($file, $path='default',$config=[])
    {
        if (!$path) {
            $path 	= 'default';
        }
		$config		= array_merge(config('web'),$config);
		$fileSize	= empty($config['upload_size'])?1024:$config['upload_size'];
		$fileExt	= empty($config['upload_ext'])?'jpg,jpeg,png,bmp,gif,xlsx,docx,txt,doc,xls,ppt,pdf':str_replace('php','jpg',strtolower($config['upload_ext']));
        try {
            validate(['file' => [
                'fileSize' => $fileSize*1024,
                'fileExt' => $fileExt,
                /*'fileMime' => 'image/jpeg,image/png,image/gif,text/plain,
				application/vnd.ms-powerpoint
				application/vnd.ms-excel,
				application/msexcel,
				application/x-excel,
				application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
				application/msword,
				application/vnd.openxmlformats-officedocument.wordprocessingml.document,
				application/vnd.openxmlformats-officedocument.wordprocessingml.template,
				application/vnd.ms-word.document.macroEnabled.12,
				application/vnd.ms-word.template.macroEnabled.12
				',*/
            ]])->check(['file' => $file]);
        } catch (\think\exception\ValidateException $e) {
            return ['msg' => $e->getMessage(), 'code' => 201];
        }
        foreach ($file as $k) {
			$browse 	= get_browse_domain();
            if($config['file-type'] == 2) {
                //阿里云上传
                $res 	= Oss::alYunOSS($k, $k->extension(), $path);
                if($res["code"] == 201) {
                    return ['msg' => '上传失败', 'code' => 201, 'data' => $res["msg"]];
                }
                $name 	= $res['src'];
            }else if($config['file-type'] == 3) {
                //七牛上传
                $res 	= Qiniu::QiniuOSS($k, $k->extension(), $path);
                if($res["code"] == 201) {
                    return ['msg' => '上传失败', 'code' => 201, 'data' => $res["msg"]];
                }
                $name 	= $res['src'];
            }else {
                $name 	= str_replace("\\", "/", '/' . 'upload' . '/' . \think\facade\Filesystem::disk('public')->putFile($path, $k));
            }
			$model 		= AdminPhoto::add($k, $name, $path, $config['file-type']);
			//compressImage('.'.$name,'.'.$name,70);
        }
        return ['msg' => '上传成功', 'code' => 200, 'data' => ['src' => $name, 'thumb' => $browse.$name,'file' => $model]];
    }
}
