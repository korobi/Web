$(function() {
    $('.line .nick').each(function(i, e) {
        var width = e.scrollWidth - e.offsetWidth;
        if(width > 0) {
            $(e)
                .attr('title', e.innerText)
                .attr('rel', 'tooltip')
            ;
        }
    });
});
