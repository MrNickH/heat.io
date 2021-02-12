<?php if(isset($hili) && SiteSession::LOU()): ?>

<?php else: ?>
<logincta>
    <?php $returnString = "?return=".$_SERVER['REQUEST_URI'] ?>
    <?php if ($action): ?>
        <text>Please login or register to <?= $action ?>.</text>
        <?php $returnString = "?return=" . ($custom_url ?? $_SERVER['REQUEST_URI']);  ?>
    <?php endif; ?>
    <div>
    <?php if (SiteSession::LOU()): ?>
        <span>
            <a class="btn btn-info btn-single" href="/account/details">
                <i class="fa fa-user-circle"></i>
            </a>
            <a class="btn btn-danger btn-single" href="/account/logout">
                <i class="fa fa-sign-out-alt"></i>
            </a>
        </span>
        <span class="bubbleable">
            <a class="btn btn-warning message-bubble btn-single" href="/account/messages">
                <i class="fa fa-envelope"></i>
                <span class="bubble"></span>
            </a>
            <a class="btn btn-default noti-bubble btn-single" href="/account/notifications">
                <i class="fa fa-comment-alt"></i>
                <span class="bubble"></span>
            </a>
        </span>
        <?php if(SiteSession::getSessionVar('oldUser')): ?>
            <a class="btn btn-success" href="/account/unimpersonate">
                <i class="fa fa-cross"></i>
                Stop Impersonating
            </a>
        <?php endif;?>

    <?php else: ?>
        <span>
            <a class="btn btn-active btn-wide" href="/account/login<?= $returnString ?? "" ?>">
                Login
            </a>
            <a class="btn btn-full btn-wide" href="/account/register">
                Register
            </a>
        </span>
    <?php endif; ?>
    </div>
</logincta>
<?php endif; ?>