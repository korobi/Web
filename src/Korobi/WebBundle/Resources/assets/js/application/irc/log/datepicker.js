$(function() {
    'use strict';

    var $dateInput = $('#log-date');
    if($dateInput.length === 0) {
        return;
    }

    var moving = false;
    var availableDays = JSON.parse($('#available-log-days').text());

    function isLeapYear(date) {
        var yr = date.getFullYear();
        return !((yr % 4) || (!(yr % 100) && (yr % 400)));
    }
    function dayOfYear(date) {
        var _dayOfYearOffsets = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

        var m = date.getMonth();
        var d = date.getDate();
        if(isLeapYear(date) && m > 1) {
            return _dayOfYearOffsets[m] + d + 1;
        }
        return _dayOfYearOffsets[m] + d;
    }

    $dateInput.datepicker({
        format: 'yyyy/mm/dd',
        startDate: $dateInput.attr('data-start-date'),
        endDate: 'today',
        todayBtn: 'linked',
        language: 'en-GB',
        orientation: 'top right',
        forceParse: false,
        beforeShowDay: function(date) {
            for(var i = 0; i < availableDays.length; ++i) {
                if(dayOfYear(date) === availableDays[i].day_of_year
                        && date.getFullYear() === availableDays[i].year) {
                    return availableDays[i].has_valid_content ? 'has-data' : true;
                }
            }
            return false;
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
