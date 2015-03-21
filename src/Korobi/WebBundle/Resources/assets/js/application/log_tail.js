function pad(n) {
    n = n + '';
    return n.length >= 2 ? n : new Array(2 - n.length + 1).join('0') + n;
}

function dateToStringTimestamp(date) {
    return pad(date.getHours()) + ':' + pad(new_time.getMinutes()) + ':' + pad(new_time.getSeconds());
}

$(function() {
    var logs = $('.logs');
    if (!logs.hasClass('tailing')) {
        return;
    }

    // Translate all logs to current timezone
    var timezone_offset = new Date().getTimezoneOffset();

    logs.find('.timestamp').each(function(index, time) {
        var time_parts = time.html().split(':');
        time.html(dateToStringTimestamp(new Date(
            0, 0, 0,
            time_parts[0],
            time_parts[1] - timezone_offset,
            time_parts[2]
        )));
    });

    // Start tailing
    var lastId = logs.find('.line:last').data('event-id');

    $(document).scrollTop($("#bottom").offset().top);
    setInterval(function () {
        $.ajax({
            url: window.location.href,
            data: {last_id: lastId},
            dataType: "json",
            type: 'GET',
            success: function (data) {
                if (data.length == 0) {
                    return;
                }

                $.each(data, function (index, line) {
                    var timestamp = $('<span/>')
                        .addClass('timestamp')
                        .html(dateToStringTimestamp(new Date(line.timestamp * 1000)));
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
});
