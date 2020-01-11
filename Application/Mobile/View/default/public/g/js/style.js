//订单
function qiehuan(a) {
	var num = document.getElementsByClassName('vux-tab-item').length;
	for(var i = 0; i < num; i++) {
		var pages = document.getElementById("page" + i);
		pages.setAttribute('style', 'display: none');
		var btns = document.getElementById("btn" + i);
		btns.removeAttribute('style');
	}
	document.getElementById("page" + a).setAttribute('style', 'display: block');
	document.getElementById("btn" + a).setAttribute('style', 'color: rgb(35, 174, 255);border-bottom: 2px solid rgb(35, 174, 255);');
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
//消息中心
! function(window) {
	"use strict";

	var doc = window.document,
		ydui = {};

	$(window).on('load', function() {});

	var util = ydui.util = {

		parseOptions: function(string) {},

		pageScroll: function() {}(),

		localStorage: function() {}(),

		sessionStorage: function() {}(),

		serialize: function(value) {},

		deserialize: function(value) {}
	};

	function storage(ls) {}

	$.fn.emulateTransitionEnd = function(duration) {};

	if(typeof define === 'function') {
		define(ydui);
	} else {
		window.YDUI = ydui;
	}

}(window);

! function(window) {
	"use strict";

	function Tab(element, options) {
		this.$element = $(element);
		this.options = $.extend({}, Tab.DEFAULTS, options || {});
		this.init();
		this.bindEvent();
		this.transitioning = false;
	}

	Tab.TRANSITION_DURATION = 150;

	Tab.DEFAULTS = {
		nav: '.tab-nav-item',
		panel: '.tab-panel-item',
		activeClass: 'tab-active'
	};

	Tab.prototype.init = function() {
		var _this = this,
			$element = _this.$element;

		_this.$nav = $element.find(_this.options.nav);
		_this.$panel = $element.find(_this.options.panel);
	};

	Tab.prototype.bindEvent = function() {
		var _this = this;
		_this.$nav.each(function(e) {
			$(this).on('click.ydui.tab', function() {
				_this.open(e);
			});
		});
	};

	Tab.prototype.open = function(index) {
		var _this = this;

		index = typeof index == 'number' ? index : _this.$nav.filter(index).index();

		var $curNav = _this.$nav.eq(index);

		_this.active($curNav, _this.$nav);

		_this.active(_this.$panel.eq(index), _this.$panel, function() {
			$curNav.trigger({
				type: 'opened.ydui.tab',
				index: index
			});
			_this.transitioning = false;
		});
	};

	Tab.prototype.active = function($element, $container, callback) {
		var _this = this,
			activeClass = _this.options.activeClass;

		var $avtive = $container.filter('.' + activeClass);

		function next() {
			typeof callback == 'function' && callback();
		}

		$element.one('webkitTransitionEnd', next).emulateTransitionEnd(Tab.TRANSITION_DURATION);

		$avtive.removeClass(activeClass);
		$element.addClass(activeClass);
	};

	function Plugin(option) {
		var args = Array.prototype.slice.call(arguments, 1);

		return this.each(function() {
			var target = this,
				$this = $(target),
				tab = $this.data('ydui.tab');

			if(!tab) {
				$this.data('ydui.tab', (tab = new Tab(target, option)));
			}

			if(typeof option == 'string') {
				tab[option] && tab[option].apply(tab, args);
			}
		});
	}

	$(window).on('load.ydui.tab', function() {
		$('[data-ydui-tab]').each(function() {
			var $this = $(this);
			$this.tab(window.YDUI.util.parseOptions($this.data('ydui-tab')));
		});
	});

	$.fn.tab = Plugin;

}(window);
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