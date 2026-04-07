<?php
use app\common\model\DictData ;
use app\common\model\ArticleCategory;
use think\facade\Db;
use think\facade\Session;
// 应用公共文件

if (!function_exists('rm')) {
    //清除缓存
    function rm()
    {
        //delete_dir(root_path().'runtime');
    }
}

if (!function_exists('is_url')){
    //是否
    function is_url($url)
    {
        if(preg_match("/^http(s)?:\\/\\/.+/",$url)) return $url;
    }
}

if (!function_exists('rand_string')) {
    /**
     *  随机数
     *
     * @param string $length 长度
     * @param string $type   类型
     * @return void
     */
    function rand_string($length = '32',$type=4): string
    {
        $rand='';
        switch ($type) {
            case '1':
                $randstr= '0123456789';
                break;
            case '2':
                $randstr= 'abcdefghijklmnopqrstuvwxyz';
                break;
            case '3':
                $randstr= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default:
                $randstr= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }
        $max = strlen($randstr)-1;
        mt_srand((double)microtime()*1000000);
        for($i=0;$i<$length;$i++) {
            $rand.=$randstr[mt_rand(0,$max)];
        }
        return $rand;
    }
}


/**
 * Desc: 用时间生成编号
 * @param $table
 * @param $field
 * @param string $prefix
 * @param int $rand_suffix_length
 * @param array $pool
 * @return string
 */
function create_sn($table, $field='sn', $prefix = '', $rand_suffix_length = 4, $pool = [])
{
    $suffix = '';
    for ($i = 0; $i < $rand_suffix_length; $i++) {
        if (empty($pool)) {
            $suffix .= rand(0, 9);
        } else {
            $suffix .= $pool[array_rand($pool)];
        }
    }
    $sn = $prefix . date('YmdHis') . $suffix;
	if(is_string($table)){
		$count = Db::name($table)->where($field, $sn)->count();
	}else{
		$count = $table->removeOption('where')->where($field, $sn)->count();
	}
    if($count) {
        return create_sn($table, $field, $prefix, $rand_suffix_length, $pool);
    }
    return $sn;
}


if (!function_exists('set_password')) {
    //密码截取
    function set_password($password): string
    {
      return substr(md5($password), 3, -3);
    }
}

/**
 * 数据签名认证
 */
function data_sign($data = [])
{
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data);
    $code = http_build_query($data);
    $sign = sha1($code);
    return $sign;
}

/**
 * 修改网站配置文件
 */
if (!function_exists('set_web')) {
    function set_web($data = [])
    {
		$web = config('web');
		$web = $web?$web:[];
		foreach ($data as $key => $value) {
			$web[$key] = $value;
		}
		
        $str = "<?php\r\n/**\r\n * 系统配置文件\r\n */\r\nreturn [\r\n";
        foreach ($web as $key => $value) {
            if(is_array($value)){
            $str .= get_arr_tree($key,$value);
            }else{
                $str .= "\t'$key' => '$value',";
                $str .= "\r\n";
            }
        }
        $str .= '];';
        @file_put_contents(config_path().'web.php', $str);
    }
}

if (!function_exists('get_arr_tree')) {
    /**
     * 递归配置数组
     */
    function get_arr_tree($key,$data,$level="\t")
    {
        $i = "$level'$key' => [\r\n";
        foreach ($data as $k => $v) {
            if(is_array($v)){
                $i .= get_arr_tree($k,$v,$level."\t");
            }else{
                $i .= "$level\t'$k' => '$v',";
                $i .= "\r\n";      
            }
        }
        return  $i."$level".'],'."\r\n";
    }
}

