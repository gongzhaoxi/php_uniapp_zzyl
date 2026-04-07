<?php
namespace app\common\enum;

class ErpMaterialCheckMaterialEnum
{

	const STATUS_HANDLE  				= 1; //处理中
	const STATUS_FINISH  				= 2; //已盘点
	const STATUS_CANCEL  				= 3; //已作废

	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_HANDLE 	=> '处理中',
			self::STATUS_FINISH   	=> '已盘点',
            self::STATUS_CANCEL   	=> '整批不合格',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
}