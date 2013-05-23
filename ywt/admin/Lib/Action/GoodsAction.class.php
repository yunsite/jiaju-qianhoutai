<?php
require './login.inc.php';
require './init.inc.php';
class GoodsAction extends Action{
protected $baseroot = __ROOT__; //设置网站根目录
protected $uploadroot = 'Public/Uploads/upimg/'; //设置网站上传目录
protected $notetxt = 'public/files.txt';  //记录上传文件的URL地址
protected $edittxt = 'public/edit.txt'; //修改文件时用来存放图片路径
	//商品列表
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		
		/*分页及扩展配置*/
		load('extend');			// 导入扩展函数(对中文字符串的截取)
		import("ORG.Util.Page");// 导入分页类
		
		$g = M('goods');	
		//栏目
		$c = M('goodsClass');
		$cate = checkTrip($c->field('id,name,pid,path,concat(path,"-",id) as bpath')->order('bpath')->select());
		
		$count = $g->count();
		$page = new Page($count,12);
		$show = $page->show();
		
		//条件筛选
		if (!isset($_GET['search'])){
			$list = checkTrip($g->order('goods_addtime desc')->limit($page->firstRow.','.$page->listRows)->select());
		}else{
			$sql = '';
			$get = $_GET;
		
			//栏目的筛选
			if (isset($_GET['search_class'])){
				$class = $_GET['search_class'];
				if ($class !=''){
					$sql .= 'goods_class = "'.$class.'" ';
				}
			}
			//打折与未打折的筛选
			if (isset($_GET['search_bzb'])){
				$val = $_GET['search_bzb'];
				if ($val!='all'){
					$and = empty($sql) ? '' : 'and';
					$sql .= ($val==10) ? $and.' goods_dz > 1'  : $and.' goods_dz = '.$val;					
				}
			}
			//筛选商品名称、货号
			if ($_GET['key'] != ''){
				$v = $_GET['key'];
				$field = $_GET['namenum'];
				$ha = $field.' like "%'.$v.'%"';
			}
			//排序
			if (isset($_GET['order'])){
				$ziduan = $_GET['order'];
				$px = $_GET['px'];
				$order = $ziduan.' '.$px;
			}

			$sql = !($sql) ? 1 : $sql;
			$ha = !($ha) ? 1 : $ha;
			
			$c = count($g->where($sql)->having($ha)->order($order)->select());
			$page = new Page($c,12);
					
			foreach ($get as $k2=>$v2){
				if ($get[$k2] === '') unset($get[$k2]);
			}

			foreach ($get as $k3=>$v3){
				$page->parameter .= "$k3=".urlencode($v3)."&";
			}
			$list = checkTrip($g->where($sql)->having($ha)->order($order)->limit($page->firstRow.','.$page->listRows)->select());
			$show = $page->show();
			
			//关键字描红
			foreach($list as $key=>$val){
				 $val[$field] = str_replace($v,'<b>'.$v.'</b>',$val[$field]);
				 $list[$key][$field] = $val[$field];
			}
		}

