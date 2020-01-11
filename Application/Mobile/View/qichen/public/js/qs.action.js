/**
 * action
 */
$(function() {
    $(".js-showActionSheet").on("click", function(){
        var $iosActionsheet = $(this).closest('.js-actionParent').find('.js-actionsheet');
        var $iosMask = $(this).closest('.js-actionParent').find('.qs-mask');
        $iosActionsheet.removeClass('qs-actionsheet-toggle');
        $iosActionsheet.addClass('qs-actionsheet-toggle');
        $iosMask.fadeIn(200);
        $iosMask.on('click', hideActionSheet);
        $(this).closest('.js-actionParent').find('.js-cancelActionSheet').on('click', hideActionSheet);
        $(this).closest('.js-actionParent').find('.qs-actionsheet-cell').on('click', hideActionSheet);
        function hideActionSheet() {
            $(this).closest('.js-actionParent').find('.js-actionsheet').removeClass('qs-actionsheet-toggle');
            $(this).closest('.js-actionParent').find('.qs-mask').fadeOut(200);
        }
    });
});
