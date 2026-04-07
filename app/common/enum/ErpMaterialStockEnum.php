<?php
namespace app\common\enum;

class ErpMaterialStockEnum
{

	
    const TYPE_OUT_PRODUCE_GET 			= 'out_produce_get'; //计划领料			//不可删除
    const TYPE_OUT_PRODUCE_ORDER        = 'out_produce_order';//订单领料
    const TYPE_OUT_PRODUCE_REGET  		= 'out_produce_reget'; //售后领料
	const TYPE_OUT_PROCESS 				= 'out_process'; //后勤领料
	const TYPE_OUT_AFTER_SALES 			= 'out_after_sales'; //委外领料	
	const TYPE_OUT_CHECK  				= 'out_check'; //盘点出库				//不可删除
	const TYPE_OUT_CORRECT  			= 'out_correct'; //系统修正出库				//不可删除
	const TYPE_OUT_ALLOCATE  			= 'out_allocate'; //调拨出库				//不可删除
	const TYPE_OUT_PULL  				= 'out_pull'; //零件领用				//不可删除
	const TYPE_OUT_BACK_WAREHOUSE  		= 'out_back_warehouse'; //退仓出库				//不可删除
	const TYPE_OUT_OUTSOURCING  		= 'out_outsourcing'; //委外出库				//不可删除
	
	const TYPE_ENTER_PURCHASE 			= 'enter_purchase'; //采购
    const TYPE_ENTER_PURCHASE_RETURN  	= 'enter_purchase_return'; //采购退换
	const TYPE_ENTER_PROCESS 			= 'enter_process'; //外加工入库
	const TYPE_ENTER_PROCESS_RETURN 	= 'enter_process_return'; //外加工退换
	const TYPE_ENTER_PRODUCE_RETURN  	= 'enter_produce_return'; //生产退料
				
	const TYPE_ENTER_TECHNOLOGY  		= 'enter_technology'; //技术退料
	const TYPE_ENTER_SALE  				= 'enter_sale'; //销售退料
	const TYPE_ENTER_CHECK  			= 'enter_check'; //盘点入库				//不可删除
	const TYPE_ENTER_PLAN  				= 'enter_plan'; //计划入库				//不可删除
	const TYPE_ENTER_CORRECT  			= 'enter_correct'; //系统修正入库		//不可删除
	const TYPE_ENTER_ALLOCATE  			= 'enter_allocate'; //调拨入库		//不可删除
	const TYPE_ENTER_DISCARD  			= 'enter_discard'; //不良品入库	  //不可删除
	const TYPE_ENTER_BACK_WAREHOUSE  	= 'enter_back_warehouse'; //退仓入库				//不可删除
	const TYPE_ENTER_WORKSHOP  			= 'enter_workshop'; //车间退回仓库				//不可删除
	

	
	const TYPE_CHECK					= 'check'; //盘点
	
	const DISCARD_SCRAP					= 'discard_scrap'; //报废  
	const DISCARD_RETURN				= 'discard_return'; //退货
	const DISCARD_QUALITY				= 'discard_quality'; //质检退货 //不可删除
	
	const TYPE_ALLOCATE					= 'allocate'; //调拨
	const TYPE_BACK_WAREHOUSE			= 'back_warehouse'; //退仓
	
	const STATUS_HANDLE  				= 1; //待审核
	const STATUS_CANCEL  				= 2; //已作废
	const STATUS_SETTLEMENT  			= 3; //已结算
	const STATUS_FINISH  				= 4; //已完成
	
	const DATA_TYPE_OUT  				= 1; //出库
	const DATA_TYPE_ENTER  				= 2; //入库
	const DATA_TYPE_CHECK  				= 3; //盘点
	const DATA_TYPE_DISCARD  			= 4; //报废
	const DATA_TYPE_ALLOCATE  			= 5; //调拨
	
	const SUPPLIER_STATUS_NO 			= 0;
	const SUPPLIER_STATUS_YES 			= 1;
	const SUPPLIER_STATUS_CONFIRM 		= 2;

