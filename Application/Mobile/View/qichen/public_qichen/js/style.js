//定义一个60秒计时器变量
var countdown = 60;
//构造一个倒计时函数叫settime
function settime(obj) {
	//开始判断倒计时是否为0
	if(countdown == 0) {
		obj.removeAttribute("disabled");
		obj.innerText = "获取验证码";
		countdown = 60;
		//立即跳出settime函数，不再执行函数后边的步骤
		return;
	} else {
		obj.setAttribute("disabled", true);
		obj.innerText = "重新发送(" + countdown + ")";
		countdown--;
	}
	//过1秒后执行倒计时函数
	setTimeout(function() {
		settime(obj)
	}, 1000)
}
//发送验证码结束

function qiehuan(a) {
	var num = document.getElementsByClassName('vux-tab-item').length;
	for(var i = 0; i < num; i++) {
		var pages = document.getElementById("page" + i);
		pages.setAttribute('style', 'display: none');
		var btns = document.getElementById("btn" + i);
		btns.removeAttribute('style');
	}
	document.getElementById("page" + a).setAttribute('style', 'display: block');
	document.getElementById("btn" + a).setAttribute('style', 'color: rgb(242, 179, 0);border-bottom: 2px solid rgb(242, 179, 0);');
}
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
}
//个人中心设置上传头像
$(document).ready(function() {
	$('#div22').click(function() {
		$('.photo_scc').click();
	})
	$('.photo_scc').change(function() {
		var fileObj = $(this).get(0).files;
		for(var i = 0; i < fileObj.length; i++) {
			$('#div22').html('').prepend("<img style='width: 50px;height: 50px; border-radius: 50%;' src='" + getObjURL(fileObj[i]) + "' />");

		}
	})
});

function getObjURL(file) {
	var url = null;
	if(window.createObjectURL != undefined) {
		url = window.createObjectURL(file);
	} else if(window.URL != undefined) {
		url = window.URL.createObjectURL(file);
	} else if(window.webkitURL != undefined) {
		url = window.webkitURL.createObjectURL(file);
	}
	return url;
}
//简历上传头像
$(document).ready(function() {
	$('#div23').click(function() {
		$('.photo_scc1').click();
	})
	$('.photo_scc1').change(function() {
		var fileObj = $(this).get(0).files;
		for(var i = 0; i < fileObj.length; i++) {
			$('#div23').html('').prepend("<img style='width: 90px;height: 90px; border-radius: 50%;' src='" + getObjURL(fileObj[i]) + "' />");

		}
	})
});

function getObjURL(file) {
	var url = null;
	if(window.createObjectURL != undefined) {
		url = window.createObjectURL(file);
	} else if(window.URL != undefined) {
		url = window.URL.createObjectURL(file);
	} else if(window.webkitURL != undefined) {
		url = window.webkitURL.createObjectURL(file);
	}
	return url;
}
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