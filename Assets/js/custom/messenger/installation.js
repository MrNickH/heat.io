    var deferredInstallationPrompt = false;

    window.addEventListener('beforeinstallprompt', function(e) {
        console.log('bip');
        e.preventDefault();
        deferredInstallationPrompt = e;

        $('.gdm-install-link').show();
    });

    $('.gdm-install-link').click(function(){

        if (!deferredInstallationPrompt) {
            return;
        }

        deferredInstallationPrompt.prompt();
        deferredInstallationPrompt.userChoice.then(function(choiceResult){
            if(choiceResult.outcome == 'dismissed') {
                console.log('User cancelled home screen install');
            } else {
                console.log('User added to home screen');
            }

            // We no longer need the prompt.  Clear it up.
            deferredInstallationPrompt = false;
        });
    });


    if('serviceWorker' in navigator && window.location.pathname == '/account/messages/mobile') {
        navigator.serviceWorker.register('/account/messages/sw', {scope: '/account/messages/'});
    }