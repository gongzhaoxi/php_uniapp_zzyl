<?php
namespace app\common\enum;

class RegionTypeEnum
{

    const DOMESTIC = 1; //国内
    const FOREIGN  = 2; //国外

    public static function getDesc($value = true)
    {
        $desc = [
            self::DOMESTIC    => '国内',
            self::FOREIGN     => '国外'
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
}