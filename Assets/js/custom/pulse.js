$(function(){
    function checkPulse() {
        callServer('/account/pulse','', handlePulse);
    }

    function handlePulse(pulse, passthrough) {
        //console.log(pulse.halt);
          if (!pulse.halt) {
            setTimeout(checkPulse, 30000);
        }
    }

    checkPulse();
});