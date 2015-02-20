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

        // if shift is being held, add another line
        if (event.shiftKey) {
            // if we're shifting and the line exists, remove it
            if (activeLines.indexOf(line) != -1) {
                removeLine(line);
            } else {
                addLine(line);
            }
        } else {
            // shift is not being held, remove all and add new
            activeLines.forEach(function(entry) {
                removeLine(entry);
            });
            activeLines = [];

            addLine(line);
        }

        // set the new hash
        window.location.hash = createHash();
    });

    // delay load
    setTimeout(hashChange, 500);
    setTimeout(jumpToFirstLine, 600);
});

$(function() {
    $('[data-toggle="tooltip"]').tooltip({
        'html': true
    });
});

$(document).ready(function() {
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
