function pad(n) {
    n = n + '';
    return n.length >= 2 ? n : new Array(2 - n.length + 1).join('0') + n;
}

var logs = $('.logs');
if (logs.data('is-tail')) {
    var lastId = logs.data('tail-last-id');

    $(document).scrollTop($("#bottom").offset().top);
    setInterval(function() {
        $.ajax({
            url: window.location.href,
            data: { last_id: lastId },
            type: 'GET',
            success: function(data) {
                var json = JSON.parse(data);
                if(json.length == 0) {
                    return;
                }

                $.each(json, function(index, line) {
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
                lastId = json.slice(-1)[0].id;
                $(document).scrollTop($("#bottom").offset().top);
            }
        });
    }, 5000);
}
