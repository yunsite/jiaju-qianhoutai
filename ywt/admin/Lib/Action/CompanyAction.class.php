<?php
require './login.inc.php';
require './init.inc.php';
class CompanyAction extends Action{
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		//公司所属行业
		$arr = array('电子、电器、电工','安全、监控器材','服装、鞋帽、皮具','手机、数码、电脑','食品、饮茶、茶叶、酒类','广告、设计','拍卖、典当','风景、旅游','IT\互联网','学校、教育','自定义');
		$gs = M('company');
		$list = checkTrip($gs->find());
		$this->assign(array(
			'arr'=>$arr,
			'list'=>$list
		));
		
		$this->display();
	}
	
	//添加数据
	public function addData(){
		$gs = M('company');
		$list = checkTrip($gs->select());
		
		if($gs->create()){
			if (empty($list)){
				if ($gs->add()){
					$this->success('保存成功');
				}else{
					$this->error('保存失败');
				}				
			}else{
				if ($gs->where('id=1')->save()){
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}	
			}
		}else{
			$this->error();
		}
	}

	//公司介绍
	public function companyDesc(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('company');
		$list =checkTrip($c->field('id,desc')->find());
		if (!empty($list)){
			$this->assign('list',$list);
		}
		$this->display();
	}
	//添加公司介绍信息
	public function addDesc(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('company');
		$list = checkTrip($c->find());
		if (empty($list)){
			echo '<script>alert("请先完成 \'公司的基本设置\' 进行此操作");location.href="./index.php"</script>';
			return;
		}
		if ($c->create()){
			if ($c->where('id=1')->save()){
				$this->success('保存成功');
			}else{
				$this->error('保存失败');
			}
		}else{
			$this->error('操作失败');
		}
	}
		
	//关于我们
	public function gywm(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('company');
		$list = checkTrip($c->field('id,gywm')->find());
		if (!empty($list)){
			$this->assign('list',$list);
		}
		$this->display();
	}
	//添加关于我们
	public function addGywm(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('company');
		$list = checkTrip($c->find());
		if (empty($list)){
			echo '<script>alert("请先完成 \'公司的基本设置\' 进行此操作");location.href="./index.php"</script>';
			return;
		}
		if ($c->create()){
			if ($c->where('id=1')->save()){
				$this->success('保存成功');
			}else{
				$this->error('保存失败');
			}
		}else{
			$this->error('操作失败');
		}
	}		
	
	
	//上传相片并保存图片名到数据库 之共用方法
	public function addPic(){
		header('Content-Type:text/html;charset=utf-8');

		$filename = $_POST['filename'];
		$field = $_POST['field'];
		
		//文件上传配置
		import('ORG.Net.uploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 1000000 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->allowTypes = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif');
		$upload->savePath =  './Public/Uploads/'.$filename.'/';// 设置附件上传目录
		$upload->saveRule = 'uniqid';	
		
		
		//缩略图配置	
		$upload->thumb = true;
		$upload->thumbMaxWidth = '500';
		$upload->thumbMaxHeight = '500';
		$upload->thumbPrefix = 's_';
		$upload->thumbRemoveOrigin = true;

		//开始上传
		if(!$upload->upload()){
			$this->error($upload->getErrorMsg());
		}else{
			$info =  $upload->getUploadFileInfo();
		}
		
		$c = M('company');

		$pics = '';
		$oldPic = checkTrip($c->order('id')->find());
		foreach($info as $val){
			$pics .= $val['savename'].'-';	
		}

		$newPics = $oldPic[$field].$pics;	//将原来的图片取出来,再加上临时添加的相片,实现追加效果
		
		$data[$field] = $newPics;

		if ($c->where('id=1')->save($data)){
			$this->success('更新成功');
		}else{
			$this->error('更新失败');
		}
	}
	
	//删除相片
	public function delPic(){
		header('Content-Type:text/html;charset=utf-8');
		$p = M('company');
		$picname = $_POST['picname'];	//要删除的图片名
		$field = $_POST['ziduan'];		//要删除哪个字段的图片(这个字段和存放的目录文件名相同，只是员工风采不同)

		$list = checkTrip($p->order('id')->find());
		$list[$field] = str_replace($picname.'-','',$list[$field]);

		$field = isset($_POST['dirname']) ? $_POST['dirname'] : $_POST['ziduan'];
		unlink('./Public/Uploads/'.$field.'/s_'.$picname);
		
		if ($p->where('id=1')->save($list)){
			echo '删除成功';
		}else{
			echo '删除失败';
		}
	}
	
	//公司相册、员工风采等共用方法
	private function PublicCode($field,$tmp){
		header('Content-Type:text/html;charset=utf-8');
		$gs = M('company');
		$list = checkTrip($gs->order('id')->find());
		if (empty($list)){
			echo '<script>alert("请先完成 \'公司的基本设置\' 再添加相册");location.href="./index.php"</script>';
			echo '请先完成 <font style="font-weight:900; color:blue"><A href="./index.php">公司的基本设置</a></font> 再添加相册';
		}else{
			$pics = $list[$field];
			$this->assign('pics',$pics);
			$this->display($tmp);		
		}		
	}
	
	//公司相册
	public function companyPic(){
		$this->PublicCode('gspic',__FUNCTION__);
	}
	
	//员工风采
	public function ygPic(){
		$this->PublicCode('ygpic',__FUNCTION__);
	}
	
	//公司荣誉
	public function honour(){
		$this->PublicCode('honour',__FUNCTION__);
	}
}
?>