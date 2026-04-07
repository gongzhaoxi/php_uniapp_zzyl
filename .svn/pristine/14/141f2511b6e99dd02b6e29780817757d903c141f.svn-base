<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Session;
use think\facade\Cookie;
use think\facade\Db;
use app\common\model\AdminAdmin;
use app\common\model\AdminAdminLog;
use app\common\model\AdminPermission;
use app\common\model\AdminRole;
use app\admin\validate\AdminAdminValidate;

class AdminAdminLogic extends BaseLogic{
    
	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $map 		= [];
		$map[] 		= ['id','>','1'];
		$map[] 		= ['id','<>',Session::get('admin.id')];
        if(!empty($query['username'])) {
            $map[] 	= ['username', 'like', "%".$query['username']."%"];
        }
        $list 		= AdminAdmin::where($map)->order('id','desc')->withoutField('password,token,delete_time')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function goFind($id){
        return AdminAdmin::find($id);
    }
	
    // 获取管理拥有的角色
    public static function getRole($id)
    {
        $admin = AdminAdmin::with('roles')->where('id',$id)->find();
        $roles = AdminRole::select();
        foreach ($roles as $k=>$role){
            if (isset($admin->roles) && !$admin->roles->isEmpty()){
                foreach ($admin->roles as $v){
                    if ($role['id']==$v['id']){
                        $roles[$k]['own'] = true;
                    }
                }
            }
        }
        return ['admin'=>$admin,'roles'=>$roles];
    }

    // 获取用户直接权限
    public static function getPermission($id)
    {
        $admin 			= AdminAdmin::with('directPermissions')->find($id);
        $permissions 	= AdminPermission::order('sort','asc')->select();
        foreach ($permissions as $permission){
            foreach ($admin->direct_permissions as $v){
                if ($permission->id == $v['id']){
                    $permission->own = true;
                }
            }
        }
        $permissions = get_tree($permissions->toArray());
        return ['admin'=>$admin,'permissions'=>$permissions];
    }	
	
