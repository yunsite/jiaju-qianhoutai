<?php
require './login.inc.php';
require './init.inc.php';
class MsgAction extends Action{
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		load('extend');			// 导入扩展函数(对中文字符串的截取,过滤html)
		import("ORG.Util.Page");// 导入分页类
		
		$m = M('msgs');
		$count = $m->count();
		$page = new Page($count,20);
		$show = $page->show();
		
		//后台的首页查看未读留言
		if (isset($_GET['allmsg']) && isset($_GET['act'])){
			if ($_GET['allmsg'] ==0 && $_GET['act']=='nread'){
				$list = checkTrip($m->where('m_status=0')->order('m_id desc')->select());
			}
			$this->assign('list',$list);
			$this->display();
			return;
		}
		
		if (!isset($_GET['search'])){
			$list = checkTrip($m->order('m_time desc')->limit($page->firstRow.','.$page->listRows)->select());
		}else{
			//查看所有留言、已读、未读
			$sql = '';
			if (isset($_GET['allmsg'])){
				if ($_GET['allmsg']!='status'){
					$sql = 'm_status='.$_GET['allmsg'];
				}
			}
			
			//按留言标题、姓名查找
			$methodone = isset($_GET['search_bzb']) ?$_GET['search_bzb'] : '';
			$bzb = $_GET['bzb'];
			
			if ($bzb !== ''){
				$and = (isset($_GET['allmsg']) && $_GET['allmsg']!='status')  ? ' and ' : '';
				$sql .= $and.$methodone.' like "%'.$bzb.'%"';
			}
			
			//按添加时间排序
			$methodtwo = $_GET['search_tdb'];
			$px = $_GET['order'];
			$order = $methodtwo.' '.$px;
			
			foreach ($_GET as $k=>$v){
				if ($_GET[$k]==='') unset($_GET[$k]);
			}
			
			//查找记录后,更新显示条数
			$count = $m->where($sql)->order($order)->count();
			$page = new Page($count,20);
			$show = $page->show();
						
			$list = checkTrip($m->where($sql)->order($order)->limit($page->firstRow.','.$page->listRows)->select());
			
			foreach($list as $key=>$val){
				 $val[$methodone] = str_replace($bzb,'<p>'.$bzb.'</p>',$val[$methodone]);
				 $list[$key][$methodone] = $val[$methodone];
			}
		}
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	
	//阅读留言
	public function readMsg(){
		header('Content-Type:text/html;charset=utf-8');
		load('extend');			// 导入扩展函数(对中文字符串的截取,过滤htm		
		$m = M('msgs');	
		$mid = isset($_GET['mid']) ? intval($_GET['mid']) : 1;
		
		$list = checkTrip($m->where('m_id='.$mid)->find());
		$this->assign('list',$list);
		if ($list['isread']!=1){
			$data['m_status'] = 1;
			$m->where('m_id='.$mid)->save($data);		//更新为已读		
		}

		$this->display();
		
	}
	
	//删除留言
	public function delNew(){
		header('Content-Type:text/html;charset=utf-8');
		$m = M('msgs');		
		
		$ids = rtrim($_GET['mid'],',');

		if ($m->where('m_id in('.$ids.')')->delete()){
			echo '<script>alert("删除成功");location.href="'.__URL__.'"</script>';
		}else{
			echo '<script>alert("删除失败");location.href="'.__URL__.'"</script>';
		}
	}
}