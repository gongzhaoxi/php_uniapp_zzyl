<?php
namespace app\common\enum;

class ErpPurchaseOrderEnum
{

	const STATUS_NO  					= 0; //未审批
	const STATUS_YES  					= 1; //已审批
	const STATUS_CANCEL  				= 2; //已作废
	const STATUS_FINISH  				= 3; //已完成
	
	const SUPPLIER_STATUS_WAIT_SEND  	= 0; //未发供应商
	const SUPPLIER_STATUS_WAIT_CONFIRM  = 10; //待供应商确定
	const SUPPLIER_STATUS_CONFIRMED  	= 20; //供应商已确定
	const SUPPLIER_STATUS_WAREHOUSED  	= 30; //已入库
	const SUPPLIER_STATUS_CANCEL  		= 40; //已撤销

	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_NO 	=> '未审批',
			self::STATUS_YES   	=> '已审批',
			self::STATUS_CANCEL => '已作废',
			self::STATUS_FINISH => '已完成',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getSupplierStatusDesc($value = true)
    {
        $desc = [
			self::SUPPLIER_STATUS_WAIT_SEND 	=> '未发供应商',
			self::SUPPLIER_STATUS_WAIT_CONFIRM	=> '待供应商确定',
			self::SUPPLIER_STATUS_CONFIRMED   	=> '供应商已确定',
			self::SUPPLIER_STATUS_WAREHOUSED   	=> '已入库',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }	
	
	
}