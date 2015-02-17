$(function() {
    function highlightLine(lineNum) {
        var selector = ".logs--line[data-line-num='" + lineNum + "']";
        $(selector).addClass("highlighted");
    }

    function hashChange() {
        $(".logs--line").removeClass("highlighted");
        var hash = window.location.hash;
        var remainingPart = hash.substr(0, 2);
        if (remainingPart == "#L") {
            if ($("div.logs").length == 0) {
                // meow
            } else {
                var remainingParts = hash.substr(2).split(",");
                for (var i = 0; i < remainingParts.length; i++) {
                    var part = remainingParts[i];

                    if (part.indexOf("-") !== -1) {
                        var re = /([0-9]+)-([0-9]+)/;
                        var match = re.exec(part);
                        if (match.length !== 3) {
                        } else {
                            var imin = Number(match[1]);
                            var imax = Number(match[2]);
                            for (var j = imin; j <= imax; j++) {
                                highlightLine(j);
                            }
                        }
                    } else {
                        var ival = Number(part);
                        if (isNaN(ival)) {
                            // meow
                        } else {
                            highlightLine(ival);
                        }
                    }
                }
            }
        }
    }

    $(window).on('hashchange', function() {
        hashChange();
    });

    setTimeout(hashChange, 500);
});

$(function() {
    var targets = $('[rel~=tooltip]'),
        target = false,
        tooltip = false,
        title = false;

    targets.bind('mouseenter', function() {
        target = $(this);
        tip = target.attr('title');
        tooltip = $('<div id="tooltip"></div>');

        if (!tip || tip == '')
            return false;

        target.removeAttr('title');
        tooltip.css('opacity', 0)
            .html(tip)
            .appendTo('body');

        var init_tooltip = function() {
            if ($(window).width() < tooltip.outerWidth() * 1.5)
                tooltip.css('max-width', $(window).width() / 2);
            else
                tooltip.css('max-width', 340);

            var pos_left = target.offset().left + (target.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                pos_top = target.offset().top - tooltip.outerHeight() - 20;

            if (pos_left < 0) {
                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass('left');
            } else
                tooltip.removeClass('left');

            if (pos_left + tooltip.outerWidth() > $(window).width()) {
                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass('right');
            } else
                tooltip.removeClass('right');

            if (pos_top < 0) {
                var pos_top = target.offset().top + target.outerHeight();
                tooltip.addClass('top');
            } else
                tooltip.removeClass('top');

            tooltip.css({
                left: pos_left,
                top: pos_top
            })
                .animate({
                    top: '+=10',
                    opacity: 1
                }, 50);
        };

        init_tooltip();
        $(window).resize(init_tooltip);

        var remove_tooltip = function() {
            tooltip.animate({
                top: '-=10',
                opacity: 0
            }, 50, function() {
                $(this).remove();
            });

            target.attr('title', tip);
        };

        target.bind('mouseleave', remove_tooltip);
        tooltip.bind('click', remove_tooltip);
    });
});
