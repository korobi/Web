$(function() {
    var $toolbar = $('.sf-toolbar');

    function getSymfonyPreference(name) {
        if(!window.localStorage) {
            return null;
        }

        return localStorage.getItem('sf2/profiler/' + name);
    }

    var $body = $('body');

    if($toolbar.length != 0) {
        var displayState = getSymfonyPreference('toolbar/displayState');
        if(displayState === null || displayState == 'block') {
            $('.footer').addClass('footer--debug');
            $body.addClass('open-debug');
        }
    }

    function toggleFooterDebugMode() {
        var $debugToolbar = $('.sf-minitoolbar');
        if($debugToolbar.css('display') != 'none') {
            $('.footer').removeClass('footer--debug');
            $('body').removeClass('open-debug');
        } else {
            $('.footer').addClass('footer--debug');
            $('body').addClass('open-debug');
        }
    }

    $body.on('click', ".hide-button, a[title='Show Symfony toolbar']", function() {
        toggleFooterDebugMode();
    });
});
