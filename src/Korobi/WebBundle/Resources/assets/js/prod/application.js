/**
 * Line highlighting
 */
$(function() {
    var activeLines = [],
        /**
         * @param line
         */
        addLine = function(line) {
            // we don't want zero
            if (line === 0) {
                return;
            }

            // only add if not already in array
            if (activeLines.indexOf(line) == -1) {
                activeLines.push(line);
            }

            // add class
            $(".logs--line[data-line-num='" + line + "']").addClass('highlighted');
        },
        /**
         * @param line
         */
        removeLine = function(line) {
            // remove line from array
            var index;
            if ((index = activeLines.indexOf(line)) > -1) {
                activeLines.splice(index, 1);
            }

            // remove class
            $(".logs--line[data-line-num='" + line + "']").removeClass('highlighted');
        },
        /**
         * @returns {string}
         */
        createHash = function() {
            var result = 'L'; // hash (#) is automatically added
            var first = true;

            activeLines.forEach(function(entry) {
                if (!first) {
                    result += ',';
                }

                first = false;
                result += entry;
            });

            return result;
        },
        hashChange = function() {
            $('.logs--line').removeClass('highlighted');

            var hash = window.location.hash;

            var remainingPart = hash.substr(0, 2);
            if (remainingPart == "#L") {
                if ($('div.logs').length !== 0) {
                    var remainingParts = hash.substr(2).split(',');
                    for (var i = 0; i < remainingParts.length; i++) {
                        var part = remainingParts[i];

                        if (part.indexOf('-') !== -1) {
                            var re = /([0-9]+)-([0-9]+)/;
                            var match = re.exec(part);
                            if (match.length === 3) {
                                var imin = Number(match[1]);
                                var imax = Number(match[2]);
                                if (imin > imax) {
                                    var tempMax = imax;
                                    imax = imin;
                                    imin = tempMax;
                                }
                                for (var j = imin; j <= imax; j++) {
                                    addLine(j);
                                }
                            }
                        } else {
                            var lineNum = Number(part);
                            if (!isNaN(lineNum)) {
                                addLine(lineNum);
                            }
                        }
                    }
                }
            }
        },
        jumpToFirstLine = function() {
            if (activeLines.length > 0) {
                var shift = Math.min.apply(Math, activeLines);
                var elem = $(".logs--line[data-line-num='" + shift + "']");
                $(window).scrollTop(elem.offset().top - 0.33 * $(window).height());
            }
        };

    // listen for hash changes
    $(window).on('hashchange', function() {
        hashChange();
    });

    // allow adding and removing lines using the icon beside the log line
    $(document).on('mousedown', '.js-hl .fa', function(event) {
        event.preventDefault();

        // grab parent (.fa is a child)
        var line = $(this).parent().data('line-num');

        // if ctrl is being held, add another line
        if (event.ctrlKey) {
            // if we're holding ctrl and the line exists, remove it
            if (activeLines.indexOf(line) != -1) {
                removeLine(line);
            } else {
                addLine(line);
            }
        } else if (event.shiftKey) {
            // We'll assume the user wants to select a range here

            // I'm arbitrarily deciding that a range will be made up of the last line they added to the list
            // and the line they're clicking now.

            var lastLine = activeLines[activeLines.length - 1];
            if (lastLine < line) {
                // The just-selected line is in front of the last selected line. Great, we'll
                // highlight all of the lines in between.

                window.location.hash = window.location.hash + "," + lastLine + "-" + line;
                hashChange();
                return;
            } else {
                // The last line is after the one the user just selected. We'll start at the selected line
                // and step to the next.

                window.location.hash = window.location.hash + "," + line + "-" + lastLine;
                hashChange();
                return;
            }


        } else {
            var alreadyExisted = (activeLines.indexOf(line) != -1 && activeLines.length === 1);
            // ctrl is not being held, remove all and add new
            activeLines.forEach(function(entry) {
                removeLine(entry);
            });
            activeLines = [];

            if (!alreadyExisted) {
                addLine(line);
            }
        }

        // set the new hash
        var hash = createHash();
        if (hash !== 'L') {
            window.location.hash = createHash();
        } else {
            window.location.hash = '';
        }
    });

    // delay load
    setTimeout(hashChange, 500);
    setTimeout(jumpToFirstLine, 600);
});

/**
 * Tooltips
 */
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

/**
 * Permissions
 */
$(function() {
    $('#permission-add').click(function(event) {
        event.preventDefault();

        var permissionList = $('#permissions');
        var permission = permissionList.attr('data-prototype');
        permission = permission.replace(/__name__/g, permissionCount);
        var permissionLi = $('<tr data-content="form_permissions_' + permissionCount + '"></tr>').html(
            '<td>' + permission + '</td><td><a href="#" id="permission-del" data-related="form_permissions_' + permissionCount + '">Remove</a></td>'
        );
        permissionLi.appendTo(permissionList);

        permissionCount++;
    });

    $('body').on('click', 'a', function(event) {
        if (event.currentTarget.text == 'Remove') {
            console.log(event);
            event.preventDefault();
            var id = $(this).attr('data-related');
            $('*[data-content="' + id + '"]').remove();
            permissionCount--;
        }
    });
});

/**
 * Responsive nav
 */
$(function() {
    $(".navigation--menutoggle").click(function() {
        $("nav.navigation").toggleClass("on-mobile");
    });
});

