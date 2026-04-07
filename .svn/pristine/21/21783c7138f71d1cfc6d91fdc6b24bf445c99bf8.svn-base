<?php
namespace app\common\enum;

class ErpOrderLogEnum
{
	const ORDER_FILED_CHANGE 				= 'order_filed_change'; //订单字段变动
    const ORDER_PRODUCT_FILED_CHANGE 		= 'order_product_filed_change'; //订单产品字段变动
    const ORDER_PRODUCT_BOM_ADD  			= 'order_product_bom_add'; //订单产品加配
	const ORDER_PRODUCT_BOM_ADD_DELETE  	= 'order_product_bom_add_delete'; //订单产品删除加配
	const ORDER_PRODUCT_BOM_ADD_CHANGE  	= 'order_product_bom_add_change'; //订单产品变动
	const ORDER_PRODUCT_BOM_CHANGE  		= 'order_product_bom_change'; //订单产品配置变动
	
	const ORDER_PRODUCT_BOM_REPLACE  		= 'order_product_bom_replace'; //订单产品换配
	const ORDER_PRODUCT_BOM_REPLACE_DELETE  = 'order_product_bom_replace_delete'; //订单产品删除换配
	const ORDER_PRODUCT_ADD  				= 'order_product_add'; //添加订单产品
	const ORDER_PRODUCT_DELETE  			= 'order_product_delete'; //删除订单产品
	const ORDER_CANCEL  					= 'order_cancel'; //取消订单
	const ORDER_DELETE  					= 'order_delete'; //删除订单
	const ORDER_RESTORE  					= 'order_restore'; //从回收站恢复订单
	const TECHNICIAN_PASS  					= 'technician_pass'; //技术审核订单
	const TECHNICIAN_RESET  				= 'technician_reset'; //技术反审订单
	const SALESMAN_PASS  					= 'salesman_pass'; //销售审核订单
	const SALESMAN_RESET  					= 'salesman_reset'; //销售反审订单
	const ORDER_MATERIAL_ADD  				= 'order_material_add'; //添加售后物料	
	const ORDER_MATERIAL_FILED_CHANGE  		= 'order_material_filed_change'; //售后物料字段变动	
	const ORDER_MATERIAL_DELETE  			= 'order_material_delete'; //删除售后物料
	const ORDER_PRODUCT_ADD_FROM_RETURNED  	= 'order_product_add_from_returned'; //添加退货产品
	
	const ORDER_ACCESSORY_ADD  				= 'order_accessory_add'; //添加订单配件
	const ORDER_ACCESSORY_DELETE  			= 'order_accessory_delete'; //删除订单配件
	const ORDER_ACCESSORY_FILED_CHANGE 		= 'order_accessory_filed_change'; //订单配件字段变动

    public static function getDataTypeDesc($value = true)
    {
        $desc = [
			self::ORDER_FILED_CHANGE    			=> '订单字段变动',
            self::ORDER_PRODUCT_FILED_CHANGE    	=> '订单产品字段变动',
            self::ORDER_PRODUCT_BOM_ADD    			=> '订单产品加配',
			self::ORDER_PRODUCT_BOM_ADD_DELETE    	=> '订单产品删除加配',
			self::ORDER_PRODUCT_BOM_REPLACE  		=> '订单产品换配',
			self::ORDER_PRODUCT_BOM_REPLACE_DELETE  => '订单产品删除换配',
			self::ORDER_PRODUCT_ADD  				=> '添加订单产品',
			self::ORDER_PRODUCT_DELETE  			=> '删除订单产品',
			self::ORDER_CANCEL  					=> '取消订单',
			self::ORDER_DELETE  					=> '删除订单',
			self::ORDER_RESTORE  					=> '回收站恢复订单',
			self::TECHNICIAN_PASS  					=> '技术审核订单',
			self::TECHNICIAN_RESET  				=> '技术反审订单',
			self::SALESMAN_PASS  					=> '销售审核订单',
			self::SALESMAN_RESET  					=> '销售反审订单',
			self::ORDER_MATERIAL_ADD  				=> '添加售后物料',
			self::ORDER_MATERIAL_FILED_CHANGE  		=> '售后物料字段变动',
			self::ORDER_MATERIAL_DELETE  			=> '删除售后物料',
			self::ORDER_PRODUCT_ADD_FROM_RETURNED  	=> '添加退货产品',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
}