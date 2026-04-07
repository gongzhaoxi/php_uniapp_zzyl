<?php
namespace app\common\enum;

class ErpAftersaleEnum
{

    //订单状态
    const ORDER_STATUS_WAIT_HANDLE     		= 10;  //待处理
	const ORDER_STATUS_WAIT_PRODUCE    		= 20;  //待生产
    const ORDER_STATUS_PRODUCING         	= 30;  //生产中
	const ORDER_STATUS_WAIT_SHIPPING      	= 40;  //待发货
	const ORDER_STATUS_FINISH          		= 50;  //已完成
	const ORDER_STATUS_CLOSE          		= 60;  //已关闭

	//发货状态
	const SHIPPING_STATUS_NO    			= 10;  //未发货
	const SHIPPING_STATUS_PART    			= 20;  //部分发货
	const SHIPPING_STATUS_FINISH    		= 30;  //全部发货
	
	//生产状态
	const PRODUCE_STATUS_NO    				= 10;  //未生产
	const PRODUCE_STATUS_PART    			= 20;  //部分生产
	const PRODUCE_STATUS_All    			= 30;  //全部生产中
	const PRODUCE_STATUS_FINISH    			= 40;  //生产完成
	
	//发货类型
	const SHIPPING_TYPE_1    				= 1;  //款到发货
	const SHIPPING_TYPE_2    				= 2;  //尽快发货
	const SHIPPING_TYPE_3    				= 3;  //等通知送货
	const SHIPPING_TYPE_4    				= 4;  //尽快送货
	const SHIPPING_TYPE_5    				= 5;  //等通知发货

	//售后类型
	const ORDER_TYPE_1    					= 1;  //销售
	const ORDER_TYPE_2    					= 2;  //维护
	const ORDER_TYPE_3    					= 3;  //赠送

    public static function getOrderStatusDesc($value = true)
    {
        $desc = [
            self::ORDER_STATUS_WAIT_HANDLE  		=> '待处理',
			//self::ORDER_STATUS_WAIT_PRODUCE     	=> '待生产',			
			//self::ORDER_STATUS_PRODUCING     		=> '生产中',	
			self::ORDER_STATUS_WAIT_SHIPPING     	=> '待发货',
			self::ORDER_STATUS_FINISH     			=> '已完成',
			self::ORDER_STATUS_CLOSE     			=> '已关闭',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getShippingStatusDesc($value = true)
    {
        $desc = [
            self::SHIPPING_STATUS_NO  		=> '未发货',
            self::SHIPPING_STATUS_PART		=> '部分发货',
			self::SHIPPING_STATUS_FINISH	=> '全部发货',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	public static function getProduceStatusDesc($value = true)
    {
        $desc = [
            self::PRODUCE_STATUS_NO  		=> '未生产',
            self::PRODUCE_STATUS_PART		=> '部分生产中',
			self::PRODUCE_STATUS_All		=> '全部生产中',
			self::PRODUCE_STATUS_FINISH		=> '生产完成',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }	
	
	
	public static function getShippingTypeDesc($value = true)
    {
        $desc = [
            self::SHIPPING_TYPE_1  		=> '款到发货',
            self::SHIPPING_TYPE_2		=> '尽快发货',
			self::SHIPPING_TYPE_5  		=> '等通知发货',
			self::SHIPPING_TYPE_3		=> '等通知送货',
			self::SHIPPING_TYPE_4		=> '尽快送货',
			
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }	
	
	public static function getOrderTypeDesc($value = true)
    {
        $desc = [
            self::ORDER_TYPE_1  	=> '销售',
            self::ORDER_TYPE_2		=> '维护',
			self::ORDER_TYPE_3		=> '赠送',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }
	
	
}