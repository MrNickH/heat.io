$(function(){

    var windownotifications = {};
    var playPop = false;
    var firstLoad = false;
    var nonmessagenotifications = 0;
    var messagesmode = false;

    /*

               db         88  88     88888888ba
              d88b        88  88     88      "8b
             d8'`8b       88  88     88      ,8P
            d8'  `8b      88  88     88aaaaaa8P'  ,adPPYYba,   ,adPPYb,d8   ,adPPYba,  ,adPPYba,
           d8YaaaaY8b     88  88     88""""""'    ""     `Y8  a8"    `Y88  a8P_____88  I8[    ""
          d8""""""""8b    88  88     88           ,adPPPPP88  8b       88  8PP"""""""   `"Y8ba,
         d8'        `8b   88  88     88           88,    ,88  "8a,   ,d88  "8b,   ,aa  aa    ]8I
        d8'          `8b  88  88     88           `"8bbdP"Y8   `"YbbdP"Y8   `"Ybbd8"'  `"YbbdP"'
                                                               aa,    ,88
                                                                "Y8bbdP"
     */

    checkNotifications();
    messagesmode = ($('messaging').length > 0);

    function checkNotifications() {
        callServer('/account/notifications/call', '', handleNotifications);
    }

    function handleNotifications(response, passthrough) {
        if (response.halt) {
            return;
        }

        if($("maininner > notifications").length) {
            return;
        }

        var updatedList = [];
        response.notifications.forEach(function (notification) {
            var nID = sha1(notification.notification_id);

            //CREATE OR UPDATE -- Depending on existance//
            if (!windownotifications[nID]) {
                windownotifications[nID] = JSON.parse(notification.content_json);
                windownotifications[nID]['id'] = notification.notification_id;
                windownotifications[nID]['time'] = notification.time;
                playPop = true;
            }
            //Make list of IDS
            updatedList.push(notification.notification_id);
        });

        deleteOldWindowNotifications(updatedList);

        showNotifications();
        dealWithClearAllButton();

        setNotificationCount(response.noticount);
        setMessageCount(response.messagecount);
        setTimeout(checkNotifications, 5000);

        if (firstLoad == false) {
            firstLoad = true;
            playPop = false;

            return;
        }

        if(!playPop){
            return;
        }

        playPop = false;

        if (messagesmode && nonmessagenotifications == 0){
            return;
        }

        var audio = new Audio('/Assets/sounds/pop.mp3');
        audio.play();
    }

    function dealWithClearAllButton() {

        var lengthToTest = Object.keys(windownotifications).length;

        if(messagesmode){
            lengthToTest = nonmessagenotifications;
        }

        if(lengthToTest <= 1) {
            removeClearAllButton();
            return;
        }

        if(!clearAllButtonExists()){
            var button = $("<a id='clearall' class='styled-button'><i class='fa fa-eye'></i> Mark all Read</a>").click(function(){
                callServer(
                    '/account/notifications/clear/all',
                    [],
                    function(response) {
                        $("notifications").empty();
                        windownotifications = {};
                        removeClearAllButton();
                    }
                );
            });
            $("holder").append( button );
        }
    }

    function removeClearAllButton() {
        $("holder #clearall").remove();
    }

    function clearAllButtonExists() {
        return $("holder #clearall").length > 0;
    }

    function showNotifications() {

        Object.keys(windownotifications).forEach(function (index) {

            let content = windownotifications[index];

            if('visible' in content) {
                return;
            }

            if(content.icon == 'fa-envelope-square' && messagesmode){
                //Don't show DM Notis on DM SYSTEM
                return;
            }

            nonmessagenotifications++;


            var notification = $("<notification id='notification_'>").attr('id', 'notification_'+content.id)
            notification.data('notiid', content.id);
            notification.data('notihash', index);
            notification.append($("<n-icon></n-icon>").append($("<i class='fa'></i>").addClass(content.icon)));
            notification.append($("<n-image></n-image>").append($("<img/>").attr('src', content.img)));

            notification.append($("<n-title></n-title>").text(content.title));
            notification.append($("<n-subtext></n-subtext>").text(content.message));
            notification.append($("<n-time></n-time>").text(content.time));

            var actions = $("<n-actions></n-actions>");

            var icon = $('<i class="fa"></i>').addClass('fa-archive');
            var tag = $('<a class="btn btn-primary"></a>').data('id', content.id);
            tag.append(icon);
            tag.click(function() {
                activateArchiveButton(this);
            });

            actions.append(tag);

            if(content.actions && typeof content.actions == 'object') {
                content.actions.forEach(function(action){
                    Object.keys(action).forEach(function (index) {
                        var button = action[index];
                        var icon = $('<i class="fa"></i>').addClass(button.icon);
                        var tag = $('<a class="btn btn-success btn-icon"></a>').attr('href', button.url);
                        tag.append(icon)
                        actions.append(tag);
                    });
                });
            }

            notification.append(actions);

            notification.append($("<i class='fa fa-times-circle'></i>").click(function(){
                callServer(
                    '/account/notifications/clear/'+$(this.parentNode).data('notiid'),
                    [],
                    function(response, passthrough) {
                        delete windownotifications[index];
                        passthrough.remove();
                    },
                    this.parentNode
                );
            }));

            $("holder notifications").prepend(notification);

            windownotifications[index].visible = 1;
        });
    }

    function deleteOldWindowNotifications(updatedList) {
        $("notifications notification").each(function () {
            if (updatedList.indexOf($(this).data('notiid')) == -1) {
                delete windownotifications[$(this).data('notihash')];
                $(this).remove();
            }
        });
    }

    /*
        88888888ba                88           88           88
        88      "8b               88           88           88
        88      ,8P               88           88           88
        88aaaaaa8P'  88       88  88,dPPYba,   88,dPPYba,   88   ,adPPYba,  ,adPPYba,
        88""""""8b,  88       88  88P'    "8a  88P'    "8a  88  a8P_____88  I8[    ""
        88      `8b  88       88  88       d8  88       d8  88  8PP"""""""   `"Y8ba,
        88      a8P  "8a,   ,a88  88b,   ,a8"  88b,   ,a8"  88  "8b,   ,aa  aa    ]8I
        88888888P"    `"YbbdP'Y8  8Y"Ybbd8"'   8Y"Ybbd8"'   88   `"Ybbd8"'  `"YbbdP"'
     */


    function setNotificationCount(count) {
        if(count > 99){
            count = "99+";
        }

        if($(".noti-bubble .bubble").text() != count) {
            if (count == 0) {
                $(".noti-bubble .bubble").slideUp(300);
            } else {
                $(".noti-bubble .bubble").slideDown(300);
                $(".noti-bubble .bubble").text(count);
            }
        }
    }
    function setMessageCount(count) {
        if(count > 99){
            count = "99+";
        }

        if (count == 0) {
            $(".message-bubble .bubble").slideUp(300);
        } else {
            $(".message-bubble .bubble").slideDown(300);
            $(".message-bubble .bubble").text(count);
        }
    }

    /*
               db                                 88           88
              d88b                                88           ""
             d8'`8b                               88
            d8'  `8b      8b,dPPYba,   ,adPPYba,  88,dPPYba,   88  8b       d8   ,adPPYba,
           d8YaaaaY8b     88P'   "Y8  a8"     ""  88P'    "8a  88  `8b     d8'  a8P_____88
          d8""""""""8b    88          8b          88       88  88   `8b   d8'   8PP"""""""
         d8'        `8b   88          "8a,   ,aa  88       88  88    `8b,d8'    "8b,   ,aa
        d8'          `8b  88           `"Ybbd8"'  88       88  88      "8"       `"Ybbd8"'
     */

    $('notification n-actions a.archive').click(function(){
        activateArchiveButton(this);
    });

    function activateArchiveButton(tag) {
        callServer('/account/notifications/archive/' + $(tag).data('id'), {}, dealWithArchive, tag.parentNode.parentNode);
    }

    function dealWithArchive(response, notification) {
        if(response.OK) {
            $(notification).remove();
        }
    }
});