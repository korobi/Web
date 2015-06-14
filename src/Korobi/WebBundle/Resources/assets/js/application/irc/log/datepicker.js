$(function() {
    var moving = false;
    var availableDays = JSON.parse($('#available-log-days').text());
    var $dateInput = $('#log-date');
    $dateInput.datepicker({
        format: "yyyy/mm/dd",
        endDate: "today",
        todayBtn: "linked",
        language: "en-GB",
        orientation: "top right",
        forceParse: false,
        beforeShowDay: function (date) {
            for(var i = 0; i < availableDays.length; ++i) {
                if(date.getDate() === availableDays[i].day
                        && date.getMonth() + 1 === availableDays[i].month
                        && date.getYear() + 1900 === availableDays[i].year) {
                    return 'has-data';
                }
            }
            return true;
        }
    });
    $('label[for=log-date]').click(function() {
        $dateInput.datepicker('show');
    });

    $dateInput.change(function() {
        if(moving) {
            return;
        }
        moving = true;
        window.location = window.location.href.replace(/logs\/?.*/, 'logs/' + $dateInput.val());
    })
});
