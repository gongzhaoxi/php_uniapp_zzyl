<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Session;
use think\facade\Db;
use app\common\model\AdminPermission;
use app\admin\validate\AdminPermissionValidate;

class AdminPermissionLogic extends BaseLogic{

	// 获取列表
    public static function getList()
    {
        $list = AdminPermission::order(['sort'=>'asc','id'=>'asc'])->select();
		return ['code'=>0,'data'=>$list->toArray(),'extend'=>['count' => $list->count()]];
    }
	
	public static function permissions(){
		return get_tree(AdminPermission::order(['sort'=>'asc','id'=>'asc'])->select()->toArray());
	}
	
    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new AdminPermissionValidate;
        if(!$validate->scene('add')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            AdminPermission::create($data);
            rm();
			Session::clear();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
 
    public static function goFind($id)
    {
       return AdminPermission::find($id);
    }	
	
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate = new AdminPermissionValidate;
        if(!$validate->scene('edit')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            AdminPermission::update($data);
            rm();
			Session::clear();
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
            rm();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($id,$type)
    {
        $model = AdminPermission::with('child')->find($id);
        if($type){
            $arr = Db::name('admin_permission')->where('pid',$id)->field('id,pid')->select();
            foreach($arr as $k=>$v){
                Db::name('admin_permission')->where('pid',$v['id'])->delete();
                Db::name('admin_role_permission')->where('permission_id',$v['id'])->delete();
                Db::name('admin_admin_permission')->where('permission_id',$v['id'])->delete();
            }
        }else{
            if (isset($model->child) && !$model->child->isEmpty()){
                return ['msg'=>'存在子权限，确认删除后不可恢复','code'=>201];
            }
        }
        $model->delete();
        Db::name('admin_role_permission')->where('permission_id', $id)->delete();
        Db::name('admin_admin_permission')->where('permission_id', $id)->delete();
        rm();
		Session::clear();
    }

}
