<header class="">
    <widthconstrainer>
        <a class="logo" href="/">
            <logo>
                <img src="<?=ASSETURL?>/img/logo/white-across.png">
            </logo>
            <?php if(\Model\Utilities\Theming::checkForTheme()): ?>
                <img class="theme" src="<?=\Model\Utilities\Theming::$theme['images']['logoaccent']?>">
            <?php endif; ?>
        </a><navcontainer><?= View::partialView('Partial/common/logincta') ?><?= View::partialView('Partial/header/nav') ?></navcontainer>
    </widthconstrainer>
</header>
