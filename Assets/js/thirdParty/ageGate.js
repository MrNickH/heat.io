(function ($) {

    $.fn.ageGate = function (settings) {

        var that = this, config = {
            'legal_age': 18,
            'required_fields_msg': 'All fields are mandatory',
            'underage_msg': 'You have to be over 18 to enter this site',
            'underage_url': 'http://google.com',
        };

        if (settings) {
            $.extend(config, settings);
        }

        //underage
        that.illegalAge = function () {
            $('#age_gate_error_message').html('<p>' + config.underage_msg + '</p>');
            $('form#age_gate_form').remove();
            error = true;

            setTimeout(function () {
                window.location.href = config.underage_url;
            }, 1000);
        }

        //Set Local Storage for Remember me Checked or Unchecked option
        that.rememberMe = function () {
            localStorage.setItem('remember_me', '1');
        }

        //Enable autotab
        that.autoTab = function (el) {
            if (el.val().length == el.attr('maxlength')) {
                tabindex = parseInt(el.attr('tabindex')) + 1;
                $('[tabindex=' + tabindex + ']').focus();
            }

        }
        //Age Calculation
        that.AgeCheck = function (day, month, year) {
            var dob = new Date(year + '-' + month + '-' + day);
            var today = new Date();
            var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
            return age;
        }

        //mobile Keyboard
        that.mobileKeyboard = function () {
            if ((/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
                $('#age_gate_form .numeric').each(function (index, el) {
                    $(this).replaceWith($(this).clone().attr('type', 'tel'));
                });
            }
        }

        //Placeholder reset
        that.placeholderReset = function () {
            $('#age_gate_form .numeric').each(function (index, el) {
                //data placeholder
                $(this).data('placeholder', $(this).attr('placeholder'));

                //show and hide placeholder
                placeholder = $(this).attr('placeholder');

                $(this).focus(function (event) {
                    $(this).val('').attr('placeholder', '');
                });

                $(this).blur(function (event) {
                    $(this).attr('placeholder', $(this).data('placeholder'));
                });

            });
        }

        //Validation accept only numbers
        that.numericValidation = function () {
            $(document).on('keyup', '#age_gate_form .numeric', function () {
                var numericheck = $.isNumeric($(this).val());

                var val = $(this).val();

                if (!numericheck) {
                    $(this).val('');
                }
            });
        }

        //Date Validations
        that.dateValidations = function () {
            //Day date Validation limit day no more than 31
            $(document).on('keyup', '#age_gate_day', function () {
                if ($(this).val() > 31) {
                    $(this).val('').focus();
                    return false;
                }

                that.autoTab($(this));
            });
            //Day date Validation limit day no more than 31
            $(document).on('blur', '#age_gate_day', function () {
                if ($(this).val() > 31) {
                    $(this).val('');
                    return false;
                }
            });

            //Mont date validation limit month no more than 12
            $(document).on('blur', '#age_gate_month', function () {
                if ($(this).val() > 12) {
                    $(this).val('');
                    return false;
                }
            });

            $(document).on('keyup', '#age_gate_month', function () {
                if ($(this).val() > 12) {
                    $(this).val('').focus();
                    return false;
                }

                that.autoTab($(this));
            });

            //Year validation limit year no more than current
            $(document).on('keyup', '#age_gate_year', function () {
                var current = new Date().getFullYear();

                if ($(this).val() > current - 18) {
                    $(this).val('').focus();
                    return false;
                }

                that.autoTab($(this));
            });

            $(document).on('blur', '#age_gate_year', function () {
                var current = new Date().getFullYear();

                if ($(this).val() > current - 18) {
                    $(this).val('');
                    return false;
                }
            });
        }

        //Submit function
        that.ageGateSubmit = function () {
            $('#age_gate_form a.submit').focus().click(function (event) {
                event.preventDefault();

                //Required Fields Validation
                $('.required').each(function (index, el) {

                    switch ($(this).val()) {
                        case "":
                        case $(this).attr('placeholder'):
                            $(this).css({
                                'background-color': '#FF9F9F',
                                'color': '#CC3333'
                            });
                            error = true;
                            break;
                        default:
                            $(this).css({
                                'background-color': '#B8F5B1',
                                'color': '#000'
                            });

                            error = false;
                    }
                });

                //legal Age Validation
                var age = that.AgeCheck($('#age_gate_day').val(), $('#age_gate_month').val(), $('#age_gate_year').val());

                if (isNaN(age)) {
                    return;
                }

                if (age < config.legal_age) {
                    that.illegalAge();
                    return;
                }

                setCookie('age_gate', 'legal', 10000);
                localStorage.setItem('remember_me', '1');
                window.location.href = window.location.href;
            });
        }

        //cookie & Local Storage Reset
        that.storageCookieReset = function () {
            localStorage.removeItem('remember_me');
            setCookie('age_gate', null);
        }

        //Chek legal Age underage & set cookie
        that.checkAgeOk = function () {
            if (getCookie('age_gate') && getCookie('age_gate') === 'underage') {

                if (localStorage.getItem('remember_me')) {
                    localStorage.removeItem('remember_me');
                }

                that.illegalAge();
            }
        }

        //Returns actual Domain
        that.getDomain = function () {
            return $(location).attr('hostname');
        }

        //Check remember me
        that.checkRememberMe = function () {
            if (localStorage.getItem('remember_me') && localStorage.getItem('remember_me') === '1') {
                that.setLocation();
            }
        }

        //Build HTML Structure with functionality
        that.buildAgegate = function () {
            that.checkAgeOk();
            that.mobileKeyboard();
            that.placeholderReset();
            that.numericValidation();
            that.dateValidations();
            that.ageGateSubmit();
        }

        that.setLocation = function (location = '') {
            window.location = '//' + that.getDomain() + '/' + location;
        }

        that.buildAgegate();
        return that;
    };
})(jQuery);