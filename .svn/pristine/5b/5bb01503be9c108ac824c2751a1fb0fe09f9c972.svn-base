<?php
namespace app\common\enum;

class ErpMaterialEnterMaterialEnum
{

	const STATUS_HANDLE  				= 1; //处理中
	const STATUS_PART  					= 2; //部分入库
	const STATUS_FINISH  				= 3; //全部入库
	const STATUS_CANCEL  				= 4; //已作废
	const STATUS_RETURN  				= 5; //已退货
	
	const CHECK_STATUS_HANDLE  			= 1; //未发起质检
	const CHECK_STATUS_NOTICED  		= 2; //已发起质检
	const CHECK_STATUS_PART  			= 3; //质检中
	const CHECK_STATUS_FINISH  			= 4; //质检完成

	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_HANDLE 	=> '处理中',
			self::STATUS_PART   	=> '部分入库',
			self::STATUS_FINISH   	=> '全部入库',
            self::STATUS_CANCEL   	=> '整批不合格',
			//self::STATUS_RETURN   	=> '已退货',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	
	public static function getCheckStatusDesc($value = true)
    {
        $desc = [
			self::CHECK_STATUS_HANDLE 	=> '未发起质检',
			self::CHECK_STATUS_NOTICED 	=> '已发起质检',
			self::CHECK_STATUS_PART   	=> '质检中',
			self::CHECK_STATUS_FINISH   => '质检完成',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }	
	
	
}