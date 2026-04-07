<?php
return [

    //用户token（登录令牌）配置
    'user_token' => [
		'cookie_key'		=>'user_token'	,	
		'expire_duration' 	=> 3600*24*7 ,//用户token过期时长(单位秒）
        'be_expire_duration'=> 3600,//用户token临时过期前时长，自动续期
    ],
    'wechat_oa' => [
		'app_id' 		=> 'wx3cf0f39249eb0xxx',
		'secret' 		=> 'f1c242f4f28f735d4687abb469072xxx',
		'token' 		=> 'TestToken',
		'response_type' => 'array',
    ],	
    'wechat_mnp' => [
		'app_id' 		=> 'wx3cf0f39249eb0xxx',
		'secret' 		=> 'f1c242f4f28f735d4687abb469072xxx',
		'response_type' => 'array',
    ],
	
    'user_permission' => [
		'1' 			=> '零件检验',
		'2' 			=> '生产报工',
		'3' 			=> '车间库存',
		'4' 			=> '仓库管理',
		'5' 			=> '成品出库',
		'6' 			=> '零件检验 - 编辑检验报告',
		'7' 			=> '零件检验 - 检验审批',
		'8' 			=> '零件检验 - 主管审批',
		'9' 			=> '生产报工 - 完成随工单',
		'10' 			=> '不合格/返工评审处理单 - 编辑',
		'11' 			=> '不合格/返工评审处理单 - 生产主管',
		'12' 			=> '不合格/返工评审处理单 - 质管主管',
		'13' 			=> '零件检验 - 编辑送检信息',
		'14' 			=> '零件退仓 - 审批',
		'15' 			=> '零件列表',
		'16' 			=> '指导书',
		'17' 			=> '图纸集',
		
    ],	
	
];
