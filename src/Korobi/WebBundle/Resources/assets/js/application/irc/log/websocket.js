(function() {
    'use strict';

    var $logs = $('#logs');

    if ($logs.length === 0 || $logs.hasClass('linkable')) {
        return;
    }

    var $empty = $logs.find('.empty');
    if($empty.length === 0) {
        $empty = null;
    }

    $(document).scrollTop($("#bottom").offset().top);

    var ws;
    var retries = 0;
    var logInfo = JSON.parse($('#log-info').text());
    var connecting = false;

    initWebSocket();

    document.addEventListener("beforeunload", function() {
        ws.close(1001);
    });

    function initWebSocket() {
        if(connecting) {
            return;
        }
        connecting = true;
        ws = new WebSocket("wss://" + document.location.hostname + ":443/ws/");

        ws.onmessage = function(e) {
            var data = JSON.parse(e.data);

            if(data._status !== "ok") {
                // Notification!
                console.error(data);
                return;
            }

            //console.log(data);

            switch(data._type.toLowerCase()) {
                case "channel_event":
                    insertLine(data);
                    break;
                default:
                    console.warn("unhandled message _type", data._type);
                    break;
            }
        };

        ws.onopen = function() {
            connecting = false;
            retries = 0;
            ws.send(JSON.stringify({
                _type: "listen_request",
                network: logInfo.network,
                channel: logInfo.channel,
            }));
        };

        ws.onerror = function(e) {
            console.error("Connection error", e);
        };

        ws.onclose = function(e) {
            if(e.code === 1001 || retries >= 3) {
                return;
            }

            connecting = false;
            // Notification!
            var time = ++retries * 5000;
            console.warn("Connection closed, retrying in " + time + "ms", e.code);
            setTimeout(initWebSocket, time);
        };
    }

    function insertLine(data) {
        var info = {
            time: new Date(data.date * 1000),
            nick: data.actorName,
            message: data.message,
        };
        $logs.trigger('new_line', info);

        var timestamp = $('<span/>')
            .addClass('timestamp')
            .text(dateToStringTimestamp(info.time));
        var nick = $('<span/>')
            .addClass('nick ' + data.actorPrefix)
            .text(info.nick);
        var message = $('<span/>')
            .addClass('message')
            .html(info.message);
        $logs.append(
            $('<div/>')
                .attr({
                    'data-nick': info.nick,
                    //'data-event-id': line.id,
                    'data-event-type': data.type.toLowerCase()
                })
                .addClass('line')
                .append(timestamp)
                .append(nick)
                .append(message)
        );
        $(document).scrollTop($("#bottom").offset().top);
    }
})();
