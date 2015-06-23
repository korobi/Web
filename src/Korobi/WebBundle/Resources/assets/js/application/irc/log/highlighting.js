$(function() {
    'use strict';

    var $logs = $('#logs');

    // Check if line highlighting is needed
    if($logs.is('.tailing')) {
        return;
    }

    var activeLines = [];
    var activeNick = null;
    var lastSelection = null;

    /**
     * @returns {string}
     */
    function createHash() {
        var result = '';
        var first = true;
        var lastAdded = null;
        var rangeCreated = false;

        activeLines.sort(function(e1, e2) { return e1 - e2; }).forEach(function(entry) {
            if(first) {
                first = false;
                result = 'lines=' + entry;
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

        if(activeNick) {
            if(!first) {
                result += '&';
            }
            result += 'nick=' + activeNick;
        }

        return result;
    }

    function highlight() {
        $logs.find('.highlighted').removeClass('highlighted');

        if(activeNick) {
            $logs.find('.line[data-nick=' + activeNick + ']').addClass('highlighted');
        }

        activeLines.forEach(function(line) {
            $logs.find('.line[data-line-num=' + line + ']').addClass('highlighted');
        });
    }

    function parseHash() {
        activeLines = [];
        activeNick = null;
        if($logs.length === 0) {
            return;
        }

        var hash = window.location.hash;
        if(hash[0] === '#') {
            hash = hash.substr(1);
        }

        if(hash.length === 0) {
            return;
        }

        // Legacy #L1-3,5 urls
        if(hash[0] === 'L') {
            parseLineNumbers(hash.substr(1));
            return;
        }

        var parts = hash.split('&');
        for(var i = 0; i < parts.length; ++i) {
            var pair = parts[i].split('=');
            if(pair[0] === 'lines') {
                parseLineNumbers(pair[1]);
            } else if(pair[0] === 'nick') {
                activeNick = pair[1];
            }
        }
    }

    function parseLineNumbers(numbers) {
        var parts = numbers.split(',');
        var number;
        for(var i = 0; i < parts.length; ++i) {
            var part = parts[i];

            if(part.indexOf('-') === -1) {
                number = Number(part);
                if(!isNaN(number) && isFinite(number)) {
                    activeLines.push(number);
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
                    activeLines.push(j);
                }
            }
        }
    }

    function jumpToLine(line) {
        if(line === undefined) {
            return;
        }
        var $elem = $logs.find('.line[data-line-num=' + line + ']');
        $(window).scrollTop($elem.offset().top - 0.05 * $(window).height());
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
            if(activeLines.length === 1 && activeLines[0] === line && activeNick === null) {
                // The user just clicked the currently selected line
                return;
            }

            activeNick = null;
            activeLines = [line];
            lastSelection = line;
        }

        highlight();

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
        parseHash();
        highlight();
    });

    // delay load
    parseHash();
    setTimeout(function() {
        highlight();
    }, 1000);
    jumpToLine(activeLines[0]);
});
