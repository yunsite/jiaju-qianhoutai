<?php
class GoodsModel extends Model{	
/*自动填充货号、打折后的价格*/
protected $_auto = array(
	array('goods_num','tianc',1,'callback'),
	array('goods_dzh','dz',3,'callback')
);

//填充货号
protected  function tianc($num){
	$str = 'YWT'.mt_rand(10,90).mt_rand(10,90).mt_rand(10,99);
	return empty($num) ? $str : $num;
}

//计算打折价
protected function dz(){
	$old = $_POST['shop_price'];	//原价
	$dzl = $_POST['goods_dz'];
	
	if (empty($_POST['goods_dz'])){
		return $old;
	}else{
		if (is_numeric($dzl)){
			return round($old * ($dzl/10),2);
		}else{
			return $old;
		}
	}
}
}
?>