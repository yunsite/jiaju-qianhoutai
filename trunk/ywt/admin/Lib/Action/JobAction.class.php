<?php
require './login.inc.php';
require './init.inc.php';
class JobAction extends Action{
	//招聘
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		$j = M('job');
		$list = checkTrip($j->order('add_time desc')->select());
		if (!empty($list)){
			$this->assign('list',$list);
		}
		//数据分页	
		import("ORG.Util.Page");// 导入分页类
		$count = $j->count();
		$page = new Page($count,6);
		$show = $page->show();
		$this->assign('page',$show);
				
		$this->display();
	}
	
	//添加数据
	public function addJob(){
		header('Content-Type:text/html;charset=utf-8');
		//判断用户是在个修改还是在新增
		if (isset($_GET['act']) && $_GET['act']=='edit'){
			$j = M('job');	
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;	
			if ($id==0)
				return '非法操作';		
			
			$list = checkTrip($j->where('id='.$id)->find());
			$list['act'] = 'edit';
			$this->assign('list',$list);
		}
	
		$this->display();
	}
	
	//接收数据并添加
	public function addData(){
		header('Content-Type:text/html;charset=utf-8');
		$j = M('job');
			
		$_POST['add_time'] = strtotime($_POST['add_time']);	//将日期格式化时间戳

		if (isset($_FILES['job_pics'])){
		//文件上传配置
		import('ORG.Net.uploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 1000000 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->allowTypes = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif');
		$upload->savePath =  './Public/Uploads/jobs/';// 设置附件上传目录
		$upload->saveRule = 'uniqid';	

		//开始上传
		if(!$upload->upload()){
			$this->error($upload->getErrorMsg());
		}else{
			$info =  $upload->getUploadFileInfo();
		}	
	
		$_POST['job_pics'] = $info[0]['savename'];
		
		//判断用户在添加还是在修改(在已选择文件域的情况下)
		if (isset($_POST['act']) && $_POST['act']=='edit'){	//修改
			//取出原图片名称,进行删除
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$list = checkTrip($j->where('id='.$id)->field('job_pics')->find());
			$oldPic = './Public/Uploads/jobs/'.$list['job_pics'];

			if ($j->where('id='.$id)->save($_POST)){
				$this->success('更新成功');
				unlink($oldPic);
			}else{
				$this->error('更新失败');
			}
		}else{											//添加	
			if ($j->create()){
				if ($j->add()){
					$this->success('保存成功');	
				}else{
					$this->error('保存失败');
				}
			}else{
				$this->error();
			}		
		}
	}else{									//没有选择LOGO的情况下修改文字
			$data = $_POST;
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			if ($j->where('id='.$id)->save($data)){
				$this->success('更新成功');
			}else{
				$this->error('更新失败');
			}
	}	
	}

	//删除记录(包含图片)
	public function del(){
		$j = M('job');
		$id = isset($_GET['id']) ? rtrim($_GET['id'],',') : 1;
		
		//批量删除图片\单张图片
		if (isset($_GET['act']) && $_GET['act']=='one'){	//代表单条删除，否则为批量删除
			unlink('./Public/Uploads/jobs/'.$_GET['pics']);
		}else{
			$arrpic = explode(',',rtrim($_GET['pics'],','));
			foreach ($arrpic as $pic){
				unlink('./Public/Uploads/jobs/'.$pic);
			}		
		}
		
		if ($j->where('id in ('.$id.')')->delete()){
			echo '<script>alert("删除成功");location.href="'.__URL__.'"</script>';
		}else{
			echo '<script>alert("删除失败");location.href="'.__URL__.'"</script>';
		}
	}
	
}