$(function() {
    var $controls = $('.controls');
    var statusEvents = ['join', 'quit', 'part'];
    var hiddenEvents = [];

    if(!$controls.length) {
        return;
    }

    $controls.find('#toggle-join-part').change(function() {
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

        $('.logs .line').each(function(i, e) {
            var $e = $(e);
            var shouldHide = hiddenEvents.indexOf($e.attr('data-event-type')) !== -1;
            if(shouldHide) {
                $e.addClass('hidden');
            } else {
                $e.removeClass('hidden');
            }
        });
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
