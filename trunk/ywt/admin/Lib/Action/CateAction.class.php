<?php
require './login.inc.php';
require './init.inc.php';
class CateAction extends Action{
	//首页栏目
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('cate');
		$list = checkTrip($c->field('id,name,lmlogo,pid,path,px,url,target,concat(path,"-",id) as bpath')->order('bpath asc')->select());
		$this->assign('list',$list);
		$this->display();
	}
	
	//添加根栏目[或修改根栏目]
	public function addCate(){
		header('Content-Type:text/html;charset=utf-8');
		$id = $_GET['id'] + 0;
		$c = M('cate');
		$list = checkTrip($c->where('id='.$id)->find());
		$this->assign('list',$list);
		$this->display();
	}
	
	//接收添加栏目的数据
	public function addData(){
		header('Content-Type:text/html;charset=utf-8');
		$_GET['pid'] = $_POST['id'];
		$c = D('cate');
		
		//修改与增加根栏目
		if (isset($_FILES['lmlogo'])){
			//文件上传配置
			import('ORG.Net.uploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 1000000 ;// 设置附件上传大小
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->allowTypes = array('image/jpg','image/jpeg','image/pjpeg','image/x-png','image/png','image/gif');
			$upload->savePath =  './Public/Uploads/lmlogo/';// 设置附件上传目录
			$upload->saveRule = 'uniqid';	
	
			//开始上传
			if(!$upload->upload()){
				$this->error($upload->getErrorMsg());
			}else{
				$info =  $upload->getUploadFileInfo();
			}	
			
			$_POST['lmlogo'] = $info[0]['savename'];	
			


			//判断用户在添加还是在修改(在已选择文件域的情况下)
			if (isset($_POST['act']) && $_POST['act']=='edit'){	//修改
				$data = $_POST;
				
				//取出原图片名称,进行删除
				$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
				$list = checkTrip($c->where('id='.$id)->field('lmlogo')->find());
				$oldPic = './Public/Uploads/lmlogo/'.$list['lmlogo'];
			
				if ($c->where('id='.$id)->save($data)){
					$this->success('更新成功');
					unlink($oldPic);
				}else{
					$this->error('没有记录被影响');
				}
			}else{											//添加
				if ($c->create()){
					if ($c->add()){
						$this->success('保存成功');	
					}else{
						$this->error('没有记录被影响');
					}
				}else{
					$this->error();
				}
				exit;
			}
				
		}else{
			$data = $_POST;
			if (isset($_POST['act']) && $_POST['act']=='edit'){	//没有图片时的修改
				$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
				if ($c->where('id='.$id)->save($data)){
					$this->success('更新成功');
				}else{
					$this->error('没有更新记录被影响');
				}				
			}else{
				$_GET['pid'] = $_POST['id'];							//添加子栏目
				$_POST['pid'] = $_POST['id'];			
				$d = $c->create();
				unset($d['id']);
				
				if ($c->add($d)){
					$this->success('添加子栏目成功');	
				}else{
					$this->error('添加子栏目失败');
				}
				
			}
			exit;
		}
		
		//二级栏目与一级栏目,二级不允许上传图片
		if (!isset($_POST['id'])){		//表示添加子栏目
			//文件上传配置
			import('ORG.Net.uploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 1000000 ;// 设置附件上传大小
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->allowTypes = array('image/jpg','image/jpeg','image/pjpeg','image/x-png','image/png','image/gif');
			$upload->savePath =  './Public/Uploads/lmlogo/';// 设置附件上传目录
			$upload->saveRule = 'uniqid';	
	
			//开始上传
			if(!$upload->upload()){
				$this->error($upload->getErrorMsg());
			}else{
				$info =  $upload->getUploadFileInfo();
			}	
			
			$_POST['lmlogo'] = $info[0]['savename'];			
			
		}else{
				//....
		}

	}
	
	//添加子栏目
	public function addzcate(){
		header('Content-Type:text/html;charset=utf-8');
		$pid = isset($_GET['pid']) ? $_GET['pid'] : 1;
		$c = M('cate');
		$list = checkTrip($c->where('id='.$pid)->find());
		$this->assign(array(
			'list' => $list,
		));
		if (isset($_GET['ppid'])){
			$list2 = checkTrip($c->field('name')->where('id='.$_GET['ppid'])->find());
			$list['pname'] = $list2['name'];
			$this->assign('class',$list);
		}
		$this->display();
	}
	
	//修改栏目[根据传递过来的class判断是修改根栏目还是子栏目,从页跳转到不同的处理页面]
	public function editlm(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('cate');

		$id = $_GET['id'] + 0;
		$class = $_GET['class'] + 0;
		
		//要据自己的ID查询父ID(真实的父ID)
		$ppid = $c->field('pid')->where('id='.$id)->find();

		if ($class >= 2 ){
			echo '<script>location.href="'.__URL__.'/'.addzcate.'?pid='.$id.'&ppid='.$ppid['pid'].'";</script>';
		}else{
			echo '<script>location.href="'.__URL__.'/'.addCate.'?id='.$id.'";</script>';
		}
	}
	
	
	//删除栏目
	public function delCate(){
		header('Content-Type:text/html;charset=utf-8');
		$id = $_GET['id'] + 0;
		if (!$id){
			return '请按正常流程操作';
		}

		$c = M('cate');
		
		$list = checkTrip($c->where('id='.$id)->field('lmlogo')->find());
		$oldPic = './Public/Uploads/lmlogo/'.$list['lmlogo'];
		
		if ($c->where('id='.$id)->delete()){
			echo '<script>alert("删除成功");location.href="'.__URL__.'"</script>';
			unlink($oldPic);
		}else{
			echo '<script>alert("删除失败");location.href="'+__URL__+'"</script>';
		}
	}
	
	
	
}
?>