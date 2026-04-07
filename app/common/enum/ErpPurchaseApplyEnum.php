<?php
namespace app\common\enum;

class ErpPurchaseApplyEnum
{

	const STATUS_NO  				= 0; //未生成
	const STATUS_YES  				= 1; //已生成
	
	const DATA_TYPE_INPUT			= 10;//手工添加
	const DATA_TYPE_WAREHOUSE		= 11;//仓库申购
	const DATA_TYPE_PLAN			= 12;//计划申购
	const DATA_TYPE_PRODUCE			= 20;//成本外发加工
	
	const TYPE_MATERIAL  			= 1; //物料
	const TYPE_PRODUCT  			= 2; //成品
	const TYPE_OUTSOURCING  		= 3; //委外

	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_NO 	=> '未生成',
			self::STATUS_YES   	=> '已生成',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getDataTypeDesc($value = true)
    {
        $desc = [
			self::DATA_TYPE_INPUT 		=> '手工添加',
			self::DATA_TYPE_WAREHOUSE	=> '仓库申购',
			self::DATA_TYPE_PLAN		=> '计划申购',
			self::DATA_TYPE_PRODUCE		=> '成本外发加工',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
}