<?php
require './init.inc.php';
class IndexAction extends Action {
  public function index(){
		header('Content-Type:text/html;charset=utf-8');		
		session_start();
		if (!isset($_SESSION['name'])){
			$path = explode('/',__URL__,-1);
			$path = join('/',$path);
			header('Location:'.$path.'/login');
		}else{
		//如果登录成功，则更新登录次数及更新时间
			$user = M('user');
			$list = checkTrip($user->field(array('loginnums','logintime'))->find());
			if (!isset($_SESSION['isloginnums'])){
				$data['loginnums'] = $list['loginnums'] + 1;
				$data['logintime'] = time();
				$user->where('id=1')->save($data);			
			}
			$_SESSION['isloginnums'] = true;
			
			$this->display();
		}
		
    }
	
	
}
?>