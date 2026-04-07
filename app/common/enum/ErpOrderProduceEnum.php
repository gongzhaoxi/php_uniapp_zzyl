<?php
namespace app\common\enum;

class ErpOrderProduceEnum
{
	//发货状态
	const CHECK_STATUS_NO    						= 10;  //待排查
	const CHECK_STATUS_SUCCESS    					= 20;  //足料
	const CHECK_STATUS_FAILED    					= 30;  //缺料
	
	//生产状态
	const PRODUCE_STATUS_NO    						= 10;  //待排产
	const PRODUCE_STATUS_PROGRESS    				= 20;  //生产中
	const PRODUCE_STATUS_PURCHASE    				= 21;  //委外采购
	const PRODUCE_STATUS_WAIT    					= 22;  //生产完成(未审入库)
	const PRODUCE_STATUS_FINISH    					= 30;  //生产完成(已审入库)
	
	//加工方式
	const PRODUCE_TYPE_1 							= 1;//厂内生产
	const PRODUCE_TYPE_2 							= 2;//发外采购
	const PRODUCE_TYPE_3 							= 3;//库存改配	
	
	const APPROVE_STATUS_NO 						= 0 ;//未审批
	const APPROVE_STATUS_YES 						= 1 ;//已审批

	
    public static function getCheckStatusDesc($value = true)
    {
        $desc = [
            self::CHECK_STATUS_NO  			=> '待排查',
			self::CHECK_STATUS_SUCCESS     	=> '足料',			
			self::CHECK_STATUS_FAILED   	=> '缺料',	

        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getProduceStatusDesc($value = true)
    {
        $desc = [
			self::PRODUCE_STATUS_NO  		=> '待排产',
			self::PRODUCE_STATUS_PROGRESS	=> '生产中',
			self::PRODUCE_STATUS_PURCHASE	=> '委外采购',
			self::PRODUCE_STATUS_WAIT		=> '生产完成',
			self::PRODUCE_STATUS_FINISH		=> '生产完成',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getProduceTypeDesc($value = true)
    {
        $desc = [
			self::PRODUCE_TYPE_1  	=> '厂内生产',
			self::PRODUCE_TYPE_2	=> '发外采购',
			self::PRODUCE_TYPE_3	=> '库存改配',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getApproveStatusDesc($value = true)
    {
        $desc = [
			self::APPROVE_STATUS_NO  	=> '未审批',
			self::APPROVE_STATUS_YES	=> '已审批',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
}