<?php
require 'init.inc.php';
$c = M('config');	//网站配置
$l = M('cate');		//网站栏目
$a = M('link');		//友情链接
$gs = M('Company');	//公司配置
$q = M('qq');		//客服QQ

//公共查询
$config = checkTrip($c->find());
$cate = checkTrip($l->order('px asc')->select());
$link = checkTrip($a->where('l_status=1')->select());
$company = checkTrip($gs->find());
$qq = checkTrip($q->find());

//分配变量
$this->assign(array(
	'config' => $config,
	'cate' => $cate,
	'link' => $link,
	'company' => $company,
	'qq' => $qq
));	