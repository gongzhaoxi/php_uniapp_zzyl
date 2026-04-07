<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\Region;
use app\admin\validate\RegionValidate;
use think\facade\Db;

class RegionLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 	= 'id,parent_id,name';
        $list 	= Region::field($field)->where('level','<=',2)->order('id','asc')->select();
        return ['code'=>0,'data'=>$list->toArray(),'extend'=>['count' => $list->count(), 'limit' => $limit]];
    }

	public static function tree($top=true,$level=''){
		$map 		= [];
		$map[]		= ['status','=',1];
		if($level){
			$map[]	= ['level','in',$level];
		}
		$data 		= Region::field('id,parent_id,name')->where($map)->order(['id'=>'asc'])->select()->toArray();
		$pid 		= 0;
		if($top){
			$pid	= -1;
			$data[] = ['id'=>0,'parent_id'=>-1,'name'=>'顶级'];
		}
		return get_tree($data,$pid,'id','parent_id');
	}

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new RegionValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		if($data['parent_id']){
			$parent 		= Region::where('id',$data['parent_id'])->find();
			if(empty($parent['id'])){
				return ['msg'=>'父级不存在','code'=>201];
			}
			$data['level'] 	= $parent['level'] + 1;
		}else{
			$data['level'] 	= 0;
		}
        try {
			
            Region::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return Region::where($map)->find();
		}else{
			return Region::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new RegionValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
			if($data['parent_id'] != $model['parent_id']){
				if($data['parent_id']){
					$parent = Region::where('id',$data['parent_id'])->find();
					if(empty($parent['id'])){
						return ['msg'=>'父级不存在','code'=>201];
					}
					$data['level'] 	= $parent['level'] + 1;
				}else{
					$data['level'] 	= 0;
				}
				if($data['level'] != $model['level']){
					$diff 		= $data['level'] - $model['level'];
					$regions 	= getSubs(Region::field('id,parent_id,name')->where('status',1)->order(['id'=>'asc'])->select()->toArray(),$model['id'],'id','parent_id');
					if($regions){
						Region::where('id','in',array_column($regions,'id'))->update(['level'=>Db::raw('level'.($diff>0?'+':'').$diff)]);
					}
				}
			}
			
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($id)
    {
		$model 		= self::getOne($id);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try{
			$model->delete();
			$regions 	= getSubs(Region::field('id,parent_id,name')->where('status',1)->order(['id'=>'asc'])->select()->toArray(),$model['id'],'id','parent_id');
			if($regions){
				Region::where('id','in',array_column($regions,'id'))->delete();
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

}
