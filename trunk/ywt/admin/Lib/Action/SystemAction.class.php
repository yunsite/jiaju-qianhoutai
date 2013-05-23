<?php
class SystemAction extends Action{
	//系统设置[修改密码]
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		load('extend');
		$this->display();
	}
	
	//验证码
	public function verify(){
		import('ORG.Util.Image');	
		Image::buildImageVerify();
	}
	
	//接收数据,检查合法性
	public function checkData(){
		header('Content-Type:text/html;charset=utf-8');
		if($_SESSION['verify'] != md5($_POST['code'])) {
			echo  '验证码错误';
			return;
		 }

		$n = 1;
		foreach ($_POST as $key=>$val){
			//$_POST[$key] = trim($_POST[$key]);
			if ($_POST[$key] == ''){
				echo '第'.$n.'项不能为空或不合法<br/>';
				$n++;
				return;
			}
		}
		
		$p = M('user');
		$list = $p->where('username="'.$_SESSION['uname'].'"')->find();
		
		if (($list['userpwd']) == (md5($_POST['oldpwd']))){
			$data['userpwd'] = md5($_POST['newpwd']);
			
			if ($p->where('username="'.$_SESSION['uname'].'"')->save($data)){
					echo '<script>alert("修改成功\n 将在下次重新登录时起效");location.href="'.__URL__.'"</script>';
				}else{
					echo '<script>alert("修改失败");location.href="'.__URL__.'"</script>';
			}
		}else{
			echo '<script>alert("旧密码错误");location.href="'.__URL__.'"</script>';
		}
			
	}
	
	//验证失败
	public function checkError(){
	
	}
	
	//验证成功
	public function checkSuccess(){
	
	}
	
}
?>