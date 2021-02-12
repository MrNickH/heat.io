function addTimer(textspace, countdown) {
    setInterval(function () {

        var distance = countdown - new Date().getTime();


        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        $(textspace).text(days + "d " + hours + "h " + minutes + "m " + seconds + "s ");

        // If the count down is finished, write some text
        if (distance <= 0) {
            $(textspace).text("Closed.");
        }
    }, 1000);
}

function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}