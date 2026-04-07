<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\YesNoEnum;

class ErpProduct extends BaseModel{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
	protected $json = ['qc_file','produce_file','num_version'];
	protected $jsonAssoc = true;
	
	public function bom(){
		return $this->hasMany('app\common\model\ErpProductBom','product_id','id')->where('project_id',0);
	}
	
	public function project(){
		return $this->hasMany('app\common\model\ErpProductProject','product_id','id')->order('sort desc');
	}	
	
    public function getBomFomatAttr($value, $data)
    {
		$bom 						= [];
		foreach($this->bom as $vo){
			$bom[$vo['data_type']][]= $vo;
		}
		return $bom;
    }
	
	public function category(){
		return $this->belongsTo('app\common\model\DictData','cid','id');
	}
	
    public function getCategoryNameAttr($value, $data)
    {
		$category = get_dict_data('product_category');
		return $category&&!empty($category[$data['cid']])?$category[$data['cid']]['name']:'';
    }	
	
    public function getStatusDescAttr($value, $data)
    {
        return YesNoEnum::getIsOpenDesc($data['status']);
    }		
	
	public function getPhotoLinkAttr($value,$data)
    {
        return get_browse_url($data['photo']);
    }
	
	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }
        if (!empty($value['sn'])) {
            $query->where('sn', 'like', '%' . $value['sn'] . '%');
        }	
        if (!empty($value['remark'])) {
            $query->where('remark', 'like', '%' . $value['remark'] . '%');
        }		
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where('status', '=', $value['status']);
        }
        if (!empty($value['cid'])) {
			$query->where('cid', '=', $value['cid']);
        }	
        if (!empty($value['id'])) {
			$query->where('id', '=', $value['id']);
        }	
        if (!empty($value['region_type'])) {
			$query->where('region_type', '=', $value['region_type']);
        }		
        if (!empty($value['keyword'])) {
            $query->where('sn|name|specs|model', 'like', '%' . $value['keyword'] . '%');
        }	
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }
	
	public function getQcFileLinkAttr($value,$data)
    {
		$data 						= $data['qc_file'];
		if(!empty($data['file'])){
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
			}
		}
        return $data;
    }
	
	public function getProduceFileLinkAttr($value,$data)
    {
		$data 						= $data['produce_file'];
		if(!empty($data['file'])){
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
			}
		}
        return $data;
    }	
	
	public function getNumVersionLinkAttr($value,$data)
    {
		$data 						= $data['num_version'];
		if(!empty($data['file'])){
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
			}
		}
        return $data;
    }	
	
	
 	public function getGuideBookAttr($value, $data){
		return ErpGuideBook::whereRaw("FIND_IN_SET('".$data['id']."',data_id) or FIND_IN_SET('0',data_id)")->where('data_type','=',3)->order('data_id asc,id desc')->select();
    }	
	
}