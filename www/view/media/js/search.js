var Search = function () {

    return {
        //main function to initiate the abmodule
        init: function () {
            if (jQuery().datepicker) {
                $('.date-picker').datepicker();
            }

            App.initFancybox();
        }

    };

}();