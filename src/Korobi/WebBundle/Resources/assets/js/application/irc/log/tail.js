$(function() {
    'use strict';

    var $logs = $('#logs');

    // Start tailing
    if ($logs.length === 0 || $logs.hasClass('linkable')) {
        return;
    }

    var $empty = $logs.find('.empty');
    if($empty.length === 0) {
        $empty = null;
    }

    var lastId = $logs.find('.line:last').data('event-id');

    $(document).scrollTop($("#bottom").offset().top);
    setInterval(function () {
        $.ajax({
            url: window.location.href,
            data: {last_id: lastId},
            dataType: "json",
            type: 'GET',
            success: function (data) {
                if (data.length === 0) {
                    return;
                }

                if($empty !== null) {
                    $empty.remove();
                    $empty = null;
                }

                var info;
                $.each(data, function (index, line) {
                    info = {
                        time: new Date(line.timestamp * 1000),
                        nick: line.displayNick,
                        message: line.message,
                    };
                    $logs.trigger('new_line', info);

                    var timestamp = $('<span/>')
                        .addClass('timestamp')
                        .text(dateToStringTimestamp(info.time));
                    var nick = $('<span/>')
                        .addClass('nick irc--' + pad(line.nickColour) + '-df ' + line.role)
                        .text(info.nick);
                    var message = $('<span/>')
                        .addClass('message')
                        .html(info.message);
                    $logs.append(
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
