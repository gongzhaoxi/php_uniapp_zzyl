<?php
namespace app\common\util;
use app\common\model\AdminPhoto;

/**
 * 表单组件生成类
 */
class Form{

    /**
     * 下拉列表组件
     *
     * @param  string $name
     * @param  array $list
	 * @param  array $value_display
     * @param  mixed $selected
     * @param  array $options
     * @return string
     */
    public function select($name, $list = [],$value_display = [], $selected = null, $options = []){
		$options['name']= $name;
		$html 			= [];
		$data 			= $this->formatList($list,$value_display,$selected);
		foreach($data as $vo){
			$html[] 	= '<option value="'.$vo['value'].'" '.(($vo['status'])?'selected':'').' >' . $vo['display'] . '</option>';
		}
		return '<select '.$this->attributes($options).' >'.implode('', $html).'</select>';   
    }
	
    /**
     * 生成下拉列表组件(多选)
     *
     * @param  string $name
     * @param  array $list
	 * @param  array $value_display
     * @param  mixed $selected
     * @param  array $options
     * @return string
     */
    public function selects($name, $list = [],$value_display = [], $selected = null, $options = []){
		$options['xm-select']= $name;
		return $this->select($name, $list ,$value_display, $selected , $options);
    }
	
    /**
     * 单选框组件
     *
     * @param  string $name
     * @param  array $list
	 * @param  array $value_display
     * @param  mixed $checked
     * @param  array $options
     * @return string
     */
    public function radio($name, $list = [],$value_display = [], $checked = null, $options = []){
		$html 			= [];
		$options['name']= $name;
		$data 			= $this->formatList($list,$value_display,$checked);
		foreach($data as $vo){
			$html[] 	= '<input '.$this->attributes($options).' type="radio"  value="'.$vo['value'].'" title="'.$vo['display'].'" '.(($vo['status'])?'checked':'').'>';	
		}
		return implode('', $html);   
    }	
    /**
     * 复选框组件
     *
     * @param  string $name
     * @param  array $list
	 * @param  array $value_display
     * @param  mixed $checked
     * @param  array $options
     * @return string
     */
    public function checkbox($name, $list = [],$value_display = [], $checked = null, $options = []){
		$html 			= [];
		$options['name']= $name.'[]';
		$data 			= $this->formatList($list,$value_display,$checked);
		foreach($data as $vo){
			$html[] 	= '<input '.$this->attributes($options).' type="checkbox"  value="'.$vo['value'].'" title="'.$vo['display'].'" '.(($vo['status'])?'checked':'').'>';	
		}
		return implode('', $html);   
    }
	
	/**
	 * 创建图片上传组件
	 * @param        $name 
	 * @param        $value
	 * @return mixed
	 */
	public function image($name,$value='',$options = [],$url = ''){
		$config		= config('web');
		$url		= $url?$url:(string)url('index/upload');
		$data 		= "url:'".$url."', accept:'images',data:{path:'image'}";
		if(!empty($config['upload_size']) && $config['upload_size'] > 0){
			$data 	.= ',size:'.$config['upload_size'];
		}
		if(!empty($config['upload_ext'])){
			$data	.= ",exts:'".str_replace(',','|',$config['upload_ext'])."'";
		}
		$options['name']	= $name;
		$options['id']		= $name;
		$options['value']	= $value;
		
		$browse_url			= get_browse_url($value);
		$html 				= '';
		$html 				.= '<input type="text" '.$this->attributes($options).' class="layui-input layui-input-inline" >';
		$html 				.= '<button type="button" lay-data="{'.$data.'}"  class="layui-btn uload_image" >上传图片</button>';
		$html 				.= '<a onclick="window.top.layerPhotos(this)" class="lightbox-a"  data-href="'.$browse_url.'" href="javascript:;"><button type="button" class="layui-btn layui-btn-primary">预览图片</button></a>';
		
		return $html;
	}
	
