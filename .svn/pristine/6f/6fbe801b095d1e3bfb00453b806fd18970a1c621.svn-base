<?php
namespace app\common\model;

use think\Model;
use app\common\enum\SystemEnum;

/**
 * 基础模型
 * Class BaseModel
 * @package app\common\model
 */
class BaseModel extends Model
{
    public function getDeleteTimeAttr($value, $data)
    {
		if(!$value){
			return '';
		}
		if(is_numeric($value)){
			return date('Y-m-d H:i:s',$value);
		}
        return $value;
    }
	

	public function getSystemNameAttr($value,$data){
		return SystemEnum::getNameDesc($data['system_id']);
	}	
	
}