	// 添加
    public static function goAdd($data){
        //验证
        $validate = new AdminAdminValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			AdminAdmin::create($data);
        }catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    // 编辑
    public static function goEdit($data){
        //验证
        $validate 	= new AdminAdminValidate;
        if(!$validate->scene('edit')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            $model 	= self::goFind($data['id']);
			if ($model->isEmpty())  return ['msg'=>'数据不存在','code'=>201];
            //是否需要修改密码
            if(empty($data['password'])){
				unset($data['password']);
            }
            $model->save($data); 
			rm();
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
                'token' => null
             ]);
             rm();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($id)
    {
        $model = self::goFind($id);
        if ($model->isEmpty()) return ['msg'=>'数据不存在','code'=>201];
        try{
            $model->delete();
            Db::name('admin_admin_role')->where('admin_id', $id)->delete();
            Db::name('admin_admin_permission')->where('admin_id', $id)->delete();
            rm();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 批量删除
    public static function goBatchRemove($ids)
    {
        if (!is_array($ids)) return ['msg'=>'数据不存在','code'=>201];
        try{
            AdminAdmin::destroy($ids);
            Db::name('admin_admin_role')->whereIn('admin_id', $ids)->delete();
            Db::name('admin_admin_permission')->whereIn('admin_id', $ids)->delete();
            rm();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 用户分配角色
    public static function goRole($data,$id)
    {
		Db::startTrans();
		try{
			//清除原先的角色
			Db::name('admin_admin_role')->where('admin_id',$id)->delete();
			//添加新的角色
			if($data){
				foreach ($data as $v){
					Db::name('admin_admin_role')->insert([
						'admin_id' => $id,
						'role_id' => $v,
					]);
				}
			}
			Db::commit();
			rm();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
    }

    // 用户分配直接权限
    public static function goPermission($data,$id)
    {
		Db::startTrans();
		try{
			//清除原有的直接权限
			Db::name('admin_admin_permission')->where('admin_id',$id)->delete();
			//填充新的直接权限
			if($data){
				foreach ($data as $v){
					Db::name('admin_admin_permission')->insert([
						'admin_id' => $id,
						'permission_id' => $v,
					]);
				}
			}
			Db::commit();
		}catch (DbException $exception){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
    }

    // 获取列表
    public static function getRecycle($query=[],$limit=10)
    {
        $map 		= [];
        if(!empty($query['username'])) {
            $map[]	= ['username', 'like', "%".$query['username']."%"];
        }
        $list 		= AdminAdmin::onlyTrashed()->order('id','desc')->withoutField('password,token')->where($map)->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
        
    }

    // 获取列表
    public static function batchRecycle($ids,$type)
    {
		if (!is_array($ids)) return ['msg'=>'参数错误','code'=>'201'];
		try{
			if($type){
				$data = AdminAdmin::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}
			}else{
				AdminAdmin::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }

     // 修改密码
     public static function goPass($data)
     {
        $validate = new AdminAdminValidate;
        if(!$validate->scene('pass')->check($data)) 
        return ['msg'=>$validate->getError(),'code'=>201];
		$model =  self::goFind(Session::get('admin.id'));
		$model->save($data);
		self::logout();
     }
    

    // 用户登录验证
    public static function login(array $data)
    {
        $validate = new AdminAdminValidate;
        if(!$validate->scene('login')->check($data)) 
			return ['msg'=>$validate->getError(),'code'=>201];
        //验证用户
        $admin = AdminAdmin::where([
            'username' => trim($data['username']),
            'password' => set_password(trim($data['password'])),
            'status' => 1
            ])->find();
        if(!$admin) return ['msg'=>'用户名密码错误','code'=>201];
        $admin->token = rand_string().$admin->id.microtime(true);
        $admin->save();
        //是否记住密码
        $time = 3600;
        if (isset($data['remember'])) $time = 30 * 86400;
        //缓存登录信息
        $info = [
            'id' 		=> $admin->id,
            'token'		=> $admin->token,
			'username'	=> $admin->username,
			'nickname'	=> $admin->nickname,
            'menu' 		=> self::permissions($admin->id)
        ];
        Session::set('admin', $info);
        Cookie::set('token',$admin->token, $time);
        // 触发登录成功事件
        event('AdminLog');
        return ['msg'=>'登录成功'];
    }
    
    // 判断是否登录
    public static function isLogin()
    {
        if(Session::get('admin')) return true; 
        if(Cookie::has('token')){
            $admin = AdminAdmin::where(['token'=>Cookie::get('token'),'status'=>1])->find();
            if(!$admin) return false;
            return Session::set('admin',[
                'id' 		=> $admin->id,
                'token' 	=> $admin->token,
				'username'	=> $admin->username,
				'nickname'	=> $admin->nickname,
                'menu' 		=> self::permissions($admin->id)
            ]); 
        }
        return false;
    }
    
    // 退出登陆
    public static function logout()
    {
        Session::delete('admin');
        Cookie::delete('token');
        Cookie::delete('sign');
        return ['msg'=>'退出成功'];
    }
	
	
    // 用户的所有权限
    public static function permissions($id)
    {

        $admin = AdminAdmin::with(['roles.permissions', 'directPermissions'])->findOrEmpty($id)->toArray();
        $permissions = [];
        //超级管理员缓存所有权限
        if ($admin['id'] == 1){
            $perms = AdminPermission::order('sort','asc')->select()->toArray();
            foreach ($perms as $p){
                if($p['status'] == 1){
                    $permissions[$p['id']] =  $p;
					$permissions[$p['id']]['href'] = is_url($p['href'])??(string)url(ltrim($p['href'],'/'));
					$permissions[$p['id']]['auth'] = strtolower(ltrim($p['href'],'/'));
					$permissions[$p['id']]['icon'] = $p['icon']?('layui-icon '.$p['icon']):'';
                 }
            }
        }else{
             //处理角色权限
             if (isset($admin['roles']) && !empty($admin['roles'])) {
                foreach ($admin['roles'] as $r) {
                    if (isset($r['permissions']) && !empty($r['permissions'])) {
                        foreach ($r['permissions'] as $p) {
                            if($p['status'] == 1){
                                $permissions[$p['id']] =  $p;
                                $permissions[$p['id']]['href'] = is_url($p['href'])??(string)url(ltrim($p['href'],'/'));
								$permissions[$p['id']]['auth'] = strtolower(ltrim($p['href'],'/'));
								$permissions[$p['id']]['icon'] = $p['icon']?('layui-icon '.$p['icon']):'';
                             }
                        }
                    }
                }
            }
            //处理直接权限
            if (isset($admin['directPermissions']) && !empty($admin['directPermissions'])) {
                foreach ($admin['directPermissions'] as $p) {
                    if($p['status'] == 1){
						$permissions[$p['id']] =  $p;
						$permissions[$p['id']]['href'] = is_url($p['href'])??(string)url(ltrim($p['href'],'/'));
						$permissions[$p['id']]['auth'] = strtolower(ltrim($p['href'],'/'));
						$permissions[$p['id']]['icon'] = $p['icon']?('layui-icon '.$p['icon']):'';
                    }
                }
            }
            $key = array_column($permissions, 'sort');
            array_multisort($key,SORT_ASC,$permissions);
        }
        //合并权限为用户的最终权限
        return $permissions;
    }
	
	
    // 获取日志列表
    public static function getLog($query=[],$limit=10)
    {
        $map 		= [];
        if(!empty($query['uid'])) {
            $map[] 	= ['uid', '=',$query['uid']];
        }
        $list 		= AdminAdminLog::with('log')->where($map)->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }	
	
	public static function admins()
    {
        return AdminAdmin::field('id,username,nickname')->where('status',1)->select();
    }
	
}
