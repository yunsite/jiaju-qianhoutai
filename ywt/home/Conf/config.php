<?php
$db = include './config.inc.php';
$myConf = array(
	//调式期关闭缓存
	'TMPL_CACHE_ON' => false,
	//URL模式 PathInfo模式
	'URL_MODEL'=> 1,
	//伪静态
	'URL_HTML_SUFFIX' => 'shtml', 
 	//访问URL忽略大小写
    'URL_CASE_INSENSITIVE' =>true,
	
	//令牌配置
	'TOKEN_ON'=>true,  // 是否开启令牌验证
    'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true
);

return array_merge($db,$myConf);
?>