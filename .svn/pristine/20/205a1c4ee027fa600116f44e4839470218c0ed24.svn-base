<?php
namespace app\common\enum;

class ErpNoticeEnum
{
    //状态
    const STATUS_AUDITING     	= 0;  //待审批
    const STATUS_AUDITED    	= 1;  //已审批
    const STATUS_REJECT      	= 2;  //驳回


    public static function getStatusDesc($value = true)
    {
        $desc = [
            self::STATUS_AUDITING  	=> '待审批',
			self::STATUS_AUDITED 	=> '已审批',			
			self::STATUS_REJECT   	=> '驳回',	
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	

	
	
}