	/**
	 * 创建文件上传组件
	 * @param        $name 
	 * @param        $value
	 * @return mixed
	 */
	public function file($name,$value='',$ext='',$options = [],$url = ''){
		$config		= config('web');
		$url		= $url?$url:(string)url('index/upload');
		$data 		= "url:'".$url."', accept:'file',data:{path:'file'}";
		if(!empty($config['upload_size']) && $config['upload_size'] > 0){
			$data 	.= ',size:'.$config['upload_size'];
		}
		$ext		= $ext?$ext:$config['upload_ext'];
		if(!empty($ext)){
			$data	.= ",exts:'".str_replace(',','|',$ext)."'";
		}
		$options['name']	= $name;
		$options['id']		= $name;
		$options['value']	= $value;
		$options['placeholder']	= !empty($options['placeholder'])?$options['placeholder']:('文件格式：'.$ext);
		
		$browse_url			= get_browse_url($value);
		$html 				= '';
		$html 				.= '<input type="text" '.$this->attributes($options).' class="layui-input layui-input-inline" >';
		$html 				.= '<button type="button" lay-data="{'.$data.'}"  class="layui-btn uload_file" >上传文件</button>';
		$html 				.= '<a  class="file_a" target="_blank" href="'.($browse_url?$browse_url:'javascript:;').'" ><button type="button" class="layui-btn layui-btn-primary">下载文件</button></a>';
		return $html;
	}	
	
	/**
	 * 创建相册上传组件
	 * @param        $name 
	 * @param        $value
	 * @return mixed
	 */
	public function photos($name,$value='',$url = ''){
		$config		= config('web');
		$url		= $url?$url:(string)url('index/upload');
		$data 		= "url:'".$url."', accept:'images',data:{path:'image'}";
		if(!empty($config['upload_size']) && $config['upload_size'] > 0){
			$data 	.= ',size:'.$config['upload_size'];
		}
		if(!empty($config['upload_ext'])){
			$data	.= ",exts:'".str_replace(',','|',$config['upload_ext'])."'";
		}
		
		$html 		= '';
		$html 		.= '<button type="button" data-name="'.$name.'" lay-data="{'.$data.'}"  class="layui-btn uload_photos" >相册图集</button><div class="photos"><ul class="priview">';
		$data 		= $value?(is_array($value)?$value:explode(',',$value)):[];
		foreach($data as $val){
			$browse_url	= get_browse_url($val);
			$html 		.= '<li class="item_img"><div class="operate"><i class="toleft layui-icon layui-icon-left"></i><i class="toright layui-icon layui-icon-right"></i><i class="close layui-icon layui-icon-close"></i></div><img onclick="window.top.layerPhotos(this)" src="'.$browse_url.'" data-href="'.$browse_url.'" class="img" ><input type="hidden" name="'.$name.'[]" value="'.$val.'" /></li>';
		}
		$html 		.= '</ul></div>';
		return $html;
	}	
	
	
	/**
	 * 创建图文上传组件
	 * @param        $name 
	 * @param        $value
	 * @return mixed
	 */
	public function album($name,$value='',$url = ''){
		$config		= config('web');
		$url		= $url?$url:(string)url('index/upload');
		$data 		= "url:'".$url."', accept:'images',data:{path:'image'}";
		if(!empty($config['upload_size']) && $config['upload_size'] > 0){
			$data 	.= ',size:'.$config['upload_size'];
		}
		if(!empty($config['upload_ext'])){
			$data	.= ",exts:'".str_replace(',','|',$config['upload_ext'])."'";
		}
		
		$html 		= '';
		$table 		= '<table class="layui-table"><thead><tr><th>图片</th><th>标题</th><th>操作</th></tr></thead><tbody class="album_tbody">';
		$value_data = $value?(is_array($value)?$value:unserialize($value)):[];
		if($value_data){
			foreach($value_data['photo'] as $key=>$val){
				$browse_url	= get_browse_url($val);
				$table .= '<tr><td><img onclick="window.top.layerPhotos(\''.$browse_url.'\')" src="'.$browse_url.'" ><input type="hidden" name="'.$name.'[photo][]" value="'.$val.'" /></td><td><input type="text" name="'.$name.'[name][]" value="'.$value_data['name'][$key].'" class="layui-input"></td><td><button type="button" class="layui-btn layui-btn-xs album-up">上移</button><button type="button" class="layui-btn layui-btn-xs album-down">下移</button><button type="button" class="layui-btn layui-btn-xs layui-btn-danger album-delete">删除</button></td></tr>';
			}
		}
		$table		.= '</tbody></table>';
		$html 		= '<button type="button"  data-name="'.$name.'" class="layui-btn uload_album" lay-data="{'.$data.'}" >多图文</button><div class="layui-upload-list">'.$table.'</div>';		
		return $html;
	}	
	
	
	/**
	 * 创建多文件上传组件
	 * @param        $name 
	 * @param        $value
	 * @return mixed
	 */
	public function files($name,$value='',$ext='',$url = ''){
		$config		= config('web');
		$url		= $url?$url:(string)url('index/upload');
		$data 		= "url:'".$url."', accept:'file',data:{path:'file'}";
		if(!empty($config['upload_size']) && $config['upload_size'] > 0){
			$data 	.= ',size:'.$config['upload_size'];
		}
		$ext		= $ext?$ext:$config['upload_ext'];
		if(!empty($ext)){
			$data	.= ",exts:'".str_replace(',','|',$ext)."'";
		}
		
		$html 		= '';
		$table 		= '<table class="layui-table"><thead><tr><th>名称</th><th>操作</th></tr></thead><tbody class="files_tbody">';
		$value_data = $value?(is_array($value)?$value:json_decode($value,true)):[];
		if($value_data){
			foreach($value_data['file'] as $key=>$val){
				$browse_url	= get_browse_url($val);
				$table .= '<tr><td><input type="hidden" name="'.$name.'[file][]" value="'.$val.'" /><input type="text" name="'.$name.'[name][]" value="'.$value_data['name'][$key].'" class="layui-input"></td><td><button type="button" class="layui-btn layui-btn-xs files-up">上移</button><button type="button" class="layui-btn layui-btn-xs files-down">下移</button><button type="button" class="layui-btn layui-btn-xs layui-btn-danger files-delete">删除</button><a target="_blank" href="'.$browse_url.'" class="layui-btn layui-btn-xs layui-btn-normal">下载</a></td></tr>';
			}
		}
		$table		.= '</tbody></table>';
		$html 		= '<button id="files_btn_'.$name.'" type="button"   data-name="'.$name.'" class="layui-btn uload_files" lay-data="{'.$data.'}" >多文件</button><div class="layui-upload-list">'.$table.'</div>';		
		return $html;
	}	
	

