$(function(){
    $("account #pw-form").submit(function (){

        var pwfield = $(this).find("#password");
        if(pwfield.length && pwfield.val().length){
            $("<input />").attr("type", "hidden")
                .attr("name", pwfield.attr("name"))
                .attr("value", sha1(pwfield.val()))
                .appendTo("account #pw-form");

            pwfield.removeAttr("name");
        }

        var pwfield1 = $(this).find("#password1");
        if(pwfield1.length && pwfield1.val().length){
            $("<input />").attr("type", "hidden")
                .attr("name", pwfield1.attr("name"))
                .attr("value", sha1(pwfield1.val()))
                .appendTo("account #pw-form");

            pwfield1.removeAttr("name");
        }
    });
});