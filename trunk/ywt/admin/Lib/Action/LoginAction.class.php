<?php
class LoginAction extends Action{
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		$this->display();
	}
	
	//验证码
	public function verify(){
		import('ORG.Util.Image');	
		Image::buildImageVerify();
	}
	
	//验证登录
	public function check(){
		header('Content-Type:text/html;charset=utf-8');
		/* if (!isset($_POST['isopenjs'])){
			echo '<p style="line-height:20px; font-size:12px; text-align:center"><img src="../../Public/images/formimg/onFocus.gif" style="vertical-align:middle"/>系统检测到您关闭了JS, 请开启! 体验更人性化的效果!</p>';
		}
		if ($_SESSION['verify'] != md5($_POST['yzm'])){
			echo '验证码错误';
			return;
		} */	
			
		$user = M('user');
		$uname = $_POST['uname'];
		$upwd = md5($_POST['upwd']);
		$list = $user->field(array('userpwd','username','name','loginnums'))->where('username="'.$uname.'"')->find();	

		if ($list['userpwd'] != $upwd){
			echo '用户名或密码错误';
			return;
		}else{
			session_start();
			$_SESSION['uname'] = $list['username'];
			$_SESSION['name'] = $list['name'];
			echo '正在登录..';		
			
		}			
		
		/* if (!isset($_POST['isopenjs'])){
			header('location:../../admin.php');
		} */		
		
		
	}
	
	//退出登录
	public function outlogin(){
		header('Content-Type:text/html;charset=utf-8');
		session_start();
		$_SESSION = array();
		if (isset($_COOKIE[session_name()])){
			setCookie(session_name(),'',time()-100);
		}
		session_destroy();
		echo '<script>alert("退出成功");location.href="../../admin.php/login"</script>';
		echo '成功退出';
		
	}

	
}
?>