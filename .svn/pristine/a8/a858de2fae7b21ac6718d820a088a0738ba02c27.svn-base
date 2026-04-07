<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\Article;
use app\admin\validate\ArticleValidate;

class ArticleLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $list = Article::withSearch(['query'],['query'=>$query])->order('id','desc')->append(['status_desc','category_name','click'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ArticleValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
            Article::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function goFind($id)
    {
       return Article::find($id);
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ArticleValidate;
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
        $validate 	= new ArticleValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			Article::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
        $list = Article::onlyTrashed()->withSearch(['query'],['query'=>$query])->append(['status_desc','category_name','click'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 		= new ArticleValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= Article::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				Article::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }

}
