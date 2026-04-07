<?php
namespace app\common\enum;

class ErpMaterialDiscardMaterialEnum
{

	const STATUS_HANDLE  				= 1; //处理中
	const STATUS_PART  					= 2; //部分出库
	const STATUS_FINISH  				= 3; //全部出库
	const STATUS_CANCEL  				= 4; //已作废

	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_HANDLE 	=> '处理中',
			self::STATUS_PART   	=> '部分出库',
			self::STATUS_FINISH   	=> '全部出库',
            self::STATUS_CANCEL   	=> '已作废',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
}