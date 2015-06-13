$(function() {
    var $controls = $('.controls');

    if(!$controls.length) {
        return;
    }

    var $toggles = $controls.find('.toggles');
    console.log($toggles);
    $toggles.find('input').hide(0).change(function(e) {
        $toggles
            .find('label[for=' + $(this).prop('id') + ']')
            .toggleClass('active')
            ;
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
