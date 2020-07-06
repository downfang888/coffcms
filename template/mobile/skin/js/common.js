//字体设置
(function (doc, win) {
    var docEl = doc.documentElement,
        resizeEvt = "orientationchange" in window ? "orientationchange" : "resize",
        recalc = function () {
            var clientWidth = docEl.clientWidth;
            //var foot = document.getElementById("foot");
            if (!clientWidth) return;
            if (clientWidth<640){
                docEl.style.fontSize = 120 * (clientWidth / 640) + "px";
                console.log(120 * (clientWidth / 640) + "px");
            }else{
                docEl.style.fontSize = "120px";
            }
        };

    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);



//顶部搜索
$(function(){
    $(".search_hl").click(function(event) {
    	var search =$(".search");
    	search.animate({top:0}, 250)
    });
    $(".xbtn").click(function(event) {
    	var search =$(".search")
    	search.animate({top:"-120%"}, 250)
    });
})





$(document).ready(function() {
//banner
	var swiper = new Swiper('.ban', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        spaceBetween: 30,
        hashnav: true
    });
//公司环境
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        spaceBetween: 30,
        hashnav: true
    });
//新闻资讯
	var swiper = new Swiper('.news_scroll', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        spaceBetween: 30,
        hashnav: true
    });
	
	
	
});

















$(document).ready(function() {


    var navbtn = $('.nav-btn');
    var box = $('.allpage'),
        blackFixed = $(".black-fixed");
    function navShow() {
        if(box.hasClass('clicked')){
            blackFixed.removeClass('black-clicked');
            box.removeClass('clicked');
            $(".head,.footer,.type").removeClass('clicked');
            $(".nav").removeClass('fixed');
        }else{
            box.addClass('clicked');
            $(".head,.footer,.type").addClass('clicked');
            $(".nav").addClass('fixed');
            blackFixed.addClass('black-clicked');
        }
    };
    navbtn.click(navShow);
    blackFixed.click(navShow);

    $('.top-search').click(function(){     // 搜索
        var search = $(".search"),
            _this = $(this);
        if(search.css("display") == "none"){
            search.show();
            _this.html("&#xe609;");
            $(".search-input").focus();
        }else{
            search.hide();
            _this.html("&#xe60f;");
            $(".search-input").blur();
        }
    });

    $('.class-btn').click(function(){     // 分类
        $(".type").toggle();
    });
    $('.common-search-btn').click(function(){     // 分类
        $(".common-search").toggle();
    });


   
});
