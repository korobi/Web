$(function() {
    var $controls = $('.controls');
    var statusEvents = ['join', 'quit', 'part'];

    if(!$controls.length) {
        return;
    }

    var $joinPartLabel = $controls.find('label[for=toggle-join-part]');
    $controls.find('#toggle-join-part').change(function(e) {
        var $this = $(this);

        $('.logs .line').each(function(i, e) {
            var $e = $(e);
            var isStatus = statusEvents.indexOf($e.attr('data-event-type')) > -1;
            if(isStatus) {
                if($this.prop('checked')) {
                    $e.removeClass('hidden');
                } else {
                    $e.addClass('hidden');
                }
            }
        })
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
