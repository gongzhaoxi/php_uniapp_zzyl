<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ArticleCategory;
use app\admin\validate\ArticleCategoryValidate;

class ArticleCategoryLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $list = ArticleCategory::withSearch(['query'],['query'=>$query])->withCount('article')->order('id','desc')->append(['status_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ArticleCategoryValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
            ArticleCategory::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function goFind($id)
    {
       return ArticleCategory::find($id);
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ArticleCategoryValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::goFind($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ArticleCategoryValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ArticleCategory::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
        $list = ArticleCategory::onlyTrashed()->withSearch(['query'],['query'=>$query])->append(['status_desc'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 	= new ArticleCategoryValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= ArticleCategory::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				ArticleCategory::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }

}
