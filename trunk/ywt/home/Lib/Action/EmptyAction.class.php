<?php
class EmptyAction extends Action{
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		echo MODULE_NAME.'模块不存在';
	}
}
?>