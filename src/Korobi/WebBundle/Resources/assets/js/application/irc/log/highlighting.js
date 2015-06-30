$(function() {
    'use strict';

    var $logs = $('#logs');

    // Check if line highlighting is needed
    if($logs.is('.tailing')) {
        return;
    }

    var activeLines;
    var lastSelection = null;

    /**
     * @returns {string}
     */
    function createHash() {
        var result = 'L'; // hash (#) is automatically added
        var first = true;
        var lastAdded = null;
        var rangeCreated = false;

        activeLines.sort(function(e1, e2) { return e1 - e2; }).forEach(function(entry) {
            if(first) {
                first = false;
                result += entry;
                lastAdded = entry;
                return;
            }

            if(entry === lastAdded + 1) {
                if(!rangeCreated) {
                    rangeCreated = true;
                    result += '-';
                }
            } else {
                if(rangeCreated) {
                    result += lastAdded + ',' + entry;
                    rangeCreated = false;
                } else {
                    result += ',' + entry;
                }
            }

            lastAdded = entry;
        });

        if(rangeCreated) {
            result += lastAdded;
        }

        return result;
    }

    function highlightLines(lines) {
        $logs.find('.highlighted').removeClass('highlighted');
        lines.forEach(function(line) {
            $logs.find('.line[data-line-num=' + line + ']').addClass('highlighted');
        });
    }

    function parseHash() {
        var result = [];
        if($logs.length === 0) {
            return result;
        }

        var hash = window.location.hash;
        var remainingPart = hash.substr(0, 2);
        if(remainingPart != "#L") {
            return result;
        }

        var remainingParts = hash.substr(2).split(',');
        var number;
        for(var i = 0; i < remainingParts.length; ++i) {
            var part = remainingParts[i];

            if(part.indexOf('-') === -1) {
                number = Number(part);
                if(!isNaN(number) && isFinite(number)) {
                    result.push(number);
                }
            } else {
                var match = part.split(/-/);
                if(match.length !== 2) {
                    continue;
                }

                var min = Number(match[0]);
                var max = Number(match[1]);
                if(min > max) {
                    var temp = max;
                    max = min;
                    min = temp;
                }

                for(var j = min; j <= max; j++) {
                    result.push(j);
                }
            }
        }

        return result;
    }

    function jumpToFirstLine(activeLines) {
        if(activeLines.length > 0) {
            var shift = Math.min.apply(Math, activeLines);
            var elem = $logs.find('.line[data-line-num=' + shift + ']');
            $(window).scrollTop(elem.offset().top - 0.05 * $(window).height());
        }
    }

    // Allow lines to be highlighted by clicking the timestamp next to the log line.
    // The behaviour for clicks is as follows:
    // - Click:
    // ---- highlight single line, un-highlighting any existing highlighted lines
    // - Ctrl + Click:
    // ---- add or remove (if line is already highlighted) a line
    // - Shift + Click:
    // ---- add/remove highlighting to a group of lines
    $logs.find('.timestamp').mousedown(function(e) {
        if(e.which === 3) {
            return;
        }
        e.preventDefault();

        // climb the dom to .line
        var line = $(this).closest('.line').data('line-num');
        var index;

        // If ctrl/command is being held while clicking, either highlight the line or remove
        // existing highlighting from the line.
        if(e.ctrlKey || e.metaKey) {
            if((index = activeLines.indexOf(line)) === -1) {
                activeLines.push(line);
            } else {
                activeLines.splice(index, 1);
            }
            lastSelection = line;

        } else if(e.shiftKey) {
            if(lastSelection === null) {
                // The user never clicked before
                activeLines = [line];
                lastSelection = line;
            } else {
                var first = Math.min(lastSelection, line);
                var last =  Math.max(lastSelection, line);
                while(first <= last) {
                    if((index = activeLines.indexOf(first)) === -1) {
                        activeLines.push(first);
                    }
                    ++first;
                }
            }

        } else {
            if(activeLines.length === 1 && activeLines[0] === line) {
                // The user just clicked the currently selected line
                return;
            }

            activeLines = [line];
            lastSelection = line;
        }

        highlightLines(activeLines);

        // set the new hash
        var hash = createHash();
        if(hash === 'L') {
            // No lines selected, remove the hash
            history.pushState('', document.title, window.location.pathname + window.location.search);
        } else {
            window.location.hash = createHash();
        }
    });

    $(window).on('hashchange', function() {
        activeLines = parseHash();
        highlightLines(activeLines);
    });

    // delay load
    activeLines = parseHash();
    setTimeout(function() {
        highlightLines(activeLines);
    }, 1000);
    jumpToFirstLine(activeLines);
});