	public function attachment($name,$value='',$title='上传文件',$download_url=''){
		$url 			= (string)url('admin/index/upload');
		$data 			= "url:'".$url."', accept:'file'";
		$html 			= '';
		$html 			.= '<button type="button" data-name="'.$name.'" lay-data="{'.$data.'}"  class="pear-btn pear-btn-primary uload_attachment" >'.$title.'</button><input  name="'.$name.'" type="hidden" value="'.$value.'" />';
		if($value && is_numeric($value)){
			$file		= AdminPhoto::where('id',$value)->find();
			$html		.= '<a href="'.$download_url.'&file_id='.$file['id'].'" target="_blank">点击下载</a>';
			$html		.= '<p class="upload_tip">最后上传日期：'.$file['create_date'].'  上传人：'.$file['admin'].'</p>';
		}
		$html 			.= '';
		return $html;
	}	
	
	
	/**
	 * tinymce富文本编辑器组件
	 * @param        $name 
	 * @param        $value
	 * @return mixed
	 */
	public function tinymce($name = '',$value='',$url = ''){
		$url		= $url?$url:(string)url('index/upload');
		$html 		= '<textarea style="padding:0;width:100%;" data-url="'.$url.'" class="tinymce_editor" name="'.$name.'" id="'.$name.'"  >'.$value.'</textarea>';
		return $html;
	}


	/**
	 * 无限级联选择器组件
	 * @param        $type 1 Ajax传参模式2直接赋值模式 
	 * @param        $data $type为1时时数据数组为2时是异步获取数据的url
	 * @param        $name 
	 * @param        $value
	 * @return mixed
	 */	
	public function cascader($type,$data,$name,$value='', $options = [],$prop=[],$clicklast=1,$separate=','){
		$ele_name			= str_replace(['[',']'],['',''],$name);
		$data				= is_array($data)?json_encode($data):$data;
		$value				= is_array($value)?implode($separate,$value):$value;
		$prop['id']			= empty($prop['id'])?'id':$prop['id'];
		$prop['name']		= empty($prop['name'])?'name':$prop['name'];
		$prop['children']	= empty($prop['children'])?'children':$prop['children'];
		$html 				= '<cascader id="cascader_'.$ele_name.'" data-type="'.$type.'" data-clicklast="'.$clicklast.'" data-separate="'.$separate.'" data-name="'.$ele_name.'" data-prop="'.implode(',',$prop).'"  data-value="'.$value.'" data-class="cascader" '.$this->attributes($options).' ></cascader>';
		$html 				.= '<input id="'.$ele_name.'" name="'.$name.'" value="'.$value.'" type="hidden" />';
		$html				.= '<div id="cascader_'.$ele_name.'_data" class="cascader_data" style="display:none;">'.$data.'</div>';
		return $html;
	}
	
