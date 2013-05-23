// JavaScript Document
$(function(){
	$('[name=checkInfo]').addClass('onShow');
	
	$('[reg]').click(function(){
		spanObj = $(this).nextAll('[name=checkInfo]').eq(0);
		spanObj.removeClass();
		spanObj.addClass('onFocus');
	})
	
	$('[reg]').blur(function(){
		check($(this));					   
	})
	
	$('form').submit(function(){
		var isSubmit = true;
		$('[reg]').each(function(){
			if (!check($(this))){
				isSubmit = false;
			}
		})
		
		return isSubmit;
	})
	
	$('textarea').keyup(function(){
		var maxlen = 20;
		var count = $(this).val().length;
		var nums = maxlen-count;	//当前可输入字数
		var spanobj = $(this).nextAll('[name=checkInfo]').eq(0);
		if(nums<=0){
			nums = 0;
		}
		spanobj.eq(0).text('你还可以输入'+nums+'个字');
		
		if (nums<=10){
			spanobj.html('<span name="checkInfo">您还可以输入<font color="red">'+nums+'</font>个字</span>');
		}
	})
})

$.extend({
	show:function(){
		alert('show');
	}		 
})
function check(obj){
	var reg = new RegExp(obj.attr('reg'));
	var value = obj.attr('value');
	
	spanObj = obj.nextAll('[name=checkInfo]').eq(0);
	spanObj.removeClass();
	
	if (!reg.test(value)){
		spanObj.addClass('onError');
		return false;
	}else{
		spanObj.addClass('onSuccess');	
		return true;
	}
}
