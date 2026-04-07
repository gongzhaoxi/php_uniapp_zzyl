<?php
namespace app\index\validate;
use think\Validate;

class ErpOrderProduceErrorValidate extends Validate{

    protected $rule = [
        'type|进度结果' 		=> 'require',
		'material_name|物料名称'=> 'requireIf:type,2',
		'lack_num|报缺数量'		=> 'requireIf:type,2',
        'produce_sn|产品编码' 	=> 'requireIf:type,3',
		'check_type|检验类型'	=> 'requireIf:type,3',
		'error|异常说明' 		=> 'require',
		'photo|附图' 			=> 'requireIf:type,3|array',

    ];

    protected $message = [
        
    ];

}