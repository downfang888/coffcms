

$(document).ready(function(){
	
	///首页导航下拉
	$('.index_nav ul li').mouseover(function(){
	
	$(this).find('.nav_tan').stop(true,true).slideDown();
	$(this).children("a").addClass("cur");
	});
	$('.index_nav ul li').mouseleave(function(){
	
	$(this).find('.nav_tan').stop(true,true).slideUp('fast');
	$(this).children("a").removeClass("cur");
	});
	
	
	///导航下拉
	$('.g_nav01 table tr td').mouseover(function(){
	
	$(this).find('.nav_tan').stop(true,true).slideDown();
	$(this).children("a").addClass("cur");
	});
	$('.g_nav01 table tr td').mouseleave(function(){
	
	$(this).find('.nav_tan').stop(true,true).slideUp('fast');
	$(this).children("a").removeClass("cur");
	});
	
	//搜索
	$(".isearch span").click(
	function(){
		$(".search01").toggle();
		}
	)
	
});


