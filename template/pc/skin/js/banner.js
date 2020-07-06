/* 幻灯动画 */
jQuery("#slideBox").slide({
	mainCell: ".bd ul",
	interTime: 5000,
	autoPlay: true
});
jQuery("#slideBox").hover(function() {
	$(this).find(".prev").stop(true, true).fadeIn(500);
	$(this).find(".next").stop(true, true).fadeIn(500);
},
function(){
	$(this).find(".prev").fadeOut(500);
	$(this).find(".next").fadeOut(500);
});