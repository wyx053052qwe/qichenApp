/**
 * 填充时间
 */
$(function() {
    var tempDateArr = $('.qs-temp-date');
    $.each(tempDateArr, function() {
        var dtype = $(this).data('type');
        var experienceDate = new Date();
        var eyear = experienceDate.getFullYear();
        var eyearMin = eyear - 59;
        var dateHtml = '<div class="f-box-inner">';
        for (var i = eyear; i >= eyearMin; i--) {
            dateHtml += '<li><a class="font12 f-item f-item-year" href="javascript:;" data-code="' + i + '">' + i + '年</a></li>';
        }
        dateHtml += '</div>';
        dateHtml += '<div class="f-box-inner">';
        for (var i = 1; i <= 12; i++) {
            dateHtml += '<li><a class="font12 f-item f-item-month js-cancelActionSheet" href="javascript:;" data-code="' + i + '">' + i + '月</a></li>';
        }
        dateHtml += '</div>';
        $('.f-box-date-' + dtype).html(dateHtml);
        function handleSelect(dtype, yearVal, monthVal) {
            if (eval(yearVal)) {
                $('.f-box-date-' + dtype + ' .f-item-year').removeClass('select');
                $('.f-box-date-' + dtype + ' .f-item-month').removeClass('select');
                $('.f-box-date-' + dtype + ' .f-item-year').each(function() {
                    if ($(this).data('code') == yearVal) {
                        $(this).addClass('select');
                    }
                });
                var $yearSel = $('.f-box-date-' + dtype).find('.f-item-year.select'),
                    $yearIn = $yearSel.closest('.f-box-inner');
                $yearIn.scrollTop(
                    $yearSel.offset().top - $yearIn.offset().top + $yearIn.scrollTop()
                );
                $('.f-box-date-' + dtype + ' .f-item-month').each(function() {
                    if ($(this).data('code') == monthVal) {
                        $(this).addClass('select');
                    }
                });
                var $monSel = $('.f-box-date-' + dtype).find('.f-item-month.select'),
                    $monIn = $monSel.closest('.f-box-inner');
                $monIn.scrollTop(
                    $monSel.offset().top - $monIn.offset().top + $monIn.scrollTop()
                );
            } else {
                $('.f-box-date-' + dtype + ' .f-item-year').eq(0).addClass('select');
                $('.f-box-date-' + dtype + ' .f-item-month').eq(0).addClass('select');
            }
        }
        if (dtype == 'end') {
            if (!eval($('#todate').val())) {
                handleSelect(dtype, $('.f-year-code-' + dtype).val(), $('.f-month-code-' + dtype).val());
            }
        } else {
            handleSelect(dtype, $('.f-year-code-' + dtype).val(), $('.f-month-code-' + dtype).val());
        }
    
        $('.f-box-date-' + dtype + ' .f-item-year').on('click', function() {
            $('.f-box-date-' + dtype + ' .f-item-year').removeClass('select');
            $(this).addClass('select');
        })
        $('.f-box-date-' + dtype + ' .f-item-month').on('click', function() {
            $('.f-box-date-' + dtype + ' .f-item-month').removeClass('select');
            $(this).addClass('select');
            // 赋值
            $('.f-year-code-' + dtype).val($('.f-box-date-' + dtype + ' .f-item-year.select').data('code'));
            $('.f-month-code-' + dtype).val($('.f-box-date-' + dtype + ' .f-item-month.select').data('code'));
            $('.f-date-txt-' + dtype).text($('.f-box-date-' + dtype + ' .f-item-year.select').text() + '-' + $('.f-box-date-' + dtype + ' .f-item-month.select').text());
            if (dtype == 'end') {
                $('#todate').val('0');
            }
        })
        $('.js-todate').on('click', function() {
            $('#todate').val('1');
            $(this).closest('.js-actionParent').find('.f-year-code-end').val('');
            $(this).closest('.js-actionParent').find('.f-month-code-end').val('');
            $(this).closest('.js-actionParent').find('.f-date-txt-end').text('至今');
        })
    })
});
