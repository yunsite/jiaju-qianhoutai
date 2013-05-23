<?php
class CompanyAction extends Action{
	//公司介绍
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		require './home/Lib/Action/Public.php';

		$this->display();
	}

}

?>