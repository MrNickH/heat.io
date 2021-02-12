<?php
$accountDetailsButton = \Text::buttonGen('/account/details', 'fa-user-circle', 'Profile');
$forumDetailsButton = \Text::buttonGen('/account/forum', 'fa-comments', 'Forum');
$statsButton = \Text::buttonGen('/account/stats', 'fa-signal', 'Stats');
$notifications = \Text::buttonGen('/account/notifications', 'fa-comment-alt', 'Notifications', 'btn-default');
$messages = \Text::buttonGen('/account/messages', 'fa-envelope', 'Messages', 'btn-warning');
$adminButton = SiteSession::LOU()->checkPermission('admin-panel') ? \Text::buttonGen('/admin', 'fa-wrench','Admin', 'btn-danger') : "";
$logoutButton = \Text::buttonGen('/account/logout', 'fa-sign-out-alt','Logout', 'btn-danger');
?>
<accountbuttons>
    <?=$accountDetailsButton
    . " " .$statsButton
    . " " .$forumDetailsButton
    . " " . $messages
    . " " . $notifications
    . " " . $adminButton
    . " " . $logoutButton?>
</accountbuttons>