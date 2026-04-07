<?php
namespace app\common\enum;

class ErpProductStockEnum
{

	const STATUS_NO  					= 0; //未审批
	const STATUS_YES  					= 1; //已审批
	
	const TYPE_PRODUCE  				= 11; //生产入库
	const TYPE_CANCEL_PRODUCE  			= 12; //取消生产入库
	const TYPE_PURCHASE					= 20; //采购入库
	const TYPE_RETURN  					= 30; //退货入库

	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_NO 	=> '未审批',
			self::STATUS_YES   	=> '已审批',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getTypeDesc($value = true)
    {
        $desc = [
			self::TYPE_PRODUCE 			=> '生产入库',
			self::TYPE_CANCEL_PRODUCE	=> '取消生产入库',
			self::TYPE_PURCHASE   		=> '采购入库',
			self::TYPE_RETURN   		=> '退货入库',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }	
}