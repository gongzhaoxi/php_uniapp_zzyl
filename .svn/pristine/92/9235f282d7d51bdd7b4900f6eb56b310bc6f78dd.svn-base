<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpDrawing};
use app\admin\validate\ErpDrawingValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use app\common\util\FileUtil;

class ErpDrawingLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 	= 'ed.*,em.name';
		$query['_alias']= 'ed';
		$query['_material_alias']= 'em';
        $list 	= ErpDrawing::alias('ed')->join('erp_material em','ed.sn = em.sn','LEFT')->withSearch(['query'],['query'=>$query])->field($field)->append(['status_desc','first_check','final_check'])->order('ed.final_check_time asc,ed.first_check_time asc,ed.id desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	// 获取列表 平板端
	public static function getLista($query=[],$limit=10)
	{
		$field 	= 'ed.*,em.name';
		$query['_alias']= 'ed';
		$query['_material_alias']= 'em';
	    $list 	= ErpDrawing::alias('ed')->join('erp_material em','ed.sn = em.sn','LEFT')->withSearch(['query'],['query'=>$query])->field($field)->append(['status_desc','first_check','final_check'])->order('ed.final_check_time desc,ed.id desc')->paginate($limit);
	    return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
	}

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpDrawingValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$data['designer'] 	= empty(self::$adminUser['nickname'])?'':self::$adminUser['nickname'];
            ErpDrawing::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpDrawing::where($map)->find();
		}else{
			return ErpDrawing::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpDrawingValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpDrawingValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			$drawing 	= ErpDrawing::where('id','in',$data['ids'])->field('pic,final_pic')->select();
			$fileUtil 	= new FileUtil();
			foreach($drawing as $vo){
				if($vo['pic']){
					$fileUtil->unlinkFile('.'.$vo['pic']);
				}
				if($vo['final_pic']){
					$fileUtil->unlinkFile('.'.$vo['final_pic']);
				}
			}
			
			ErpDrawing::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	

    public static function goFirstCheck($id)
    {
		$model 		= self::getOne($id);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        if($model->first_check_time) {
			return ['msg'=>'已初审','code'=>201];
		}		
        try {
			$model->save(['check_status'=>1,'first_check_time'=>time(),'first_check_name'=>self::$adminUser['nickname']]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    // 编辑
    public static function goFinalCheck($data)
    {
        //验证
        $validate 	= new ErpDrawingValidate;
        if(!$validate->scene('finalCheck')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        if($model->final_check_time) {
			return ['msg'=>'已终审','code'=>201];
		}		
        try {
			$data['check_status'] 		= 2;
			$data['final_check_time'] 	= time();
			$data['final_check_name'] 	= self::$adminUser['nickname'];
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
	// 导入
    public static function goImport($file)
    {
		if(empty($file) || !is_file('.'.$file)) {
			return ['msg'=>'excel文件不存在','code'=>201];
		}
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '300');
		try {

			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			$sheet 		= $spreadsheet->getActiveSheet();

			$field 		= ['A'=>'sn','B'=>'pic'];
		
			$res 		= [];
			$photo		= [];
			$floder		= '/upload/image/'.date('Ymd').'/';
			if(!file_exists('.'.$floder)) {
				mkdir('.'.$floder);
            }			
			
			foreach ($sheet->getDrawingCollection() as $drawing) {
				list($startColumn, $startRow) = Coordinate::coordinateFromString($drawing->getCoordinates());					
				$imageFileName 			= md5($drawing->getCoordinates() . mt_rand(1000, 9999).'_'.time());
				switch ($drawing->getExtension()) {
					case 'jpg':
					case 'jpeg':
						$imageFileName .= '.jpg';
						$source 		= imagecreatefromjpeg($drawing->getPath());
						imagejpeg($source, '.'.$floder . $imageFileName);
						break;
					case 'gif':
						$imageFileName 	.= '_'.time().'.gif';
						$source 		= imagecreatefromgif($drawing->getPath());
						imagegif($source, '.'.$floder . $imageFileName);
					break;
					case 'png':
						$imageFileName .= '_'.time().'.png';
						$source 		= imagecreatefrompng($drawing->getPath());
						imagepng($source, '.'.$floder. $imageFileName);
						break;
				}
				$photo[$startColumn][$startRow] =  $floder . $imageFileName;
			}
				
			foreach($sheet->getRowIterator(2) as $ii=>$row) {	
				$tmp 	= [];
				foreach ($row->getCellIterator() as $k=>$cell) {				
					if(empty($field[$k])){
						break;
					}
					$value 			= delete_html($cell->getFormattedValue());					
					if($field[$k] == 'sn' && !$value){
						$tmp 		= [];
						break;
					}
					if($field[$k] == 'pic'){
						if(!empty($photo[$k][$ii])){
							$tmp[$field[$k]] = $photo[$k][$ii];
						}
					}else{
						$tmp[$field[$k]] = $value;
					}
				}
				if($tmp){
					$tmp['designer'] 	= empty(self::$adminUser['nickname'])?'':self::$adminUser['nickname'];
					$res[] 				= $tmp;
				}
			}
			if($res){
				(new ErpDrawing)->saveAll($res);
			}
			unlink('.'.$file);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

}
