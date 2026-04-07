<?php
namespace app\common\enum;

class ErpOrderEnum
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

	//订单类型
	const ORDER_TYPE_10    					= 10;  //正常订单
	const ORDER_TYPE_11    					= 11;  //加急订单
	const ORDER_TYPE_12    					= 12;  //订单改单
	const ORDER_TYPE_20    					= 20;  //售后销售
	const ORDER_TYPE_21    					= 21;  //售后维护
	const ORDER_TYPE_22    					= 22;  //售后赠送

	//数据类型
	const DATA_TYPE_1    					= 1;  //销售订单
	const DATA_TYPE_2    					= 2;  //售后订单

    public static function getOrderStatusDesc($value = true)
    {
        $desc = [
            self::ORDER_STATUS_WAIT_HANDLE  		=> '待处理',
			self::ORDER_STATUS_WAIT_PRODUCE     	=> '待生产',			
			self::ORDER_STATUS_PRODUCING     		=> '生产中',	
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
	
	public static function getOrderTypeDesc($value = true,$data_type=0)
    {
        $desc = [
			self::DATA_TYPE_1 	=>[self::ORDER_TYPE_10  => '正常订单',self::ORDER_TYPE_11 => '加急订单',self::ORDER_TYPE_12 => '订单改单',],
			self::DATA_TYPE_2	=>[self::ORDER_TYPE_20  => '售后销售',self::ORDER_TYPE_21 => '售后维护',self::ORDER_TYPE_22 => '售后赠送',], 
        ];
		$all 				= [];
		foreach($desc as $k1=>$v1){
			foreach($v1 as $k2=>$v2){
				$all[$k2] 	= $v2;
			}
		}
        if(true === $value){
			if($data_type == 0){
				return $all;
			}else{
				return $desc[$data_type] ?? [];
			} 
        }
        return $all[$value] ?? '';
    }	
	
	public static function getDataTypeDesc($value = true)
    {
        $desc = [
            self::DATA_TYPE_1  		=> '销售订单',
			self::DATA_TYPE_2		=> '售后订单',
        ];
        if(true === $value){
            return $desc;
        }
        return $desc[$value] ?? '';
    }	
}