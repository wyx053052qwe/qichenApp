// JavaScript Document
$(document).ready(function()
{
	// 点击收起、展开
	$('.J-close-qq').live('click', function () {
		$('.qq-float').hide();
		$('.qq-float-min').show();
		setCookie('show_qq_float', 0);
	});
	$('.J-open-qq').live('click', function () {
		$('.qq-float').show();
		$('.qq-float-min').hide();
		setCookie('show_qq_float', 1);
	});
	// 写入cookies
	function setCookie(name, value) {
		var days = 7;
		var exp = new Date();
		exp.setTime(exp.getTime() + days * 24 * 60 * 60 * 1000);
		document.cookie = name + "=" + escape(value) + ";path=/;expires=" + exp.toGMTString();
	}
});
