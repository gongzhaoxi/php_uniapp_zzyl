<?php
declare (strict_types = 1);
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class AdminRole extends BaseModel
{
    use SoftDelete;

    // 角色所有的权限
    public function permissions()
    {
        return $this->belongsToMany('AdminPermission','admin_role_permission','permission_id','role_id');
    }
}
