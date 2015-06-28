$(function() {
    var targets = $('[rel~=tooltip]'),
        target = false,
        tooltip = false,
        title = false,
        tip;

    targets.bind('mouseover', function() {
        target = $(this);
        tip = target.attr('title');
        tooltip = $('<div id="tooltip"></div>');

        if (!tip || tip === '') {
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
            }, 120);
        };

        initTooltip();
        $(window).resize(initTooltip);

        var removeTooltip = function() {
            tooltip.one('webkitAnimationEnd mozAnimationEnd oAnimationEnd oanimationend animationend', function() {
                // http://osvaldas.info/detecting-css-animation-transition-end-with-javascript
                tooltip.remove();
            });
            tooltip.animate({
                top: '-=10',
                opacity: 0
            }, 120);

            target.attr('title', tip);
        };

        target.bind('mouseout', removeTooltip);
        tooltip.bind('click', removeTooltip);
    });
});
