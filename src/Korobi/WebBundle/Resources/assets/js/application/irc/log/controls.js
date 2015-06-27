function pad(n) {
    return n > 9 ? n.toString() : '0' + n;
}

function dateToStringTimestamp(date) {
    return pad(date.getHours())
        + ':' + pad(date.getMinutes())
        + ':' + pad(date.getSeconds());
}

$(function() {
    'use strict';

    function translateTimezones(add) {
        $logs.find('.timestamp').each(function(index, time) {
            var $time = $(time);
            var timeParts = $time.text().trim().split(':');
            $time.text(dateToStringTimestamp(new Date(
                0, 0, 0,
                timeParts[0],
                parseInt(timeParts[1]) + (add ? 1 : -1) * timezoneOffset,
                timeParts[2]
            )));
        });
    }

    var $controls = $('.controls');
    if(!$controls.length) {
        return;
    }

    var $logs = $('#logs');

    function hideEvents(events) {
        $logs.attr('data-event-hidden', events.join(' '));
    }

    var $toggleChatTypes = $controls.find('.check_option');
    var eventsKey = 'korobi-hidden-events';
    var hiddenEvents = [];

    var $toggleLocalTimezoneInput = $controls.find('#toggle-timezone');
    var timezoneKey = 'korobi-use-local-timezone';
    var useLocalTimezone = true;
    var timezoneOffset = new Date().getTimezoneOffset();

    // Load local storage config
    var fromStorage;
    if(window.localStorage) {
        if(fromStorage = localStorage.getItem(eventsKey)) {
            try {
                hiddenEvents = JSON.parse(fromStorage);
                hideEvents(hiddenEvents);
            } catch (ex) {
                localStorage.removeItem(eventsKey);
            }
        }

        if(fromStorage = localStorage.getItem(timezoneKey)) {
            useLocalTimezone = fromStorage === '1' || fromStorage === undefined;
        }
    }

    if(useLocalTimezone) {
        // Translate all timezones
        translateTimezones(false);
    } else {
        $toggleLocalTimezoneInput.prop('checked', false);
    }

    // Status event toggle
    $.each($toggleChatTypes, function(i, e){
        $(e).change(function() {
            var $this = $(this);
            var $type = $this.data('type');
            if($this.prop('checked')) {
                var index;
                if((index = hiddenEvents.indexOf($type)) !== -1) {
                    hiddenEvents.splice(index, 1);
                }
            } else {
                hiddenEvents.push($type);
            }

            if(window.localStorage) {
                localStorage.setItem(eventsKey, JSON.stringify(hiddenEvents));
            }

            hideEvents(hiddenEvents);
        });
    });


    // Local timezone toggle
    $toggleLocalTimezoneInput.change(function() {
        useLocalTimezone = $(this).prop('checked');
        translateTimezones(!useLocalTimezone);

        if(window.localStorage) {
            if(useLocalTimezone) {
                localStorage.setItem(timezoneKey, 1);
            } else {
                localStorage.setItem(timezoneKey, 0);
            }
        }
    });

    $logs.on('new_line', function(e, line) {
        if(!useLocalTimezone) {
            line.time.setMinutes(line.time.getMinutes() + timezoneOffset);
        }
    });
});
