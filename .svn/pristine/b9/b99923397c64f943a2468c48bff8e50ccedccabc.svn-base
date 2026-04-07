<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpNoticeEnum;

class ErpNotice extends BaseModel
{
	
	protected $json = ['file'];
	protected $jsonAssoc = true;
	
    public function getStatusDescAttr($value, $data)
    {
        return ErpNoticeEnum::getStatusDesc($data['status']);
    }	
	
    public function setNoticeAdminIdAttr($value, $data)
    {
        return is_array($value)?implode(',',$value):$value;
    }
	
    public function getNoticeAdminIdAttr($value, $data)
    {
        return $value?explode(',',$value):[];
    }	
	
    public function setViewedAdminIdAttr($value, $data)
    {
		return is_array($value)&&$value?(','.implode(',',$value).','):'';
    }
	
    public function getViewedAdminIdAttr($value, $data)
    {
        return $value?explode(',',trim($value,',')):[];
    }	
	
    public function getNoticeAdminAttr($value, $data)
    {
        return AdminAdmin::where('id','in',$data['notice_admin_id'])->column('nickname');
    }	
	
    public function getViewedAdminAttr($value, $data)
    {
        return AdminAdmin::where('id','in',$data['viewed_admin_id'])->column('nickname');
    }	
	
	public function admin(){
		return $this->belongsTo('app\common\model\AdminAdmin','admin_id','id');
	}
	
	public function auditingAdmin(){
		return $this->belongsTo('app\common\model\AdminAdmin','auditing_admin_id','id');
	}	
	
	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['content'])) {
            $query->where('content', 'like', '%' . $value['content'] . '%');
        }
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where('status', '=', $value['status']);
        }
        if (isset($value['admin_id'])) {
            $query->where('admin_id', '=', $value['admin_id']);
        }
        if (!empty($value['create_time'])) {
			$create_time = is_array($value['create_time'])?$value['create_time']:explode('至',$value['create_time']);
			if(!empty($create_time[0])){
				$query->where('create_time', '>', strtotime(trim($create_time[0])));
			}
			if(!empty($create_time[1])){
				$query->where('create_time', '>', strtotime(trim($create_time[1]))+24*3600);
			}
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }	
	
}