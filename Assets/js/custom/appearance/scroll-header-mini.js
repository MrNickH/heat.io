$(function () {
    if ($(window).width() > 700) {
        return;
    }

    $(window).on("scroll", function() {
        if($(window).scrollTop() == 0) {
            $("header").removeClass("mini");
        } else {
            $("header").addClass("mini");
        }
    })
});