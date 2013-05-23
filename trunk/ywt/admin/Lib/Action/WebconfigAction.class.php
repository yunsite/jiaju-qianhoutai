<?php
require './login.inc.php';
require './init.inc.php';
/*网站配置控制器*/
class WebconfigAction extends Action{
	/*基本信息*/
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		//读配置
		$i = M('config');
		$list = checkTrip($i->find());
		
		//读用户信息
		$u = M('user');
		$user = checkTrip($u->find());

		//存储上一次登录时间,1，先把读取的变量分配过去,2，再更新,如果存在某个SESSION变量，不管怎样刷新我都只更新一次,这个SESSION是从IndexAction分配过来的
		$this->assign(array(
			'list'=>$list,
			'user'=>$user
		));
		//读取留言本信息
		$msg = M('msgs');
		//未读留言、所有留言、最后一条留信息
		$count = $msg->where('m_status=0')->count();
		$allmsg = $msg->count();
		$lastmsg = checkTrip($msg->order('m_id desc')->limit(1)->find());
		
		$this->assign(array(
			'count'=>$count,
			'allmsg'=>$allmsg,
			'lastmsg'=>$lastmsg
		));

		$this->display();
	}
		
	//修改或添加配置
	public function advanced(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('config');
		$list = checkTrip($c->find());
		$this->assign('list',$list);
		$this->display();		
	}
	
	
	//添加数据
	public function addData(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('config');
		if (isset($_FILES['weblogo'])){					//如果定义了它，说明用户想修改或添加LOGO
			//文件上传配置
			import('ORG.Net.uploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 1000000 ;// 设置附件上传大小
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			//$upload->allowTypes = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif');
			$upload->savePath =  './Public/Uploads/weblogo/';// 设置附件上传目录
			$upload->saveRule = 'time';	
			
			//缩略图配置	
		/*	$upload->thumb = true;
			$upload->thumbMaxWidth = '300';
			$upload->thumbMaxHeight = '100';
			$upload->thumbPrefix = 's_';
			$upload->thumbRemoveOrigin = true;
		*/
			//开始上传
			if(!$upload->upload()){
				$this->error($upload->getErrorMsg());
			}else{
				$info =  $upload->getUploadFileInfo();
			}
			
			//如果用户创建了weblobo，第一种可能是在新增数据，第二种，是在修改数据
			$c->create();
			$c->weblogo = $info[0]['savename'];
			$list = checkTrip($c->find());
			if (empty($list)){		//如果数据为空,则代表在添加数据
				if ($c->add()){
					$this->success('添加成功');	
				}else{
					$this->error('没有数据被影响');
				}
			}else{					//否则在更新数据
				$data = $_POST;
				$data['weblogo'] = $info[0]['savename'];
				unset($data['dredgetime']);					//不更新创建时间
				
				$list = $c->field('weblogo')->find();
				$oldPic = './Public/Uploads/weblogo/s_'.$list['weblogo'];
							
				if ($c->where('id=1')->save($data)){
					$this->success('更新成功');
					unlink($oldPic);
				}else{
					$this->error('没有数据被影响');
				}
			}
			
		}else{			//如果没有定义,说明资料已存在，只需更新文字
			$data = $_POST;
			unset($data['weblogo']);
			unset($data['dredgetime']);
			if ($c->where('id=1')->save($data)){
				$this->success('更新成功');
			}else{
				$this->error('没有数据被影响');
			}			
		}					
	}
	
	//客服QQ配置
	public function qqconfig(){
		header('Content-Type:text/html;charset=utf-8');
		$q = M('qq');
		$qqs = checkTrip($q->find());
		$this->assign('list',$qqs);
		$this->display();
		
	}
	
	//修改或添加号码
	public function editQq(){
		header('Content-Type:text/html;charset=utf-8');
		$q = M('qq');	
		$qqs = checkTrip($q->find());
		$q->create();
		
		if (empty($qqs)){
			if ($q->add()){
				$this->success('添加成功');	
			}else{
				$this->error('没有数据被影响');
			}
		}else{
			if ($q->where('id=1')->save()){
				$this->success('更新成功');
			}else{
				$this->success('没有数据被影响');
			}
		}
	}
	
	//网页背景管理
	public function background(){
		header('Content-Type:text/htmls;charset=utf-8');
		$b = M('bgpics');
		$list = $b->order('pic asc')->select();

		$this->assign('list',$list);
		
		$this->display();
	}
	
	public function addbg(){
		header('Conten-Type:text/html;charset=utf-8"');
		$b = M('bgpics');	
		if (isset($_GET['act']) && $_GET['act'] == 'edit'){	//指在修改
			$pid = $_GET['pid'] + 0;
			$list = $b->where('id='.$pid)->find();
			$this->assign('list',$list);
		}
		
		$this->display();		
		
	}
	
	//数据操作
	public function addBack(){
		header('Conten-Type:text/html;charset=utf-8"');
		$b = M('bgpics');
			
		if (isset($_POST['act']) && $_POST['act'] == 'edit'){		//修改记录
			$pid = $_POST['pid'] + 0;
			unset($_POST['pic']);
			
			if ($b->where('id='.$pid)->save($_POST)){
				$this->error('更新成功');
			}else{
				$this->error('没有数据被影响');
			}
		}else{
			import('ORG.Net.uploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 1000000 ;// 设置附件上传大小
			$upload->allowExts  = array('jpg', 'jpeg');// 设置附件上传类型
			$upload->allowTypes = array('image/jpg','image/jpeg','image/pjpeg');
			$upload->savePath =  './Public/images/';// 设置附件上传目录
			$upload->saveRule = null;				//不更新文件名
			$upload->uploadReplace = true;			//同名覆盖
			
			
			//开始上传
			if(!$upload->upload()){
				$this->error($upload->getErrorMsg());
			}else{
				$info =  $upload->getUploadFileInfo();			
			}
			
			$_POST['pic'] = $info[0]['savename'];
	
			if ($b->add($_POST)){
				$this->success('添加成功');
			}else{
				$this->error('没有数据被影响');
			}		
		}
	}
	
	public function delpic(){
		header('Conten-Type:text/html;charset=utf-8"');
		$b = M('bgpics');
		
		$pid = $_GET['pid'] + 0;
		$list = $b->where('id='.$pid)->find();
		$picname = './Public/images/'.$list['pic'];
		
		if ($b->where('id='.$pid)->delete()){
			echo '<script>location.href="'.__URL__.'/background"</script>';	
			if (file_exists($picname)){
				unlink($picname);
			}
		}else{
			echo '<script>location.href="'.__URL__.'"</script>';
		}
	}
}
?>