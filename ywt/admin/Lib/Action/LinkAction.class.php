<?php
require './login.inc.php';
require './init.inc.php';
class LinkAction extends Action{
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		load('extend');			// 导入扩展函数(对中文字符串的截取)
		import("ORG.Util.Page");// 导入分页类
		$l = M('link');
		$count = $l->count();
		$page = new Page($count,20);
		$show = $page->show();
			
		if (!isset($_GET['search'])){
			$list = $l->order('l_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		}else{
			//按留言标题、留言姓名
			$methodone = isset($_GET['search_bzb']) ?$_GET['search_bzb'] : '';
			$bzb = $_GET['bzb'];

			if (!empty($bzb)){
				$sql = $methodone.' like "%'.$bzb.'%"';
			}		
			
			//按添加时间排序
			$methodtwo = $_GET['search_tdb'];
			$px = $_GET['order'];
			$order = $methodtwo.' '.$px;
			
			
			//筛选
			if (isset($_GET['sh'])){
				if ($_GET['sh'] != 'status'){
					$ha = 'l_status = '.$_GET['sh']; 
				}		
			}
			//查找记录后,更新显示条数
			$count = $l->where($sql)->having($ha)->count();
			$page = new page($count,20);
			$show = $page->show();
			
			$list = $l->where($sql)->having($ha)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
			
			foreach($list as $key=>$val){
				 $val[$methodone] = str_replace($bzb,'<b>'.$bzb.'</b>',$val[$methodone]);
				 $list[$key][$methodone] = $val[$methodone];
			}
			

		}
		
		$time = time();
		foreach ($list as $key=>$val){		//判断过期链接
			if ($time >= $val['l_overtime'] ){
				$data['l_status'] = 2;
				$id = $val['id'];
				$l->where('id='.$id)->save($data);
			}
		}
		
		$this->assign(array(
			'list' => $list,
			'page' => $show
		));
		
		
		$this->display();
	}
	
	//添加链接
	public function addlink(){
		header('Content-Type:text/html;charset=utf-8');			
		
		//判断用户是在个修改还是在新增
		if (isset($_GET['act']) && $_GET['act']=='edit'){
			$l = M('link');	
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;	
			if ($id==0)
				return '请正确操作';		
			
			$list = checkTrip($l->where('id='.$id)->find());
			$list['act'] = 'edit';
			$this->assign('list',$list);
			
		}
	
		$this->display();
	}
	
	//保存数据
	public function addData(){
		header('Content-Type:text/html;charset=utf-8');
		$l = M('link');
			
		$_POST['l_overtime'] = strtotime($_POST['l_overtime']);	//将日期格式化时间戳
		if (isset($_FILES['l_logo'])){
		//文件上传配置
		import('ORG.Net.uploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 1000000 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->allowTypes = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif');
		$upload->savePath =  './Public/Uploads/links/';// 设置附件上传目录
		$upload->saveRule = 'uniqid';	
		
		//缩略图配置	
		$upload->thumb = true;
		$upload->thumbMaxWidth = '160';
		$upload->thumbMaxHeight = '60';
		$upload->thumbPrefix = 's_';
		$upload->thumbRemoveOrigin = true;

		//开始上传
		if(!$upload->upload()){
			$this->error($upload->getErrorMsg());
		}else{
			$info =  $upload->getUploadFileInfo();
		}	
		
		$_POST['l_logo'] = $info[0]['savename'];
				
		
		//判断用户在添加还是在修改(在已选择文件域的情况下)
		if (isset($_POST['act']) && $_POST['act']=='edit'){	//修改
			$data = $_POST;
			
			//取出原图片名称,进行删除
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$list = checkTrip($l->where('id='.$id)->field('l_logo')->find());
			$oldPic = './Public/Uploads/links/s_'.$list['l_logo'];
			
			unset($data['l_time']);
			
			if ($l->where('id='.$id)->save($data)){
				$this->success('更新成功');
				unlink($oldPic);
			}else{
				$this->error('更新失败');
			}
		}else{											//添加	
			if ($l->create()){
				if ($l->add()){
					$this->success('保存成功');	
				}else{
					$this->error('保存失败');
				}
			}else{
				$this->error();
			}		
		}
	}else{													//没有选择LOGO的情况下修改文字
			$data = $_POST;
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			unset($data['l_time']);
			unset($data['l_logo']);
			if ($l->where('id='.$id)->save($data)){
				$this->success('更新成功');
			}else{
				$this->error('更新失败');
			}
	}
		
	}
		
	//删除记录(包含图片)
	public function del(){
		$l = M('link');
		$id = isset($_GET['id']) ? rtrim($_GET['id'],',') : 1;
		
		//批量删除图片\单张图片
		if (isset($_GET['act']) && $_GET['act']=='one'){	//代表单条删除，否则为批量删除
			unlink('./Public/Uploads/links/s_'.$_GET['pics']);
		}else{
			$arrpic = explode(',',rtrim($_GET['pics'],','));
			foreach ($arrpic as $pic){
				unlink('./Public/Uploads/links/s_'.$pic);
			}		
		}
		
		if ($l->where('id in ('.$id.')')->delete()){
			echo '<script>alert("删除成功");location.href="'.__URL__.'"</script>';
		}else{
			echo '<script>alert("删除失败");location.href="'.__URL__.'"</script>';
		}
	}



	
	
	
	
	//测试AJAX图片主传
	public function add(){
		header('Content-Type:text/html;charset=utf-8');
		$this->display();
	}
}