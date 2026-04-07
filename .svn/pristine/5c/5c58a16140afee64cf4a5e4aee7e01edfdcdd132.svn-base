<?php
namespace app\common\enum;

class ErpProductProjectEnum
{

    const REPLACE 	= 1; //改配
    const ADD  		= 2; //加配

    public static function getTypeDesc($value = true)
    {
        $desc = [
			self::REPLACE    => '改配',
            self::ADD     => '加配'
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
}