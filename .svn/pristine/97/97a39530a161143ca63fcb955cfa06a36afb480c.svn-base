<?php
namespace app\index\validate;
use think\Validate;

/**
 * 车间员工验证
 * Class CustomerValidate
 * @package app\index\validate
 */
class ErpOrderProduceProcessValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'produce_sn|产品编码' 	=> 'require',
		//'bom_sn|部件标签编码' 	=> 'require',
		'process_id|工序' 		=> 'require|number',
        'type|进度结果' 		=> 'require',
		'error|异常说明' 		=> 'requireIf:type,3',
		'photo|附图' 			=> 'requireIf:type,3|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
    ];

    public function sceneAdd(){
		return $this->only(['produce_sn','bom_sn','process_id','type','error','photo']);
    }




}