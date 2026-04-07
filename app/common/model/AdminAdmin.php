<?php
declare (strict_types = 1);
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class AdminAdmin extends BaseModel
{
    use SoftDelete;

	public function setPasswordAttr($value,$data){
		return set_password($value);
	}

    // 管理拥有的角色
    public function roles()
    {
        return $this->belongsToMany('AdminRole', 'admin_admin_role', 'role_id', 'admin_id');
    }

    // 管理的直接权限
    public function directPermissions()
    {
        return $this->belongsToMany('AdminPermission', 'admin_admin_permission', 'permission_id', 'admin_id');
    }


}
