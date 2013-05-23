<?php
class IndexAction extends Action {
    public function index(){
		header('Content-Type:text/html;charset=utf-8');
		require './home/Lib/Action/Public.php';
		$g = M('goods');	//产品中心
		$n = M('News');		//新闻表中的产品知识
		
		$dz_goods = checkTrip($g->where('goods_dz > 0')->order('goods_addtime desc')->limit(0,6)->select());	
		$new_goods = checkTrip($g->order('goods_addtime desc')->limit(0,6)->select());
		$zs = checkTrip($n->where('news_class = "产品知识"')->order('add_time desc')->having('sh = 1')->limit(10)->select());
		
		foreach($new_goods as $key=>$val){	//最新商品图片随机显
			$arr = explode('-',$val['goods_pics']);
			$c = count($arr)-2;
			$new_goods[$key]['goods_pics'] = $arr[rand(0,$c)];
		}
		
		foreach($dz_goods as $key=>$val){	//打折商品图片随机显
			$arr = explode('-',$val['goods_pics']);
			$c = count($arr)-2;
			$dz_goods[$key]['goods_pics'] = $arr[rand(0,$c)];
		}
		
		$this->assign(array(
			'dzgoods' => $dz_goods,
			'newgoods' => $new_goods,
			'zs' => $zs
			
		));
		
		$this->display();
		
    }
}