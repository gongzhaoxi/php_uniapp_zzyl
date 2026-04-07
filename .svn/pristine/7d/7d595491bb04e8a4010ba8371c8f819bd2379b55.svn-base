<?php
namespace app\common\enum;

class ErpMaterialEnterEnum
{

    const TYPE_ENTER_PURCHASE 			= 'enter_purchase'; //采购
    const TYPE_ENTER_PURCHASE_RETURN  	= 'enter_purchase_return'; //采购退换
	const TYPE_ENTER_PROCESS 			= 'enter_process'; //外加工
	const TYPE_ENTER_PROCESS_RETURN 	= 'enter_process_return'; //外加工退换
	const TYPE_ENTER_PRODUCE_RETURN  	= 'enter_produce_return'; //生产退料
	const TYPE_ENTER_DISCARD  			= 'enter_discard'; //报废
	
	const STATUS_HANDLE  				= 1; //处理中
	const STATUS_CANCEL  				= 2; //已作废
	const STATUS_FINISH  				= 3; //已结算

    public static function getTypeDesc($value = true)
    {
        $desc = [
			self::TYPE_ENTER_PURCHASE    		=> '采购',
            self::TYPE_ENTER_PURCHASE_RETURN 	=> '采购退换',
			self::TYPE_ENTER_PROCESS     		=> '外加工',
			self::TYPE_ENTER_PROCESS_RETURN  	=> '外加工退换',
			self::TYPE_ENTER_PRODUCE_RETURN  	=> '生产退料',
			self::TYPE_ENTER_DISCARD     		=> '报废',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	
	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_HANDLE 	=> '处理中',
            self::STATUS_CANCEL   	=> '已作废',
			self::STATUS_FINISH   	=> '已结算',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
}