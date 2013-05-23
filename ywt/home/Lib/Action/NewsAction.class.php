<?php
class NewsAction extends Action{
	//新闻动态首页
	public function index(){
		header('Content-Type:text/html;charset=utf-8');
		require './home/Lib/Action/Public.php';
		$n = M('News');		//新闻中心
		
		load('extend');
		import("ORG.Util.Page");// 导入分页类

		$count = $n->count();
		$page = new Page($count,6);
		$show = $page->show();

		$news = checkTrip($n->where('sh=1')->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select());
	
		$this->assign(array(
			'news' => $news,
			'page' => $show
		));		
		
		$this->display();
	}
	
	//查看新闻
	public function shownews(){
		header('Content-Type:text/html;charset=utf-8');
		$id = $_GET['nid'] + 0;
		if ($id <= 0){
			exit('非法操作');
		}
		require './home/Lib/Action/Public.php';
		$n = M('News');		//新闻中心
		$news = checkTrip($n->where('id='.$id)->find());
		if (empty($news)){
			exit('没有找到数据');
		}
		
		//更新访问量
		session_start();
		$ip = $_SERVER['REMOTE_ADDR'];
		$_SESSION['ok'] = true;
		
		$djs = checkTrip($n->field('news_djs')->where('id='.$id)->find());

		$data['news_djs'] = $djs['news_djs'] + 1;
		$n->where('id='.$id)->save($data);

		$this->assign(array(
			'news' => $news,
		));		
		
		$this->display();
	}

}

?>