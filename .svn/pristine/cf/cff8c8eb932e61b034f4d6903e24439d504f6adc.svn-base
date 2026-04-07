<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\AdminPhoto;
use app\common\util\Qiniu as QiniuService;
use app\common\util\Oss as OssService;

class AdminPhotoLogic extends BaseLogic{

	
	// 获取所有路径
   public static function getPath()
   {
        $path = public_path().'upload'.DS;
        foreach (scandir($path) as $k) {
            if(is_dir($path.$k) && $k!="." &&$k!=".."){
                $data[] = ['name'=>$k];
            }
        }
        return ['code'=>0,'data'=>$data];
   }

    // 获取列表
    public static function getList($query=[],$limit=10)
    {
        $map 			= [];
        if(!empty($query['path'])) {
            $map[]		= ['path', '=',$query['path']];
        }
		if(!empty($query['file_type'])) {
			$mime		= ['image/jpeg','image/png','image/gif','image/bmp','image/x-icon','image/webp'];
			if($query['file_type'] == "image"){
				$map[]	= ['mime', 'in',$mime];
			}else{
				$map[]	= ['mime', 'not in',$mime];
			}	
		}
        $list 			= AdminPhoto::where($map)->append(['link'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
    // 添加目录
    public static function goAdd($data)
    {
       //数据验证
		if (!preg_match('/^[a-zA-z]+$/i',$data['name'])) return ['code' => 201, 'msg' => '目录格式不正确'];
		@mkdir(public_path().'upload'.DS.$data['name']);
    }

    // 删除目录
    public static function goDel($name)
    {
        //进行转义，禁止跨目录
        $name 		= str_replace("\\","/",$name);
        try{
            $view 	= public_path().'upload'.DS.$name;
            if (file_exists($view)) delete_dir($view);
            AdminPhoto::where('path',$name)->delete();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除文件
    public static function goRemove($id)
    {
        try{
            self::del($id);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    // 批量删除文件
    public static function goBatchRemove($ids)
    {
        if (!is_array($ids)) return ['msg'=>'数据不存在','code'=>201];
        try{
            foreach ($ids as $k) {
                self::del($k);
            }
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function del($id)
    {
        $photo =  AdminPhoto::find($id);
        if($photo['type'] == 2){
            OssService::alYunDel($photo['href']);
        }elseif($photo['type'] == 3){
            QiniuService::QiniuDel($photo['href']);
        }else{
            //删除本地文件
            $path = '../public'.$photo['href'];
            if (file_exists($path)) unlink($path);
        }
        $photo->delete();
    }
}
