$(function() {
    var targets = $('[rel~=tooltip]'),
        target = false,
        tooltip = false,
        title = false,
        tip;

    targets.bind('mouseenter', function() {
        target = $(this);
        tip = target.attr('title');
        tooltip = $('<div id="tooltip"></div>');

        if (!tip || tip == '') {
            return false;
        }

        target.removeAttr('title');
        tooltip.css('opacity', 0)
            .html(tip)
            .appendTo('body');

        var initTooltip = function() {
            if ($(window).width() < tooltip.outerWidth() * 1.5) {
                tooltip.css('max-width', $(window).width() / 2);
            } else if (tip.indexOf(' ') > -1) {
                tooltip.css('max-width', 340);
            }

            var posLeft = target.offset().left + (target.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                posTop = target.offset().top - tooltip.outerHeight() - 20;

            if (posLeft < 0) {
                posLeft = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass('left');
            } else
                tooltip.removeClass('left');

            if (posLeft + tooltip.outerWidth() > $(window).width()) {
                posLeft = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass('right');
            } else
                tooltip.removeClass('right');

            if (posTop < 0) {
                posTop = target.offset().top + target.outerHeight();
                tooltip.addClass('top');
            } else
                tooltip.removeClass('top');

            tooltip.css({
                left: posLeft,
                top: posTop
            }).animate({
                top: '+=10',
                opacity: 1
            }, 50);
        };

        initTooltip();
        $(window).resize(initTooltip);

        var removeTooltip = function() {
            tooltip.animate({
                top: '-=10',
                opacity: 0
            }, 50, function() {
                $(this).remove();
            });

            target.attr('title', tip);
        };

        target.bind('mouseleave', removeTooltip);
        tooltip.bind('click', removeTooltip);
    });
});
