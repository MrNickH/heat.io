$(function(){
   if (!$("account #prefs").length) {
      return;
   }

   $('#toggleEmails input').change(function() {
      if($(this)[0].checked){
         $('.email-toggle:not([disabled])').prop('checked', true);
      } else {
         $('.email-toggle:not([disabled])').removeAttr('checked');
      }
   });

   $('#toggleNotifications input').change(function() {
      if($(this)[0].checked){
         $('.noti-toggle:not([disabled])').prop('checked', true);
      } else {
         $('.noti-toggle:not([disabled])').removeAttr('checked');
      }
   });

   $('.saveNotiPrefs').click(function(){
      callServer(
          '/account/notifications/saveprefs',
          $("account #prefs").serialize(),
          function(result) {
             if(result.OK) {
                $('.saveNotiPrefs').html('<i class="fa fa-check"></i> Saved!').removeClass('btn-info').addClass('btn-success');
                setTimeout(function(){
                   $('.saveNotiPrefs').html('<i class="fa fa-save"></i> Save Preferences').addClass('btn-info').removeClass('btn-success');
                }, 3000);
             }
             console.log(result);
          }
      )
   });
});