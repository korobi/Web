$(function() {
    var $debugToolbar = $(".sf-toolbar");
    if ($debugToolbar.length != 0) {
        console.log($debugToolbar.css("display"));
        $(".footer").addClass("footer--debug");
    }

    function toggleFooterDebugMode() {
        $(".footer").toggleClass("footer--debug");
    }

    $("body").on("click", ".hide-button, a[title='Show Symfony toolbar']", function() {
        toggleFooterDebugMode();
    });

});
