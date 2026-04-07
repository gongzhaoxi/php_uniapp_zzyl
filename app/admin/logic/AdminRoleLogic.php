<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Db;
use app\common\model\AdminRole;
use app\common\model\AdminPermission;
use app\admin\validate\AdminRoleValidate;

class AdminRoleLogic extends BaseLogic{

	
	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $list = AdminRole::order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 获取用户直接权限
    public static function getPermission($id)
    {
        $role = AdminRole::with('permissions')->find($id);
        $permissions = AdminPermission::order('sort','asc')->select();
        foreach ($permissions as $permission){
            foreach ($role->permissions as $v){
                if ($permission->id == $v['id']){
                    $permission->own = true;
                }
            }
        }
        $permissions = get_tree($permissions->toArray());
        return ['role'=>$role,'permissions'=>$permissions];
    }
	
	
    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new AdminRoleValidate;
        if(!$validate->scene('add')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            AdminRole::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function goFind($id)
    {
       return AdminRole::find($id);
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate = new AdminRoleValidate;
        if(!$validate->scene('edit')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            AdminRole::update($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($id)
    {
        $model = self::goFind($id);
        if ($model->isEmpty()) 
			return ['msg'=>'数据不存在','code'=>201];
        try{
            $model->delete();
            Db::name('admin_admin_role')->where('role_id', $id)->delete();
            Db::name('admin_role_permission')->where('role_id', $id)->delete();
            rm();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 用户分配角色
    public static function goRole($data,$id)
    {
        if($data){
            Db::startTrans();
            try{
                //清除原先的角色
                Db::name('admin_admin_role')->where('admin_id',$id)->delete();
                //添加新的角色
                foreach ($data as $v){
                    Db::name('admin_admin_role')->insert([
                        'admin_id' => $id,
                        'role_id' => $v,
                    ]);
                }
                Db::commit();
                rm();
            }catch (\Exception $e){
                Db::rollback();
                return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
            }
        }
    }

    // 用户分配直接权限
    public static function goPermission($data,$id)
    {
        if($data){
            Db::startTrans();
            try{
                //清除原有的直接权限
                Db::name('admin_role_permission')->where('role_id',$id)->delete();
                //填充新的直接权限
                foreach ($data as $p){
                    Db::name('admin_role_permission')->insert([
                        'role_id' => $id,
                        'permission_id' => $p,
                    ]);
                }
                rm();
                Db::commit();
            }catch (DbException $e){
                Db::rollback();
                return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
            }
        }
    }

    // 获取列表
    public static function getRecycle($query=[],$limit=10)
	{
        $list = AdminRole::onlyTrashed()->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function goRecycle($ids,$type)
    {
		if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
		try{
			if($type){
				$data = AdminRole::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}
			}else{
				AdminRole::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }

}
