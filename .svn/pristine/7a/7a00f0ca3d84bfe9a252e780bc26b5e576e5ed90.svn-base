<?php
namespace app\common\enum;

class ErpMaterialPayoutEnum
{
    //状态
    const STATUS_YES     		= 1;  //已审批
    const STATUS_NO   			= 0;  //未审核

	//来源类型
	const TYPE_1    			= 1;  //采购
	const TYPE_2    			= 2;  //委外
	const TYPE_3    			= 3;  //退货

    public static function getStatusDesc($value = true)
    {
        $desc = [
            self::STATUS_YES  	=> '已审批',
			self::STATUS_NO     => '未审核',			
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getDataTypeDesc($value = true)
    {
        $desc = [
            self::TYPE_1  		=> '采购',
			self::TYPE_2		=> '委外',
			self::TYPE_3		=> '退货',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }

	
	
	
}