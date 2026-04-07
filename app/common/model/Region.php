<?php
namespace app\common\model;


use think\Model;

class Region extends Model
{
	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where('status', '=', $value['status']);
        }
        if (isset($value['parent_id']) && $value['parent_id'] !== '') {
            $query->where('parent_id', '=', $value['parent_id']);
        }
        if (isset($value['level']) && $value['level'] !== '') {
            $query->where('level', '=', $value['level']);
        }		
    }
}