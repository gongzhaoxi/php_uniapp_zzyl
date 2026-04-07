<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Session;
use think\facade\Db;
use app\common\model\{ErpMaterialTree,ErpMaterial};
use app\admin\validate\ErpMaterialTreeValidate;

class ErpMaterialTreeLogic extends BaseLogic{

	// 获取列表
    public static function getList($type)
    {
        $list = ErpMaterialTree::where('type',$type)->order(['sort'=>'asc','id'=>'asc'])->select();
		return ['code'=>0,'data'=>$list->toArray(),'extend'=>['count' => $list->count()]];
    }
	
	public static function tree($type,$top=true){
		$map 		= [];
		if($type){
			$map[] 	= ['type','=',$type];
		}
		$data 		= ErpMaterialTree::where($map)->order(['sort'=>'asc','id'=>'asc'])->select()->toArray();
		$pid 		= 0;
		if($top){
			$pid	= -1;
			$data[] = ['id'=>0,'pid'=>-1,'title'=>'顶级'];
		}
		return get_tree($data,$pid);
	}
	
    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpMaterialTreeValidate;
        if(!$validate->scene('add')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            ErpMaterialTree::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
 
    public static function goFind($id)
    {
       return ErpMaterialTree::find($id);
    }	
	
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate = new ErpMaterialTreeValidate;
        if(!$validate->scene('edit')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
		if(in_array($data['pid'], ErpMaterialTree::where('path','find in set',$data['id'])->column('id'))) {
			return ['msg'=>'父级不能为自己以及子分类','code'=>201];
		}		
        try {
            ErpMaterialTree::update($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 状态
    public static function goStatus($data,$id)
    {
        $model =  self::goFind($id);
        if ($model->isEmpty())  return ['msg'=>'数据不存在','code'=>201];
        try{
            $model->save([
                'status' => $data,
            ]);
 
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($id,$type)
    {
        if($type){
			ErpMaterialTree::where('path','find in set',$id)->delete();
        }else{
            if(ErpMaterialTree::where('pid',$id)->count()){
				return ['msg'=>'存在子目录，确认删除后不可恢复','code'=>201];
            }
			if(ErpMaterial::where('tree_id','in',ErpMaterialTree::where('path','find in set',$id)->column('id'))->count()){
				return ['msg'=>'分类下有物料，确认删除后不可恢复','code'=>201];
            }
			ErpMaterialTree::where('id',$id)->delete();
        }
    }

}
