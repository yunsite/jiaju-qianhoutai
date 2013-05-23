<?php
class JobAction extends Action{
	//公司介绍
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		require './home/Lib/Action/Public.php';
		$j = M('job');		//招聘
		
		import("ORG.Util.Page");// 导入分页类

		$count = $j->count();
		$page = new Page($count,6);
		$show = $page->show();

		$job = checkTrip($j->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select());
	
		$this->assign(array(
			'job' => $job,
			'page' => $show
		));		
		
		$this->display();
	}
}
?>