<?php
namespace app\common\enum;

class ErpMaterialPlanEnum
{
    //状态
    const STATUS_NO_ASSIGN     		= 10;  //未下达
    const STATUS_ASSIGNED    		= 20;  //已下达
    const STATUS_WAREHOUSED      	= 30;  //已入库

	//来源类型
	const TYPE_PLAN    				= 1;  //添加计划
	const TYPE_ANALYSE    			= 2;  //月度计划
	const TYPE_PRODUCT    			= 3;  //计划下达
	

    public static function getStatusDesc($value = true)
    {
        $desc = [
            self::STATUS_NO_ASSIGN  	=> '未下达',
			self::STATUS_ASSIGNED     	=> '已下达',			
			self::STATUS_WAREHOUSED     => '已入库',	
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getTypeDesc($value = true)
    {
        $desc = [
            self::TYPE_PLAN  			=> '添加计划',
			self::TYPE_ANALYSE			=> '月度计划',
			self::TYPE_PRODUCT			=> '计划下达',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }

	
	
	
}