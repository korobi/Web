$(function() {

    function highlightLine(lineNum) {
        lineNum -= 1;
        $(".logs--line[data-line-num='" + lineNum + "']").addClass("highlighted");
    }

    var hash = window.location.hash;
    var remainingPart = hash.substr(0, 2);
    if (remainingPart == "#L") {
        console.log("Got request to highlight lines!");
        if ($("div.logs").length == 0) {
            console.log("Not on a logs page!");
        } else {
            var remainingParts = remainingPart.substr(2).split(",");
            for (var i = 0; i < remainingParts.length; i++) {
                var part = remainingParts[i];
                if (part.indexOf("-") !== -1) {
                    var re = /([0-9]+)-([0-9]+)/;
                    var match = re.exec(part);
                    if (match.length !== 3) {
                        console.log("Invalid fragment! " + part);
                    } else {
                        console.log("Min: " + match[1] + ", Max: " + match[2]);
                        var imin = Number(match[1]);
                        var imax = Number(match[1]);
                        for (var j = imin; j < imax; j++) {
                            highlightLine(j);
                        }
                    }
                } else {
                    var ival = Number(part);
                    if (isNaN(ival)) {
                        console.log("Invalid fragment! " + part)
                    } else {
                        highlightLine(ival);
                    }
                }
            }
        }
    }
});
