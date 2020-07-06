/* 搜索提示 */
function checksearch(the)
 {
	if ($.trim(the.key.value) == '')
	 {
		alert('请输入关键字');
		the.key.focus();
		the.key.value = '';
		return false
	}
	if ($.trim(the.key.value) == '请输入关键字')
	 {
		alert('请输入关键字');
		the.key.focus();
		the.key.value = '';
		return false
	}
}

/* 更多加载 */
var startHref;
$(function() {
	$(".c-moreAjax a").click(function() {
		var href = $(this).attr("href");
		startHref = href;
		if (href != undefined) {
			$.ajax({
				type: "get",
				cache: false,
				url: startHref,
				success: function(data) {
					var $result = $(data).find(".c-ajax");
					$(".c-commentAjax").append($result);
					var newHref = $(data).find(".c-moreAjax a").attr("href");
					if (newHref != "") {
						$(".c-moreAjax a").attr("href", newHref)
					} else {
						$(".c-moreAjax").html('')
					}
				}
			})
		}
		return false
	})
});

/* 浮动工具 */
$(".c-tools ul li").hover(function() {
	$(this).find("span").stop().fadeToggle(10)
});

/* 返回顶部 */
$(".top-ico").click(function() {
	$("body,html").stop().animate({
		scrollTop: 0
	}, 100);
});