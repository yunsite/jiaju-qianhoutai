<?php
header('Content-Type:text/html;charset=utf-8');

function sendm($phoneMail=''){
	date_default_timezone_set("Asia/Shanghai");
	require("./mail/PHPMailer/class.phpmailer.php");
	$mail = new PHPMailer();
	$address = $phoneMail;
	$mail->IsSMTP();
	$mail->CharSet='UTF-8';
	$mail->Host = "smtp.qq.com";
	$mail->SMTPAuth = true; 
	$mail->Username = "1874995829@qq.com"; 
	$mail->Password = "811819aa"; 			//QQ密码或独立密码
	$mail->From = "1874995829@qq.com";
	$mail->FromName = "ThinkPHP使用者";				//客户名字
	$mail->AddAddress("$address", "小严");//收件人址址,姓名") 　　
	$mail->Subject = "您使用ThinkPHP开发的产品又有新用户使用啦"; //邮件标题
	$mail->Body = "您使用ThinkPHP开发的产品又有新用户使用啦,IP:".$_SERVER['REMOTE_ADDR']; //邮件内容
	$mail->Send();
}

?>