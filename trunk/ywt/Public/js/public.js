// JavaScript Document
/*新闻显示、留言显示*/
$(function(){
	$('span tr[id!=h]').mouseover(function(){
		$(this).css('background','#ccffcc')
	}).mouseout(function(){
		$(this).css('background','#ffffff')
	})
	
	//批量删除
	$('[name=alldel]').click(function(){
		var status = $(this).attr('checked');
		$('[name=delall]').show();
		
		$('[name=del]').each(function(){
			if (status == 'checked'){
				$(this).attr('checked','checked');
			}else{
				$(this).removeAttr('checked');	
				$('[name=delall]').hide();	
			}
		})
	})
	
	//当用户点击两个或两个以上,也会自动让批量删除按钮显示出来		
	$('[name=del]').click(function(){
		nums = 0;
		$('[name=del]').each(function(){	
			if ($(this).attr('checked')=='checked'){
				nums = nums+1;
			}		
		})
		if (nums >=2){
			$('[name=delall]').show();
		}else{
			$('[name=delall]').hide();
		}
	})

	

})