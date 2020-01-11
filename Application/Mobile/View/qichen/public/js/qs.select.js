/**
 * select
 */
$(function() {
    var $imSel = $('.imitateSelect');
    // 判断是否需要复原选择
    $imSel.each(function() {
        var $objKey    = $(this).find('.selectKey'),
            $objTxt    = $(this).find('.selectTxt'),
            currentKey = eval($.trim($objKey.val())),
            $cuParent  = $(this).closest('.imSelectParent'),
            $cuSelect  = $cuParent.find('.imSelect'),
            $cuOption  = $cuParent.find('.imOption'),
            opNum      = $cuOption.length,
             halfNum    = (opNum/2),
            domNum     = 13;
        // option 选项大于10个，展现方式做特殊处理
        if (opNum > 2000) {
            var moreHtml = '<div class="imMoreCell">';
            if ($cuSelect.data('type') == 'year') {
                // 出生年份做特殊处理
                $cuSelect.addClass('x4');
                for (var i = 0; i < domNum; i++) {
                    moreHtml += '<div class="imOption" data-key="'+ $cuOption.eq(i).data('key') +'"data-txt="' + $cuOption.eq(i).data('txt') +'">' + $cuOption.eq(i).data('txt') +'</div>';
                }
                moreHtml += '</div><div class="imMoreCell">';
                for (var i = domNum; i < (domNum*2); i++) {
                    moreHtml += '<div class="imOption" data-key="'+ $cuOption.eq(i).data('key') +'"data-txt="' + $cuOption.eq(i).data('txt') +'">' + $cuOption.eq(i).data('txt') +'</div>';
                }
                moreHtml += '</div><div class="imMoreCell">';
                for (var i = domNum*2; i < (domNum*3); i++) {
                    moreHtml += '<div class="imOption" data-key="'+ $cuOption.eq(i).data('key') +'"data-txt="' + $cuOption.eq(i).data('txt') +'">' + $cuOption.eq(i).data('txt') +'</div>';
                }
                moreHtml += '</div><div class="imMoreCell">';
                for (var i = domNum*3; i < opNum; i++) {
                    moreHtml += '<div class="imOption" data-key="'+ $cuOption.eq(i).data('key') +'"data-txt="' + $cuOption.eq(i).data('txt') +'">' + $cuOption.eq(i).data('txt') +'</div>';
                }
                moreHtml += '</div>';
            } else {
                $cuSelect.addClass('more');
                for (var i = 0; i < halfNum; i++) {
                    moreHtml += '<div class="imOption" data-key="'+ $cuOption.eq(i).data('key') +'"data-txt="' + $cuOption.eq(i).data('txt') +'">' + $cuOption.eq(i).data('txt') +'</div>';
                }
                moreHtml += '</div><div class="imMoreCell">';
                for (var i = halfNum; i < opNum-1; i++) {
                    moreHtml += '<div class="imOption" data-key="'+ $cuOption.eq(i).data('key') +'"data-txt="' + $cuOption.eq(i).data('txt') +'">' + $cuOption.eq(i).data('txt') +'</div>';
                }
                moreHtml += '</div>';
            }
            $cuSelect.html(moreHtml);
        }
        if (currentKey) {
            // 有值才做复原选择
            $cuParent.find('.imOption').each(function() {
                if (currentKey === eval($.trim($(this).data('key')))) {
                    $(this).addClass('selected');
                    $objTxt.text($.trim($(this).data('txt')));
                }
            });
        }
    });
    // 显示
    $imSel.off().on('click', function() {
        $('.imSelMask').remove();
        $('body').append('<div class="imSelMask"></div>');
        $(this).closest('.imSelectParent').find('.imSelect').addClass('qs-action-toggle');
        funAgainstX($(this).closest('.imSelectParent').find('.imSelect'));
        $('.imSelMask').on('click', function() {
            imSelHide();
        });
    });
    // option 点击
    $imSel.closest('.imSelectParent').find('.imOption').off().on('click', function() {
        $(this).closest('.imSelectParent').find('.imOption').removeClass('selected');
        $(this).addClass('selected');
        var $objParent = $(this).closest('.imSelectParent'),
            $objKey = $objParent.find('.selectKey'),
            $objTxt = $objParent.find('.selectTxt'),
            currentKey = $(this).data('key'),
            currentTxt = $(this).data('txt');
        $objKey.val(currentKey);
        $objTxt.html(currentTxt);
        if ($(this).hasClass('contactType')) {
            // 单独处理发布职位选择联系方式
            if(currentKey == '1') {
                $('#contact_box').hide();
            } else {
                $('#contact_box').show();
            }
        }
        imSelHide();
    });
    // 隐藏
    function imSelHide() {
        $('.imSelMask').remove();
        $('.imSelect').removeClass('qs-action-toggle');
    }
    // 对iPhoneX做处理
    function funAgainstX(obj) {
        if (funJudgeX()) {
            obj.addClass('iPhoneX');
            if (obj.find('.imMoreCell').length) {
                var $mcArr = obj.find('.imMoreCell');
                $mcArr.each(function() {
                    $(this).find('.imOption').eq($(this).find('.imOption').length - 1).css('border-bottom', '0');
                })
            } else {
                obj.find('.imOption').eq(obj.find('.imOption').length - 1).css('border-bottom', '0');
            }
        }
    }
});
