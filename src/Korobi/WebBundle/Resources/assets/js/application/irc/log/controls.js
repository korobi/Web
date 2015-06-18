$(function() {
    'use strict';

    var $controls = $('.controls');
    if(!$controls.length) {
        return;
    }

    function hideEvents(events) {
        $('#logs').attr('data-event-hidden', events.join(' '));
    }

    var $toggleJoinPartInput = $controls.find('#toggle-join-part');
    var localStorageKey = 'korobi-hidden-events';
    var statusEvents = ['join', 'quit', 'part'];
    var hiddenEvents = [];
    var fromStorage;

    if(window.localStorage && (fromStorage = localStorage.getItem(localStorageKey))) {
        try {
            hiddenEvents = JSON.parse(fromStorage);
            var joinPartHidden = statusEvents.every(function(e) { return hiddenEvents.indexOf(e) !== -1; });
            $toggleJoinPartInput.prop('checked', !joinPartHidden);
            hideEvents(hiddenEvents);
        } catch(ex) {
            localStorage.removeItem(localStorageKey);
        }
    }

    $toggleJoinPartInput.change(function() {
        var $this = $(this);
        if($this.prop('checked')) {
            hiddenEvents = hiddenEvents.filter(function(e) {
                return statusEvents.indexOf(e) === -1;
            });
        } else {
            hiddenEvents = hiddenEvents
                .concat(statusEvents)
                .filter(function(e, i, arr) {
                    return arr.indexOf(e) === i;
                });
        }
        localStorage.setItem(localStorageKey, JSON.stringify(hiddenEvents));
        hideEvents(hiddenEvents);
    });

    var $dateSelectorLabel = $controls.find('label[for=log-date]');
    $dateSelectorLabel.click(function(e) {
        // TODO Display date picker
    });

    var $dateSelector = $controls.find('#log-date');
    $dateSelector.change(function(e) {
        // TODO Redirect
    });
});
