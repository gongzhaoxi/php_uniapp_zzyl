<?php
declare (strict_types = 1);
namespace app\supplier\validate;
use think\Validate;

class ErpPurchaseOrderFeedbackValidate extends Validate{

    protected $rule = [
        'order_id|订单' 		=> 'require',
		'content|反馈内容' 		=> 'require',
    ];
    
    public function sceneAdd(){
       return $this->only(['order_id','content']);
    }
}
