/**
 * downCount: Simple Countdown clock with offset
 * Author: Sonny T. <hi@sonnyt.com>, sonnyt.com
 */

(function ($) {

    $.fn.downCount = function (options, callback) {
        var settings = $.extend({
                date: null,
                offset: null
            }, options);

        // Throw error if date is not set
        if (!settings.date) {
            $.error('Date is not defined.');
        }

        // Throw error if date is set incorectly
        if (!Date.parse(settings.date)) {
            $.error('Incorrect date format, it should look like this, 12/24/2012 12:00:00.');
        }

        // Save container
        var container = this;

        /**
         * Change client's local date to match offset timezone
         * @return {Object} Fixed Date object.
         */
        var currentDate = function () {
            // get client's current date
            var date = new Date();

            // turn date to utc
            var utc = date.getTime() + (date.getTimezoneOffset() * 60000);

            // set new Date object
            var new_date = new Date(utc + (3600000*settings.offset))

            return new_date;
        };

        /**
         * Main downCount function that calculates everything
         */
        function countdown () {
            var target_date = new Date(settings.date), // set target date
                current_date = currentDate(); // get fixed current date

            // difference of dates
            var difference = target_date - current_date;

            // if difference is negative than it's pass the target date
            if (difference < 0) {
                // stop timer
                clearInterval(interval);

                if (callback && typeof callback === 'function') callback();

                return;
            }

            // basic math variables
            var _second = 1000,
                _minute = _second * 60,
                _hour = _minute * 60,
                _day = _hour * 24;

            // calculate dates
            var days = Math.floor(difference / _day),
                hours = Math.floor((difference % _day) / _hour),
                minutes = Math.floor((difference % _hour) / _minute),
                seconds = Math.floor((difference % _minute) / _second);

                // fix dates so that it will show two digets
                days = (String(days).length >= 2) ? days : '' + days;
                hours = (String(hours).length >= 2) ? hours : '' + hours;
                minutes = (String(minutes).length >= 2) ? minutes : '' + minutes;
                seconds = (String(seconds).length >= 2) ? seconds : '' + seconds;

            // based on the date change the refrence wording
            var ref_days = (days === 1) ? 'д' : 'д',
                ref_hours = (hours === 1) ? 'ч' : 'ч',
                ref_minutes = (minutes === 1) ? 'м' : 'м',
                ref_seconds = (seconds === 1) ? 'с' : 'с';

            // set to DOM
            container.find('.clock-o').html('<i class="fa fa-clock-o"></i>');
            container.find('.days').text(days+ref_days);
            container.find('.hours').text(hours+ref_hours);
            container.find('.minutes').text(minutes+ref_minutes);
            container.find('.seconds').text(seconds+ref_seconds);
        };
        
        // start
        var interval = setInterval(countdown, 1000);
    };

})(jQuery);