<?php
declare (strict_types = 1);
namespace app\supplier\logic;
use app\supplier\logic\BaseLogic;
use app\common\model\{ErpMaterialDiscard,ErpMaterialStockFeedback,ErpMaterialDiscardMaterial};
use app\common\enum\ErpMaterialStockEnum;
use think\facade\Db;
use app\supplier\validate\ErpMaterialStockFeedbackValidate;

class ErpMaterialDiscardLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,supplier_status,system_id,order_sn,type,supplier_id,stock_date';
        $list = ErpMaterialDiscard::with(['supplier'=>function($query){return $query->field('id,name');}])
		->withSearch(['query'],['query'=>$query])->field($field)
		->where('data_type',ErpMaterialStockEnum::DATA_TYPE_DISCARD)
		->where('supplier_id','=',self::$supplier['id'])
		->order('id','desc')->append(['can_confirm','system_name'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	// 获取物料
    public static function getMaterial($query=[],$limit=10)
    {
		$field 		= 'a.id,a.material_stock_id,a.material_id,a.stock_num,a.stocked_num,a.status,a.photo';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$list 		= ErpMaterialDiscardMaterial::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])->with(['material'=>function($query){return $query->field('id,status,type,name,sn,cid,stock,unit,material,surface,color,remark,photo,status');}])->field($field)->order('a.id','desc')->append(['photo_link'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function getFeedback($id){
		return ErpMaterialStockFeedback::where('stock_id',$id)->order('id desc')->select();
	}

    // 反馈
    public static function goFeedback($data)
    {
		$validate 	= new ErpMaterialStockFeedbackValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model = ErpMaterialDiscard::where('id',$data['stock_id'])->where('supplier_id','=',self::$supplier['id'])->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
        try{
			$data['operator'] 	= self::$supplier['name'];
			$data['type']		= 2;
			ErpMaterialStockFeedback::create($data);
		}catch (\Exception $e){
			
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	public static function goConfirm($id){
		$model = ErpMaterialDiscard::where('id',$id)->where('supplier_id','=',self::$supplier['id'])->find();
        if(empty($model['id'])) {
			return ['msg'=>'出库单不存在','code'=>201];
		}
		if($model['can_confirm'] == false) {
			return ['msg'=>'状态错误','code'=>201];
		}
		try {
			$model->save(['supplier_status'=>ErpMaterialStockEnum::SUPPLIER_STATUS_CONFIRM]);		
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

}
