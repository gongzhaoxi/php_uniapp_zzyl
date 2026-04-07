<?php
namespace app\common\enum;

class ErpPurchaseOrderLogEnum
{
	const ORDER_FILED_CHANGE 				= 'order_filed_change'; //订单字段变动
    const ORDER_DATA_FILED_CHANGE 			= 'order_data_filed_change'; //订单产品字段变动
	const ORDER_DATA_ADD  					= 'order_data_add'; //添加订单产品
	const ORDER_DATA_DELETE  				= 'order_data_delete'; //删除订单产品
	const ORDER_CHECK  						= 'order_check'; //审批订单
	const ORDER_RECHECK  					= 'order_recheck'; //反审订单
	const ORDER_SEND  						= 'order_send'; //发供应商
	const ORDER_CANCEL  					= 'order_cancel'; //供应商撤销
	const ORDER_CONFIRM  					= 'order_confirm'; //供应商确认
	const ORDER_WAREHOUSED  				= 'order_warehoused'; //采购入库
	const ORDER_REMOVE  					= 'order_remove'; //作废订单
	const ORDER_DATA_REMOVE  				= 'order_data_remove'; //作废订单产品

    public static function getDataTypeDesc($value = true)
    {
        $desc = [
			self::ORDER_FILED_CHANGE    			=> '订单字段变动',
            self::ORDER_DATA_FILED_CHANGE    		=> '订单产品字段变动',
			self::ORDER_DATA_ADD  					=> '添加订单产品',
			self::ORDER_DATA_DELETE  				=> '删除订单产品',
			self::ORDER_CHECK    					=> '审批订单',
			self::ORDER_RECHECK    					=> '反审订单',
			self::ORDER_SEND  						=> '发供应商',
            self::ORDER_CANCEL  					=> '供应商撤销',
			self::ORDER_CONFIRM  					=> '供应商确认',
			self::ORDER_WAREHOUSED  				=> '采购入库',
			self::ORDER_REMOVE  					=> '作废订单',
			self::ORDER_DATA_REMOVE  				=> '作废订单产品',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
}