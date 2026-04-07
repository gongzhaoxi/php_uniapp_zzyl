<?php
namespace app\common\enum;

class ErpOrderProduceProcessEnum
{

	const TYPE_FINISH  				= 1; //已完成
	const TYPE_LACK  					= 2; //缺料中
	const TYPE_ERROR  				= 3; //其它异常

	public static function getTypeDesc($value = true)
    {
        $desc = [
			self::TYPE_FINISH 	=> '已完成',
			self::TYPE_LACK   	=> '缺料中',
            self::TYPE_ERROR   	=> '其它异常',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
}