    /**
     * tag标签
	 * @param        $name 
	 * @param        $value
     * @return string
     */
    public function tag($name, $value = '',$options = []){
		$html 		= '<div class="layui-btn-container tag"  lay-filter="'.$name.'" data-name="'.$name.'" id="'.$name.'" lay-allowclose="true" lay-newTag="true">';
		$value		= $value?(is_array($value)?$value:explode(',',$value)):[];
		foreach($value as $k=>$vo){
			$html	.= '<button lay-id="'.$k.'" type="button" class="tag-item tag-item-normal layui-btn layui-btn-primary layui-btn-sm">'.$vo.'</button>';
		}
		$html		.= '<input class="tag-value" type="hidden" value="'.implode(',',$value).'"  name="'.$name.'" /></div>';
		return $html; 
    }
	
	
    //图库选择
    function optPhoto($name, $value = '',$type='radio',$url=''){
		$url 	= $url?$url:((string)url('index/optPhoto').'?file_type=image&type='.$type);
		$html 	= '';
		$html 	.= '<button type="button" data-name="'.$name.'" data-type="'.$type.'" data-url="'.$url.'" class="pear-btn pear-btn-primary  opt_photo" >图库选择</button><div class="photos"><ul class="priview">';
		if($type == 'radio'){
			$browse_url	= get_browse_url($value);
			$html 		.= '<li class="item_img"><img onclick="window.top.layerPhotos(this)" src="'.$browse_url.'" data-href="'.$browse_url.'" class="img" ><input type="hidden" name="'.$name.'" value="'.$value.'" /></li>';
		}else{
			$data 		= $value?(is_array($value)?$value:explode(',',$value)):[];
			foreach($data as $val){
				$browse_url	= get_browse_url($val);
				$html 	.= '<li class="item_img"><div class="operate"><i class="toleft layui-icon layui-icon-left"></i><i class="toright layui-icon layui-icon-right"></i><i class="close layui-icon layui-icon-close"></i></div><img onclick="window.top.layerPhotos(this)" src="'.$browse_url.'" data-href="'.$browse_url.'" class="img" ><input type="hidden" name="'.$name.'[]" value="'.$val.'" /></li>';
			}
		}

		$html 		.= '</ul></div>';
		return $html;
    }	
	
	
    /**
     * 格式化数据
     *
     * @param  array $list
	 * @param  array $value_display
     * @param  mixed $selected
     * @return array
     */	
	protected function formatList($list = [],$value_display = [], $selected = null){
		$data 		= [];
		if(count($list) == count($list, 1)) {
			//一维数组
			$value 		= empty($value_display['value'])?'__value__':$value_display['value'];//默认键值$k
			$display	= empty($value_display['display'])?'__display__':$value_display['display'];//默认$v
			foreach($list as $k=>$v){
				$val 	= $value=='__value__'?$k:$v;
				$data[] = ['value'=>$val,'display'=>($display=='__display__'?$v:$k),'status'=>((is_array($selected) && in_array($val,$selected)) || (!is_array($selected) && $val == $selected)?1:0)];
			}
		}else{
			$value 		= empty($value_display['value'])?'__value__':$value_display['value'];//默认键值$k
			$display	= empty($value_display['display'])?'name':$value_display['display'];//默认name属性
			foreach($list as $k=>$v){
				$val 	= ($value == '__value__' || !isset($v[$value]))?$k:$v[$value];
				$data[] = ['value'=>$val,'status'=>((is_array($selected) && in_array($val,$selected)) || (!is_array($selected) && $val == $selected)?1:0),'display'=>(($display=='__value__'|| !isset($v[$display])?$k:$v[$display]))];
			}
		}
		return $data;
	}	
	
	/**
     * 数组转换成一个HTML属性字符串。
     *
     * @param  array $attributes
     * @return string
     */
    public function attributes($attributes){
        $html = [];
        // 假设我们的keys 和 value 是相同的,
        // 拿HTML“required”属性来说,假设是['required']数组,
        // 会已 required="required" 拼接起来,而不是用数字keys去拼接
        foreach ((array)$attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);
            if (!is_null($element))
                $html[] = $element;
        }
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }
	
	/**
     * 拼接成一个属性。
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    protected function attributeElement($key, $value){
        if (is_numeric($key))
            $key = $value;
        if (!is_null($value)) {
            if (is_array($value) || stripos($value, '"') !== false) {
                $value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
                return $key . "='" . $value . "'";
            } else {
                return $key . '="' . $value . '"';
            }
        }
    }
   
}
