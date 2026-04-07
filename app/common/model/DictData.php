<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\model\DictType;
/**
 * 字典数据模型
 * Class DictData
 * @package app\common\model
 */
class DictData extends BaseModel
{

    use SoftDelete;

    protected $deleteTime = 'delete_time';

    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '正常' : '停用';
    }

	public static function onBeforeWrite($record){
		if($record['type_id']){
			$record->type_value	= DictType::where('id',$record['type_id'])->value('type');
		}		
    }

	public static function onAfterUpdate($record){
		$record->updateDataCache($record);
	}

	public static function onAfterInsert($record){
		$record->updateDataCache($record);
	}

    public static function onAfterDelete($record){
		$record->updateDataCache();
    }	
	
	public function updateDataCache($record){
		cache('dict_data',null);
		if($record['type_id'] == 6){
			ErpProductProject::where('cid',$record['id'])->update(['sort'=>$record['sort']]);
		}
	}

}