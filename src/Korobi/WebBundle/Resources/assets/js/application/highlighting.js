$(function() {
    // Check if line highlighting is needed
    if(!$('.logs:not(.tailing)').length) {
        return;
    }

    var activeLines = [],
        containsLine = function(line) {
            return activeLines.indexOf(line) != -1;
        },
        /**
         * @param line
         */
        addLine = function(line) {
            // we don't want zero
            if(line === 0) {
                return;
            }

            // only add if not already in array
            if(activeLines.indexOf(line) == -1) {
                activeLines.push(line);
            }

            // add class
            $(".logs .line[data-line-num='" + line + "']").addClass('highlighted');
        },
        /**
         * @param line
         */
        removeLine = function(line) {
            // remove line from array
            var index;
            if((index = activeLines.indexOf(line)) > -1) {
                activeLines.splice(index, 1);
            }

            // remove class
            $(".logs .line[data-line-num='" + line + "']").removeClass('highlighted');
        },
        /**
         * @returns {string}
         */
        createHash = function() {
            var result = 'L'; // hash (#) is automatically added
            var first = true;

            activeLines.forEach(function(entry) {
                if(!first) {
                    result += ',';
                }

                first = false;
                result += entry;
            });

            return result;
        },
        hashChange = function() {
            $('.logs .line').removeClass('highlighted');

            var hash = window.location.hash;

            var remainingPart = hash.substr(0, 2);
            if(remainingPart == "#L") {
                if($('.logs').length !== 0) {
                    var remainingParts = hash.substr(2).split(',');
                    for(var i = 0; i < remainingParts.length; i++) {
                        var part = remainingParts[i];

                        if(part.indexOf('-') !== -1) {
                            var re = /([0-9]+)-([0-9]+)/;
                            var match = re.exec(part);
                            if(match.length === 3) {
                                var imin = Number(match[1]);
                                var imax = Number(match[2]);
                                if(imin > imax) {
                                    var tempMax = imax;
                                    imax = imin;
                                    imin = tempMax;
                                }
                                for(var j = imin; j <= imax; j++) {
                                    addLine(j);
                                }
                            }
                        } else {
                            var lineNum = Number(part);
                            if(!isNaN(lineNum)) {
                                addLine(lineNum);
                            }
                        }
                    }
                }
            }
        },
        jumpToFirstLine = function() {
            if(activeLines.length > 0) {
                var shift = Math.min.apply(Math, activeLines);
                var elem = $(".logs .line[data-line-num='" + shift + "']");
                $(window).scrollTop(elem.offset().top - 0.33 * $(window).height());
            }
        };

    // listen for hash changes
    $(window).on('hashchange', function() {
        hashChange();
    });

    // Allow lines to be highlighted by clicking the timestamp next to the log line.
    // The behaviour for clicks is as follows:
    // - Click:
    // ---- highlight single line, un-highlighting any existing highlighted lines
    // - Ctrl + Click:
    // ---- add or remove (if line is already highlighted) a line
    // - Shift + Click:
    // ---- add/remove highlighting to a group of lines
    $(document).on('click', '.logs .timestamp', function(event) {
        event.preventDefault();

        // climb the dom to .line
        var line = $(this).closest('.line').data('line-num');

        // If ctrl/command is being held while clicking, either highlight the line or remove
        // existing highlighting from the line.
        if(event.ctrlKey || event.metaKey) {
            // If the line is already highlighted, remove highlighting
            if(activeLines.indexOf(line) != -1) {
                removeLine(line);
            } else {
                addLine(line);
            }
        } else if(event.shiftKey) {
            // We'll assume the user wants to select a range here

            // I'm arbitrarily deciding that a range will be made up of the last line they added to the list
            // and the line they're clicking now.

            var lastLine = activeLines[activeLines.length - 1];
            if(activeLines.length <= 0) {
                return;
            } else if(containsLine(line)) {
                return;
            } else if(lastLine < line) {
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

            if(!alreadyExisted) {
                addLine(line);
            }
        }

        // set the new hash
        var hash = createHash();
        if(hash !== 'L') {
            window.location.hash = createHash();
        } else {
            history.pushState('', document.title, window.location.pathname + window.location.search);
        }
    });

    // delay load
    setTimeout(hashChange, 500);
    setTimeout(jumpToFirstLine, 600);
});
