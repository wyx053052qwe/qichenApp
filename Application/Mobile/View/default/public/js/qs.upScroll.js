/**
 * 向上滑动插件
 * @constructor
 */
function upScroll(options) {
    var defaultOptions = {
        maskClose: true,
        showCancel: true
    };
    $.extend(defaultOptions, options);
    this.options = defaultOptions;
    this.init();
}

upScroll.prototype = {
    /**
     * 初始化
     */
    init: function() {
        var that = this;
        var upScrollHtml = '<div class="qs-mask jUpMask" style="display:none"></div>';
        upScrollHtml += '<div class="qs-actionsheet jUpMain">';
        upScrollHtml += '<div class="qs-actionsheet-menu jUpContainer"></div>';
        if (that.options.showCancel) {
            upScrollHtml += '<div class="qs-actionsheet-action"><div class="qs-actionsheet-cell jUpCancel">取消</div></div>';
        }
        upScrollHtml += '</div>';
        $('body').append(upScrollHtml);
        this.upMask = $('.jUpMask');
        this.upContainer = $('.jUpContainer');
        this.upMain = $('.jUpMain');
        $('.jUpCancel').on('click', function() {
            that.hide();
        });
        $('.jUpMask').on('click', function() {
            if (that.options.maskClose) {
                that.hide();
            }
        });
    },
    /**
     * 设置列表内容
     * @param conArr 数组
     */
    setContent: function(conArr) {
        var upConHtml = '';
        for (var i = 0; i < conArr.length; i++) {
            upConHtml += '<div class="jUpList qs-actionsheet-cell">' + conArr[i] + '</div>';
        }
        this.upContainer.html(upConHtml);
    },
    /**
     * 获取列表
     * @param index 下标
     */
    getList: function(index) {
        return this.upContainer.find('.jUpList').eq((index-1));
    },
    /**
     * 显示
     */
    show: function() {
        this.upMask.show(200);
        if (funJudgeX()) {
            $('.jUpCancel').addClass('iPhoneX');
        }
        this.upMain.addClass('qs-actionsheet-toggle');
    },
    /**
     * 隐藏
     */
    hide: function() {
        var that = this;
        if (funJudgeX()) {
            $('.jUpCancel').removeClass('iPhoneX');
        }
        this.upMain.removeClass('qs-actionsheet-toggle');
        this.upMask.hide();
        setTimeout(function() {
            that.upMain.remove();
            that.upMask.remove();
        }, 200);
    },
	/**
     * 快速隐藏
     */
    fast_hide: function() {
        var that = this;
        this.upMask.hide();
        that.upMain.remove();
        that.upMask.remove();
		un_lock_touchmove();
    }
}