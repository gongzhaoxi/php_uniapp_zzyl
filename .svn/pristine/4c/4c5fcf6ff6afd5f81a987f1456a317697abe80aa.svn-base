<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpNotice;
use app\admin\validate\ErpNoticeValidate;
use app\common\enum\ErpNoticeEnum;

class ErpNoticeLogic extends BaseLogic{

	//获取未读/未审通知
    public static function getNoRead($page)
    {
		$field 		= '*';
		$sql 		= ' (auditing_admin_id = '.self::$adminUser['id'].' and status = '.ErpNoticeEnum::STATUS_AUDITING.' ) or (status = '.ErpNoticeEnum::STATUS_AUDITED.' and FIND_IN_SET("'.self::$adminUser['id'].'", notice_admin_id) and ( viewed_admin_id is null or viewed_admin_id not like "%,'.self::$adminUser['id'].',%")) ';
        $count 		= ErpNotice::whereRaw($sql)->count();
		if($count){
			$list 	= ErpNotice::field($field)->whereRaw($sql)->order('id','desc')->limit(($page-1),1)->select();
			$model	= $list[0];
		}else{
			$model	= null;
		}
		return ['model'=>$model,'count'=>$count];
    }

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 	= '*';
        $list 	= ErpNotice::withSearch(['query'],['query'=>$query])->with(['admin'=>function($query){return $query->field('id,username,nickname');},'auditing_admin'=>function($query){return $query->field('id,username,nickname');}])->field($field)->whereRaw('admin_id = '.self::$adminUser['id'].' or auditing_admin_id = '.self::$adminUser['id'].' or (status = '.ErpNoticeEnum::STATUS_AUDITED.' and FIND_IN_SET("'.self::$adminUser['id'].'", notice_admin_id))')->order('id','desc')->append(['status_desc','notice_admin','viewed_admin'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpNoticeValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$data['admin_id'] = self::$adminUser['id'];
            ErpNotice::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpNotice::where($map)->find();
		}else{
			return ErpNotice::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpNoticeValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		if($model->status == ErpNoticeEnum::STATUS_AUDITED) {
			return ['msg'=>'已审核','code'=>201];
		}
		if($model->admin_id != self::$adminUser['id']){
			return ['msg'=>'不能修改数据','code'=>201];
		}
        try {
			$data['status'] = 0;
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpNoticeValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpNotice::destroy(function($query) use($data){
				$query->where('id','in',$data['ids'])->where('status','<>',ErpNoticeEnum::STATUS_AUDITED);
			});
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
    // 审批
    public static function goAuditing($id,$status)
    {
		$model 		= self::getOne($id);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		if($model->status != ErpNoticeEnum::STATUS_AUDITING) {
			return ['msg'=>'状态错误','code'=>201];
		}
        try{
			$viewed_admin_id 		= [];
			if(in_array(self::$adminUser['id'],$model['notice_admin_id'])){
				$viewed_admin_id[]	= self::$adminUser['id'];
			}
			$model->save(['auditing_time'=>time(),'status'=>$status,'viewed_admin_id'=>$viewed_admin_id]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 已阅
    public static function goRead($id)
    {
		$model 		= self::getOne($id);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		if($model->status != ErpNoticeEnum::STATUS_AUDITED) {
			return ['msg'=>'未审批','code'=>201];
		}
        try{
			$viewed_admin_id 		= $model['viewed_admin_id'];
			if(!in_array(self::$adminUser['id'],$viewed_admin_id)){
				$viewed_admin_id[]	= self::$adminUser['id'];
			}
			$model->save(['viewed_admin_id'=>$viewed_admin_id]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

}
