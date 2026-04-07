<?php
namespace app\common\enum;

class SystemEnum
{

    public static function getNameDesc($value = true)
    {
        $data = [
            1 => '置安',
            2 => '安业'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }

}