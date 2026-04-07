<?php
namespace app\common\model;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;


/**
 * 字典类型模型
 * Class DictType
 * @package app\common\model
 */
class DictType extends BaseModel
{

    use SoftDelete;
    protected $deleteTime = 'delete_time';


    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '正常' : '停用';
    }

}