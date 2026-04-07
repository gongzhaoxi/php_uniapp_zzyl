<?php
declare (strict_types = 1);
namespace app\common\model;
use app\common\model\BaseModel;

class AdminPhoto extends BaseModel
{

    public function getTypeDescAttr($value,$data)
    {
        $type = ['1' => '本地', '2' => '阿里云','3'=>'七牛云'];
        return $type[$data['type']];
    }
	
	public function getCreateDateAttr($value,$data){
		return substr($data['create_time'],0,10);
	}
	
	public function getLinkAttr($value,$data)
    {
        return get_browse_url($data['href']);
    }
	

    // 添加
    public static function add($info,$href,$path,$type)
    {
        return self::create([
            'name' => $info->getOriginalName(),
            'href' => $href,
            'path' => $path,
            'type' => $type,
            'ext' => $info->getOriginalExtension(),
            'mime' => $info->getOriginalMime(),
            'size' => $info->getSize(),
			'admin' => get_field('admin_admin',session('admin.id'),'nickname')
        ]);
    }
	
}