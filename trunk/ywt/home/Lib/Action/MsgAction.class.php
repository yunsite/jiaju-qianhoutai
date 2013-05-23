<?php
class MsgAction extends Action{
	/*用户留言*/
	public function index(){
		require './home/Lib/Action/Public.php';
		$this->display();
	}
	
	//验证码
	public function verify(){
		import('ORG.Util.Image');	
		Image::buildImageVerify();
	}
	
	//添加数据
	public function addData(){
		header('Content-Type:text/html;charset=utf-8');
		session_start();
		if ($_SESSION['verify'] != md5($_POST['yzm'])){
			echo '验证码不正确&nbsp;&nbsp;&nbsp;<b><a href="javascript:history.back()">返回上一步</a></b>';
			return;
		}else{
			$m = M('msgs');
			$_POST['m_ip'] = $_SERVER['REMOTE_ADDR'];	//客户端IP
			$_POST['m_time'] = time();					//添加时间
			$_POST['m_status'] = 0;						//状态

			foreach ($_POST as $k=>$v){
				$_POST[$k] = htmlspecialchars($v);
			}
			if ($m->create()){
				if ($m->add()){
					echo '<script>alert("留言成功,我们会尽快与您联系");location.href="'.__APP__.'"</script>';
				}else{
					echo '<script>alert("留言失败");location.href="'.__APP__.'"</script>';
				}
			}else{
				echo '<script>alert("留言失败");location.href="'.__APP__.'"</script>';
			}			
		}

	}
	

}
?>