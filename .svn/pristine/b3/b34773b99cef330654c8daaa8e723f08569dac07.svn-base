<?php
namespace app\common\enum;

class ErpMaterialEnum
{

    const PARTN = 1; //零件
    const COMPONENT  = 2; //部件
	
	const PROCESSING_WAY_1 = 1;
	const PROCESSING_WAY_2 = 2;	

    public static function getTypeDesc($value = true)
    {
        $desc = [
            self::PARTN    => '零件',
            self::COMPONENT     => '部件'
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getProcessingWayDesc($value = true)
    {
        $desc = [
            self::PROCESSING_WAY_1 	=> '采购申请',
            self::PROCESSING_WAY_2	=> '委外申请'
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }	
	
}