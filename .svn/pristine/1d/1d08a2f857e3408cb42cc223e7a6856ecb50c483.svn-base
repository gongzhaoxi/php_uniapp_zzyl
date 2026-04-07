<?php
namespace app\fyl\model;
use app\common\model\UploadModel;
class MaterialBom extends UploadModel{
	protected $connection = 'db_fyl';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
	protected $dateFormat = 'Y-m-d H:i:s';
	protected $updateTime = false;

	public function material(){
		return $this->belongsTo('app\fyl\model\Material','material_id','id');
	}
	
	public function product(){
		return $this->belongsTo('app\fyl\model\Material','pid','id');
	}
	
}