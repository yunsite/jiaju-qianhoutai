<?php
class GoodsClassModel extends Model{
	
	/*自动填充*/
	protected $_auto = array(
		array('path','tianc','3','callback')			//填充path字段,这里是数据库的字段,用回调函数,在添加和修改数据的时候填充
	);
	
	/*自动填充字段0-1-3-5..  该条记录的path连接该id*/
	protected  function tianc(){
		$pid = isset($_GET['pid']) ? $_GET['pid'] : 0;
		if ($pid==0) return 0;				//如果不写这条语句,那么执行到最后的结果就是：0-0 对吧?
		$list = $this->where('id='.$pid)->find();
		//dump($list); 这个dump 是查询该条记录的所有信息,如果模块里写了 dump($obj->create()) 这个dump指$_POST传过去的数据,
		
		$data = $list['path'].'-'.$list['id'];
		return $data;
		
	}
}