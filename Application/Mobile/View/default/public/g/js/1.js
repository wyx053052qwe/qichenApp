//获得slider插件对象
var gallery = mui('.mui-slider');
gallery.slider({
	interval: 3000 //自动轮播周期，若为0则不自动播放，默认为0；
});
//首页幻灯结束

//向上滚动公告
$(function() {

	//滚动公告，参数1为id名字 参数2为滚动速度
	setTop('scrolltop', 1000);

});

function setTop(name, speed) {
	$('#' + name + '>div:last').html($('#' + name + '>div:first').html());
	var timer = setInterval(function() {
		scrolltop(name);
	}, speed);
}

function scrolltop(name) {
	var box = $("#" + name);
	var boxHeight = $('#' + name + '>div:first').prop("scrollHeight");
	var go = box.height();

	if(boxHeight == box.scrollTop()) {
		box.scrollTop(0);
	}
	box.animate({
		scrollTop: "+=" + go + "px"
	});
};
//返回顶部
$(window).scroll(function() {
	if($(this).scrollTop() >= 150) {
		$("#goTop").fadeIn();
	} else {
		$("#goTop").fadeOut();
	}
});
$("#goTop").click(function() {
	$("html,body").animate({
		scrollTop: "0px"
	}, 400);
});
