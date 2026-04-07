<?php
namespace app\common\enum;

class ErpWarehouseEnum
{

    const PARTN 		= 1; //零件
    const COMPONENT  	= 2; //部件
	const PRODUCE  		= 3; //生产工位
	const PRODUCT  		= 4; //成品

    public static function getTypeDesc($value = true)
    {
        $desc = [
            self::PARTN    		=> '零件',
            self::COMPONENT   	=> '部件',
			self::PRODUCE    	=> '生产工位',
			self::PRODUCT    	=> '成品',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
}