		$this->assign(array(
			'list' => $list,
			'page' => $show,
			'cate' => $cate
		));
		$this->display();	
	}
	
	//删除商品图片
	public function delpic(){
		header('Content-Type:text/html;charset=utf-8');
		$g = M('goods');
		$id = intval($_POST['id']);
		$picname = $_POST['picname'];
		$list = $g->where('id='.$id)->find();
		$list['goods_pics'] = str_replace($picname.'-','',$list['goods_pics']);
		
		if ($g->where('id='.$id)->save($list)){
			echo '已删除';
			unlink('./Public/Uploads/goods/s_'.$picname);
		}else{
			echo '删除失败';
		}
	}
	
	//添加商品
	public function addgoods(){
		//所属栏目
		header('Content-Type:text/html;charset=utf-8');
		$c = M('goodsClass');
		$g = M('goods');
		if (isset($_GET['id']) && ($_GET['act']=='edit')){	//表示用户想修改
			$id = intval($_GET['id']);
			$list = $g->where('id='.$id)->find();
			$this->assign('list',$list);
		}
		
		$cate = checkTrip($c->field('id,name,pid,path,concat(path,"-",id) as bpath')->order('bpath')->select());
		if (!empty($cate)){
			foreach($cate as $key=>$val){
				$cate[$key]['count'] = count(explode('-',$val['path']));
			}
		}
		$dw = array('件');
		
		$this->assign(array(
			'dw' => $dw,
			'cate' => $cate
		));
		$this->display();
	}
	
	//删除商品
	public function delGood(){
		header('Content-Type:text/html;charset=utf-8');
		$g = M('goods');		
		
		$ids = rtrim($_GET['gid'],',');

		if ($g->where('id in('.$ids.')')->delete()){
			echo '<script>alert("商品成功下架");location.href="'.__URL__.'"</script>';
		}else{
			echo '<script>alert("未删除成功");location.href="'.__URL__.'"</script>';
		}
	}
	
	//添加商品数据接收页
	public function addData(){
		header('Content-Type:text/html;charset=utf-8');
		$g = D('goods');
		$id = $_POST['id'];
	
		if (isset($_FILES['goods_pics'])){					//如果定义了它，说明用户想修改或添加LOGO
			//文件上传配置
			import('ORG.Net.uploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 1000000 ;// 设置附件上传大小
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->allowTypes = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif');
			$upload->savePath =  './Public/Uploads/goods/';// 设置附件上传目录
			$upload->saveRule = 'uniqid';	
			
			//缩略图配置
			/*	
			$upload->thumb = true;
			$upload->thumbMaxWidth = '400';
			$upload->thumbMaxHeight = '400';
			$upload->thumbPrefix = 's_';
			$upload->thumbRemoveOrigin = true;
			*/
			
			
			//开始上传
			if(!$upload->upload()){
				$this->error($upload->getErrorMsg());
			}else{
				$info =  $upload->getUploadFileInfo();
			}
			
			$list = $g->where('id='.$id)->find();
			//如果用户创建了weblobo，第一种可能是在新增数据，第二种，是在修改数据
			foreach($info as $val){
				$pics .= $val['savename'].'-';	
			}		
			$picsAll = $list['goods_pics'].$pics;	//将原来的图片取出来,再加上临时添加的相片
			$_POST['goods_pics'] = $picsAll;
			$_POST['goods_addtime'] = strtotime($_POST['goods_addtime']);
			
			if ($g->create()){
				if (isset($_POST['act']) && $_POST['act']=='edit'){		//表示修改
					unset($_POST['id']);				//不更新ID
					unset($_POST['goods_num']);			//不更新货号
					$_POST['goods_pics']  = $picsAll;
					$_POST['goods_addtime'] = strtotime($_POST['goods_addtime']);
					if ($g->where('id='.$id)->save()){
						$this->success('更新成功');
					}else{
						$this->error('更新失败');
					}
				}else{													//表示新增
					if ($g->add()){
						$this->success('商品上架成功');
					}else{
						$this->error('商品上价失败');
					}
				}
			}else{
				$this->error('操作失败');
			}			
		}else{//如果没有定义,说明资料已存在，只需更新文字
			unset($_POST['id']);				//不更新ID
			unset($_POST['goods_num']);			//不更新货号
			$_POST['goods_addtime'] = strtotime($_POST['goods_addtime']);

			if ($g->create()){
				if ($g->where('id='.$id)->save()){
					$this->success('更新成功');
				}else{
					$this->error('更新失败');
				}
			}else{
				$this->error('更新失败');
			}
		}	
			

	}
	
	//商品分类
	public function classList(){
		header('Content-Type:text/html;charset=utf-8');
		$c = D('goodsClass');
		$list = checkTrip($c->field('id,name,pid,path,concat(path,"-",id) as bpath')->order('bpath')->select());
		if (!empty($list)){
			foreach($list as $key=>$val){
				$list[$key]['count'] = count(explode('-',$val['path']));
			}
		}
		$this->assign('list',$list);
		$this->display();	
	}
	
	//修改商品分类名称
	public function editClassName(){
		header('Content-Type:text/html;charset=utf-8');
		$c = D('goodsClass');
		$id = $_POST['id'];
		$newval = $_POST['newval'];
		
		$data['name'] = $newval;
		if ($c->where('id='.$id)->save($data)){
			echo '更新成功';
		}
	}
	
	//删除栏目
	public function delLm(){
		header('Content-Type:text/html;charset=utf-8');
		$c = M('goodsClass');
		$id = intval($_GET['id']);	
		if ($c->where('id='.$id)->delete()){
			echo '<script>location.href="'.__URL__.'/'.classlist.'"</script>';
		}else{
			echo '<script>location.href="'.__URL__.'/'.classlist.'"</script>';
		}
	}
	
	//添加子栏目
	public function addCate(){
	header('Content-Type:text/html;charset=utf-8');
	
	$c = D('GoodsClass');
	$data = $c->create($_GET);

	if ($c->create($data)){
			if ($c->add($data)){
				echo '<script>alert("添加成功");location.href="'.__URL__.'/'.classlist.'"</script>';
			}else{
				echo '<script>alert("添加失败");location.href="'.__URL__.'/'.classlist.'"</script>';
			}
		}else{
			echo '<script>alert("添加失败");location.href="'.__URL__.'/'.classlist.'"</script>';
		}	
	}


	//AJAX上传
function upfile(){//上传文件
header('Content-Type: text/html; charset=UTF-8');
$inputName='filedata';//表单文件域name
$attachDir='Public/Uploads/upimgs';//上传文件保存路径，结尾不要带/
$dirType=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
$maxAttachSize=2097152;//最大上传大小，默认是2M
$upExt='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';//上传扩展名
$msgType=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;//立即上传模式
ini_set('date.timezone','Asia/Shanghai');//时区
$err = "";
$msg = "''";
$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';  //临时文件
$localName=''; //上传文件名称

if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
	file_put_contents($tempPath,file_get_contents("php://input"));
	$localName=urldecode($info[2]);
}else{//标准表单式上传
	$upfile=@$_FILES[$inputName];
	if(!isset($upfile)){
		$err='文件域的name错误';
	}elseif(!empty($upfile['error'])){
		switch($upfile['error'])
		{
			case '1':
				$err = '文件大小超过了php.ini定义的upload_max_filesize值';
				break;
			case '2':
				$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
				break;
			case '3':
				$err = '文件上传不完全';
				break;
			case '4':
				$err = '无文件上传';
				break;
			case '6':
				$err = '缺少临时文件夹';
				break;
			case '7':
				$err = '写文件失败';
				break;
			case '8':
				$err = '上传被其它扩展中断';
				break;
			case '999':
			default:
				$err = '无有效错误代码';
		}
	}elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none'){
		$err = '无文件上传';
	}else{
		move_uploaded_file($upfile['tmp_name'],$tempPath);
		$localName=$upfile['name'];  
	}
}
if($err==''){//如果没有错将刚上传的文件移动到指定的目录
	$fileInfo=pathinfo($localName);
	$extension=$fileInfo['extension'];
	if(in_array($extension,explode(',',$upExt)))
	{
		$bytes=filesize($tempPath);
		if($bytes > $maxAttachSize){
			$err='请不要上传大小超过'.formatBytes($maxAttachSize).'的文件';
		}else{
			switch($dirType)
			{
				case 1: $attachSubDir = 'day_'.date('ymd'); break;
				case 2: $attachSubDir = 'month_'.date('ym'); break;
				case 3: $attachSubDir = 'ext_'.$extension; break;
			}
			$attachDir = $attachDir.'/'.$attachSubDir;
			if(!is_dir($attachDir))
			{
				@mkdir($attachDir, 0777);
			}
			$newFilename=date("YmdHis").mt_rand(100,999).'.'.$extension;
			$targetPath = $attachDir.'/'.$newFilename;
			@chmod($targetPath,0755);
			rename($tempPath,$targetPath);
			//$targetPath=$this->jsonString($targetPath);
			$msg="{'url':'".$this->baseroot.'/'.$targetPath."','localname':'".$this->jsonString($localName)."'}";
			$f = @fopen($this->notetxt,'a+');
			fwrite($f,$this->baseroot.'/'.$targetPath."\r\n");
		}
	}else $err='上传文件扩展名必需为：'.$upExt;
	
	@unlink($tempPath); //删除临时文件
}
echo "{'err':'".$this->jsonString($err)."','msg':".$msg."}";
}	

protected function jsonString($str)
{
	return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
}

protected function getimg($cont){//获取内容中所有图片
	preg_match_all('/<img src="(.*?)".*?\/>/',$cont,$arr);
	return $arr[1];
}

protected function noteimg($path='public/files.txt'){//获取文本中记录的所有图片
	$files =  file($path);
	foreach($files as &$f){
		$f = preg_replace('/\s$/m','',$f);
	}
	return $files;
}

protected function delfile($more,$few){
	$files = array_diff($more,$few);
	$fs = $this->noteimg();
	$delfs = array();
	foreach($files as $v){
		$v1=str_replace($this->baseroot.'/','',$v);
		if(is_file($v1)){
			if(unlink($v1)){
				$delfs[] = $v."\r\n";
			}	
		}	
	}
	$f = str_replace($delfs,'',file_get_contents($this->notetxt)); //将已删除的文件路径从public/files.txt中删除
	file_put_contents($this->notetxt,$f);
}

}
?>