function pad(n) {
    n = n + '';
    return n.length >= 2 ? n : new Array(2 - n.length + 1).join('0') + n;
}

$(function() {

    var logs = $('.logs');
    if (logs.hasClass('tailing')) {
        var lastId = logs.find('.line:last').data('event-id');

        $(document).scrollTop($("#bottom").offset().top);
        setInterval(function() {
            $.ajax({
                url: window.location.href,
                data: { last_id: lastId },
                dataType: "json",
                type: 'GET',
                success: function(data) {
                    if(data.length == 0) {
                        return;
                    }

                    $.each(data, function(index, line) {
                        var timestamp = $('<span/>')
                            .addClass('timestamp')
                            .html(line.timestamp);
                        var nick = $('<span/>')
                            .addClass('nick irc--' + pad(line.nickColour) + '-df ' + line.role)
                            .html(line.nick);
                        var message = $('<span/>')
                            .addClass('message')
                            .html(line.message);
                        logs.append(
                            $('<div/>')
                                .attr({
                                    'data-nick': line.nick,
                                    'data-event-id': line.id,
                                    'data-event-type': line.type
                                })
                                .addClass('line')
                                .append(timestamp)
                                .append(nick)
                                .append(message)
                        );
                    });
                    lastId = data.slice(-1)[0].id;
                    $(document).scrollTop($("#bottom").offset().top);
                }
            });
        }, 5000);
    }
});
