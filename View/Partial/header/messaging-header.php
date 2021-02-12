<header class="messaging">
    <a href="#">
        <logo>GD<span>Messaging</span></logo>
        <?php if(\Model\Utilities\Theming::checkForTheme()): ?>
            <img class="theme" src="<?=\Model\Utilities\Theming::$theme['images']['logoaccent']?>">
        <?php endif; ?>
    </a>
</header>