    public static function getTypeDesc($value = true)
    {
        $out 		= self::getOutType();
		$enter 		= self::getEnterType();
		$check 		= self::getCheckType();
		$discard 	= self::getDiscardType();
		$allocate 	= self::getAllocateType();
		$desc		= array_merge($out,$enter,$check,$discard,$allocate);
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getOutType()
    {
        $desc = [
			self::TYPE_OUT_PRODUCE_GET    	=> '计划领料',//不可删除
			self::TYPE_OUT_PRODUCE_ORDER    => '订单领料',
            self::TYPE_OUT_PRODUCE_REGET 	=> '售后领料',
			self::TYPE_OUT_PROCESS     		=> '后勤领料',
			self::TYPE_OUT_AFTER_SALES  	=> '委外领料',
			self::TYPE_OUT_CHECK  			=> '盘点出库',//不可删除
			self::TYPE_OUT_CORRECT  		=> '系统修正出库',//不可删除
			self::TYPE_OUT_ALLOCATE  		=> '调拨出库',//不可删除
			self::TYPE_OUT_PULL  			=> '车间库存领用',//不可删除
			self::TYPE_OUT_BACK_WAREHOUSE  	=> '退仓出库',//不可删除
			self::TYPE_OUT_OUTSOURCING  	=> '委外出库',//不可删除
			
        ];
		return $desc;
    }
	
	public static function getEnterType()
    {
        $desc = [
			self::TYPE_ENTER_PURCHASE    		=> '采购',
            self::TYPE_ENTER_PURCHASE_RETURN 	=> '采购退换',
			self::TYPE_ENTER_PROCESS     		=> '外加工入库',
			self::TYPE_ENTER_PROCESS_RETURN  	=> '外加工退换',
			self::TYPE_ENTER_PRODUCE_RETURN  	=> '生产退料',
			self::TYPE_ENTER_DISCARD     		=> '不良品入库',
			self::TYPE_ENTER_TECHNOLOGY     	=> '技术退料',
			self::TYPE_ENTER_SALE     			=> '销售退料',
			self::TYPE_ENTER_CHECK     			=> '盘点入库',//不可删除
			self::TYPE_ENTER_PLAN     			=> '计划入库',//不可删除
			self::TYPE_ENTER_CORRECT     		=> '系统修正入库',//不可删除
			self::TYPE_ENTER_ALLOCATE     		=> '调拨入库',//不可删除
			self::TYPE_ENTER_BACK_WAREHOUSE  	=> '退回车间库存',//不可删除
        ];
		return $desc;
    }	
	
	public static function getCheckType()
    {
        $desc = [
			self::TYPE_CHECK    		=> '盘点',
        ];
		return $desc;
    }	
	
	public static function getDiscardType()
    {
        $desc = [
			self::DISCARD_SCRAP    		=> '报废',//不可删除
			self::DISCARD_RETURN    	=> '退货',
			self::DISCARD_QUALITY    	=> '质检退货',
        ];
		return $desc;
    }	
	
	public static function getAllocateType()
    {
        $desc = [
			self::TYPE_ALLOCATE    		=> '调拨',//不可删除
			self::TYPE_BACK_WAREHOUSE   => '退仓',//不可删除
        ];
		return $desc;
    }	
	
	public static function getStatusDesc($value = true)
    {
        $desc = [
			self::STATUS_HANDLE 	=> '待审核',
            self::STATUS_CANCEL   	=> '此单作废',
			self::STATUS_SETTLEMENT	=> '已结算',
			self::STATUS_FINISH   	=> '已完成',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getDataTypeDesc($value = true)
    {
        $desc = [
			self::DATA_TYPE_OUT 	=> '出库',
            self::DATA_TYPE_ENTER 	=> '入库',
			self::DATA_TYPE_CHECK 	=> '盘点',
			self::DATA_TYPE_DISCARD => '报废',
			self::DATA_TYPE_ALLOCATE=> '调拨',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	
	public static function getIgnoreType()
    {
		return [self::TYPE_OUT_PRODUCE_GET,self::TYPE_OUT_CHECK,self::TYPE_ENTER_CHECK,self::TYPE_ENTER_PLAN,self::TYPE_ENTER_CORRECT,self::DISCARD_QUALITY,self::TYPE_OUT_CORRECT,self::TYPE_OUT_ALLOCATE,self::TYPE_ENTER_ALLOCATE,self::TYPE_ENTER_DISCARD,self::TYPE_OUT_PULL,self::TYPE_OUT_BACK_WAREHOUSE,self::TYPE_ENTER_BACK_WAREHOUSE,self::TYPE_OUT_OUTSOURCING];
    }
	
	
}