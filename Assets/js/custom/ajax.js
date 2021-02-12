$(function(){

    window['callServer'] = function(path, parameters, callback, passthru) {
        if(callback) {
            if (typeof callback ==  "string") {
                var callback = window[callback];
            }

            if (typeof callback != "function") {
                var callback = function(){console.log('path' + path + 'specified a weird callback.');};
            }

        } else {
            var callback = function(){};
        }


        $.post(
            path, parameters
        ).done(
            function (response, status, xhr) {
                if (typeof response != 'object') {
                    try {
                         var parsedResp = JSON.parse(response)
                    } catch (e) {
                        handleAjaxError();
                        return;
                    }
                    callback(parsedResp, passthru);
                } else {
                    callback(response, passthru);
                }
            }
        ).fail(
            function() {
                console.log('Server request failed - ' + path)
            }
        );
    }
});