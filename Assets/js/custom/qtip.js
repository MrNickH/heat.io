$(".qtip-trigger").each(function(){

    var textContent = $(this).find(".qtip-content-text").html();
    var that = this;
    $(this).qtip({
        content: textContent,
        style: "qtip-youtube",
        position: {
            my: 'top center',
            at: 'bottom center',
            viewport: $(document.body),
            adjust: {
                resize: true // Can be ommited (e.g. default behaviour)
            }
        }
    });
});

