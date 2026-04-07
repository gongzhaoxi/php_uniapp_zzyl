<?php
namespace app\common\enum;

class ErpOrderShippingEnum
{
	
	//发货状态
	const SHIPPING_STATUS_NO   				= 10;  //未打发货单
	const SHIPPING_STATUS_PRINTED   		= 20;  //已打发货单
	const SHIPPING_STATUS_FINISH    		= 30;  //已出库
	
	//审核状态
	const APPROVE_STATUS_NO    				= 0;  //未审核
	const APPROVE_STATUS_YES    			= 1;  //已审核

	public static function getShippingStatusDesc($value = true)
    {
        $desc = [
			self::SHIPPING_STATUS_NO  		=> '未打发货单',
			self::SHIPPING_STATUS_PRINTED  	=> '已打发货单',
			self::SHIPPING_STATUS_FINISH	=> '已出库',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }	
	
	public static function getApproveStatusDesc($value = true)
    {
        $desc = [
			self::APPROVE_STATUS_NO  		=> '未审核',
			self::APPROVE_STATUS_YES  		=> '已审核',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }		
	
	
	
}