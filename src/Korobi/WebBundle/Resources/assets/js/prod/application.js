$(function() {

    function highlightLine(lineNum) {
        console.log("Number is " + lineNum);
        var selector = ".logs--line[data-line-num='" + lineNum + "']";
        console.log("Final selector is" + selector);
        $(selector).addClass("highlighted");
    }

    function hashChange() {
        $(".logs--line").removeClass("highlighted");
        var hash = window.location.hash;
        var remainingPart = hash.substr(0, 2);
        if (remainingPart == "#L") {
            console.log("Got request to highlight lines!");
            if ($("div.logs").length == 0) {
                console.log("Not on a logs page!");
            } else {
                var remainingParts = hash.substr(2).split(",");
                console.log("Split item: " + remainingPart.substr(1));
                console.log(remainingParts);
                for (var i = 0; i < remainingParts.length; i++) {
                    console.log("Inspecting part with index " + i);
                    var part = remainingParts[i];
                    console.log("Part contents: " + part);

                    if (part.indexOf("-") !== -1) {
                        console.log("Found a hyphen in this part");
                        var re = /([0-9]+)-([0-9]+)/;
                        var match = re.exec(part);
                        if (match.length !== 3) {
                            console.log("Invalid fragment! " + part);
                        } else {
                            console.log("Min: " + match[1] + ", Max: " + match[2]);
                            var imin = Number(match[1]);
                            var imax = Number(match[2]);
                            for (var j = imin; j <= imax; j++) {
                                console.log("Highlighting " + j);
                                highlightLine(j);
                            }
                        }
                    } else {
                        console.log("No hyphen, treating as number");
                        var ival = Number(part);
                        if (isNaN(ival)) {
                            console.log("Invalid fragment! " + part)
                        } else {
                            console.log("Valid number, requesting highlight");
                            highlightLine(ival);
                        }
                    }
                }
            }
        }
    }
    $(window).on('hashchange', function() {
        hashChange();
    });
    hashChange();

});
