<?php
class GoodsAction extends Action{
	//产品中心
	public function index(){	
		import("ORG.Util.Page");// 导入分页类
		require './home/Lib/Action/Public.php';
		$g = M('goods');	//产品中心

		$count = $g->count();
		$page = new Page($count,6);
		$show = $page->show();
				
		$goods = checkTrip($g->order('goods_addtime desc')->limit($page->firstRow.','.$page->listRows)->select());
		
		foreach($goods as $key=>$val){	//随机取出每条记录的一张图片
			$arr = explode('-',$val['goods_pics']);
			$c = count($arr)-2;
			$goods[$key]['goods_pics'] = $arr[rand(0,$c)];
		}
	
		$this->assign(array(
			'goods' => $goods,
			'page' => $show
		));		
		
		$this->display();
	}
	
	//商品详情
	public function showGoods(){
		header('Content-Type:text/html;charset=utf-8');	
		require './home/Lib/Action/Public.php';
		$g = M('goods');	//产品中心

		$gid = intval($_GET['gid']);
		$goods = checkTrip($g->where('id='.$gid)->find());		

		$this->assign(array(
			'goods' => $goods,
		));
		
		$this->display();
	}

}

?>