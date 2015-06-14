$(function() {
    // Used in conjunction with max-width and text-overflow on .nick
    $('.line .nick').each(function(i, e) {
        if(e.scrollWidth - e.offsetWidth > 0) {
            $(e)
                .attr('title', $(e).text())
                .attr('rel', 'tooltip')
            ;
        }
    });
});
