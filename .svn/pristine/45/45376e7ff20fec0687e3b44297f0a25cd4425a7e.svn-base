<?php
namespace app\common\enum;

class ErpPurchaseOrderDataEnum
{
	const STATUS_NO  					= 0; //未入库
	const STATUS_YES  					= 1; //正常入库
	const STATUS_CANCEL  				= 2; //核销

	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_NO 	=> '未入库',
			self::STATUS_YES   	=> '正常入库',
			self::STATUS_CANCEL => '核销',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }

	
	
}