if (!function_exists('aes_encrypt')) {
    /**
     *
     * @param string $string 需要加密的字符串
     * @param string $key 密钥
     * @return string
     */
    function aes_encrypt($string, $key="ONSPEED"): string
    {
        $data = openssl_encrypt($string, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return strtolower(bin2hex($data));
    }
}

if (!function_exists('aes_decrypt')) {
    /**
     * @param string $string 需要解密的字符串
     * @param string $key 密钥
     * @return string
     */
    function aes_decrypt($string, $key="ONSPEED"): string
    {
        try {
            return openssl_decrypt(hex2bin($string), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        }catch (\Exception $e){
            return false;
        }
    }
}

if (!function_exists('get_field')) {
    /**
     * 获取指定表指定行指定字段
     * @param  string       $tn      完整表名
     * @param  string|array $where   参数数组或者id值
     * @param  string       $field   字段名,默认'name'
     * @param  string       $default 获取失败的默认值,默认''
     * @param  array        $order   排序数组
     * @return string                获取到的内容
     */
    function get_field($tn, $where, $field = 'name', $default = '', $order = ['id' => 'desc'])
    {
        if (!is_array($where)) {
            $where = ['id' => $where];
        }
        $row = \think\facade\Db::name($tn)->field([$field])->where($where)->order($order)->find();
        return $row === null ? $default : $row[$field];
    }
  }

  if (!function_exists('delete_dir')) {
    /**
     * 遍历删除文件夹所有内容
     * @param  string $dir 要删除的文件夹
     */
    function delete_dir($dir)
    {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != '.' && $file != '..') {
                $filepath = $dir . '/' . $file;
                if (is_dir($filepath)) {
                    delete_dir($filepath);
                } else {
                    @unlink($filepath);
                }
            }
        }
        closedir($dh);
        @rmdir($dir);
    }
  }

  if (!function_exists('get_tree')) {
    /**
     * 递归无限级分类权限
     * @param array $data
     * @param int $pid
     * @param string $field1 父级字段
     * @param string $field2 子级关联的父级字段
     * @param string $field3 子级键值
     * @return mixed
     */
    function get_tree($data, $pid = 0, $field1 = 'id', $field2 = 'pid', $field3 = 'children')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($v[$field2] == $pid) {
                $v[$field3] = get_tree($data, $v[$field1],$field1 ,$field2 );
                $arr[] = $v;
            }
        }
        return $arr;
    }
  }

  if (!function_exists('hump_underline')) {
    /**
     * 驼峰转下划线
     * @param  string $str 需要转换的字符串
     * @return string      转换完毕的字符串
     */
    function hump_underline($str)
    {
        return strtolower(trim(preg_replace('/[A-Z]/', '_\\0', $str), '_'));
    }
 }

  if (!function_exists('underline_hump')) {
    /**
     * 下划线转驼峰
     * @param  string $str 需要转换的字符串
     * @return string      转换完毕的字符串
     */
    function underline_hump($str)
    {
        return ucfirst(
            preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $str)
        );
    }
  }

  if (!function_exists('record_log')){
    /**
     * @记录日志
     * @param [type] $param
     * @param string $file
     *
     * @return void
     */
     function record_log($param,$file=''){
        $path = root_path().'log/'.$file."/";
        if (!is_dir($path)) @mkdir($path,0777,true);
        if (is_array($param)){
            $param = json_encode($param,JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
        }
        @file_put_contents(
            $path.date("Y_m_d",time()).".txt",
            "执行日期："."\r\n".date('Y-m-d H:i:s', time()) . ' ' . "\n" . $param . "\r\n",
            FILE_APPEND
        );
    }
}

function get_browse_domain(){
	$config		= config('web');
	$browse 	= request()->domain();
	if($config['file-type'] == 2) {
		$browse	= $config['file-endpoint'];
	}else if($config['file-type'] == 3) {
		$browse = $config['qiniu-Domain'];
	}
	return $browse;
}

function get_browse_url($value){
	if(!$value){
		return '';
	}
	if(substr(strtolower($value),0,4) == 'http'){
		return $value;
	}
	$browse = get_browse_domain();
	return $browse.$value;
}

function  get_dict_data($type){
	$data 				= cache('dict_data');
	$data				= $data?$data:[];
	if(empty($data[$type])){
		$data[$type] 	= DictData::where('type_value',$type)->where('status',1)->order(['sort'=>'desc','id'=>'asc'])->column('id,name,value,type_id,type_value,remark,multiple,sort','id');
		cache('dict_data',$data);
	}
	return $data[$type];
}


/**
 * @notes 去除内容图片域名
 * @param $content
 * @return array|string|string[]
 * @author 段誉
 * @date 2022/9/26 10:43
 */
function clear_file_domain($content)
{
	$fileUrl = get_browse_domain().'/';
	if($fileUrl){
		return str_replace($fileUrl, '/', $content);
	}else{
		return $content;
	}
}


/**
 * @notes 设置内容图片域名
 * @param $content
 * @return array|string|string[]|null
 * @author 段誉
 * @date 2022/9/26 10:43
 */
function get_file_domain($content)
{
    $preg 		= '/(<img .*?src=")[^https|^http](.*?)(".*?>)/is';
    $fileUrl 	= get_browse_domain().'/';
	if($fileUrl){
		return preg_replace($preg, "\${1}$fileUrl\${2}\${3}", $content);
	}else{
		return $content;
	}
}

function  get_article_category($id=true){
	$data 		= cache('article_category');
	if(empty($data)){
		$data 	= ArticleCategory::where('status',1)->order(['sort'=>'desc','id'=>'asc'])->column('*','id');
		cache('article_category',$data);
	}
	if($id === true){
		return $data?$data:[];
	}
	return $id&&!empty($data[$id])?$data[$id]:[];
}

function remove_emoji($clean_text) {
	// Match Emoticons
	$regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
	$clean_text = preg_replace($regexEmoticons, '', $clean_text);

	// Match Miscellaneous Symbols and Pictographs
	$regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
	$clean_text = preg_replace($regexSymbols, '', $clean_text);

	// Match Transport And Map Symbols
	$regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
	$clean_text = preg_replace($regexTransport, '', $clean_text);

	// Match Miscellaneous Symbols
	$regexMisc = '/[\x{2600}-\x{26FF}]/u';
	$clean_text = preg_replace($regexMisc, '', $clean_text);

	// Match Dingbats
	$regexDingbats = '/[\x{2700}-\x{27BF}]/u';
	$clean_text = preg_replace($regexDingbats, '', $clean_text);

	return $clean_text;
}
/**
 * XSS跨站脚本漏洞过滤函数
 * @param $str
 * @return string
 */
function remove_xss($str){
	$str 	= preg_replace ( '/([\x00-\x08\x0b-\x0c\x0e-\x19])/', '', $str );
	$search	= [
		'@<script[^>]*?>.*?</script>@si',  
		'@<style[^>]*?>.*?</style>@siU',   
		'@<![\s\S]*?--[ \t\n\r]*>@',
		"/<(\/?)(script|i?frame|style|html|embed|object|iframe|vbscript|applet|xml|body|title|link|meta|\?|\%)([^>]*?)>/isU", 
		"/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",//过滤javascript的on事件     
	]; 
	$str 	= preg_replace($search, '', $str);
	//$str 	= strip_tags($str,$tags);
	return $str;
}
//去除字符串的html标签
function delete_html($str,$isEn = '') {
	$str = trim ( $str ); //清除字符串两边的空格
	$str = strip_tags ( $str, "" ); //利用php自带的函数清除html格式
	$str = preg_replace ( "/\t/", "", $str ); //使用正则表达式匹配需要替换的内容，如：空格，换行，并将替换为空。
	$str = preg_replace ( "/\r\n/", "", $str );
	$str = preg_replace ( "/\r/", "", $str );
	$str = preg_replace ( "/\n/", "", $str );
	if($isEn){
		$str = preg_replace ( "/ /", "", $str );
	}
	$str = preg_replace ( "/&nbsp;/", "", $str ); //匹配html中的空格
	return trim ( $str ); //返回字符串
}
/**
 * 字符串 XHTML 格式化显示
 * @param string $str
 * @return string
 */
function str_xhtml($str) {
	$str = nl2br ( str_replace ( " ", "&nbsp;", $str ) );
	return $str;
}

/**
 * @notes 随机生成token值
 * @param string $extra
 * @return string
 */
function create_token(string $extra = '') : string
{
    return md5($extra . time());
}
function get_col_width($field,$default=0){
	$path 	= request()->baseUrl();
	$width 	= cookie(md5($path.$field));
	return $width?$width:$default;
}

function num_format($num){
	if(!is_numeric($num)){
		return false;
	}
	$rvalue='';
	$num = explode('.',$num);//把整数和小数分开
	$rl = !isset($num['1']) ? '' : $num['1'];//小数部分的值
	$j = strlen($num[0]) % 3;//整数有多少位
	$sl = substr($num[0], 0, $j);//前面不满三位的数取出来
	$sr = substr($num[0], $j);//后面的满三位的数取出来
	$i = 0;
	while($i <= strlen($sr)){
		$rvalue = $rvalue.','.substr($sr, $i, 3);//三位三位取出再合并，按逗号隔开
		$i = $i + 3;
	}
	$rvalue = $sl.$rvalue;
	$rvalue = substr($rvalue,0,strlen($rvalue)-1);//去掉最后一个逗号
	$rvalue = explode(',',$rvalue);//分解成数组
	if($rvalue[0]==0){
		array_shift($rvalue);//如果第一个元素为0，删除第一个元素
	}
	$rv = $rvalue[0];//前面不满三位的数
	for($i = 1; $i < count($rvalue); $i++){
		$rv = $rv.','.$rvalue[$i];
	}
	if(!empty($rl)){
		$rvalue = $rv.'.'.$rl;//小数不为空，整数和小数合并
	}else{
		$rvalue = $rv;//小数为空，只有整数
	}
	return trim($rvalue,',');
}

//获取某个分类的所有子分类
function getSubs($categorys,$catId=0, $field1 = 'id', $field2 = 'pid',$level=1){
	$subs = array();
	foreach($categorys as $item){
		if($item[$field2 ]==$catId){
			$item['level']=$level;
			$subs[]	= $item;
			$subs 	= array_merge($subs,getSubs($categorys,$item[$field1],$field1,$field2,$level+1));
		}
	}
	return $subs;
}

function compressImage($sourcePath, $destinationPath, $quality) {
    $imageInfo = getimagesize($sourcePath);
	if ($imageInfo === false) {
		return ;
	}
    $imageType = $imageInfo[2];
    $image = '';
    if ($imageType == IMAGETYPE_JPEG) {
        $image = imagecreatefromjpeg($sourcePath);
        imagejpeg($image, $destinationPath, $quality);
    } elseif ($imageType == IMAGETYPE_PNG) {
        $image = imagecreatefrompng($sourcePath);
        imagepng($image, $destinationPath, 9 - round($quality * 0.08));
    } elseif ($imageType == IMAGETYPE_GIF) {
        $image = imagecreatefromgif($sourcePath);
        imagegif($image, $destinationPath);
    }
	if($image){
		imagedestroy($image);
	}
    
    
}



function check_auth($url=''){
	//超级管理员不需要验证
	if (Session::get('admin.id') == 1) return true;
	//验证权限
	$url		= $url?$url:($request->controller(true).'/'.$request->action(true));
	$url 		= strtolower($url);
	if(substr($url,0,6) == 'h5.erp'){
		$url 	= 'erp.'.substr($url,6);
	}
	$auth 		= array_column(Session::get('admin.menu'), 'auth');
	if (!in_array($url, $auth)) {
		return false;
	}
	return true;
}

