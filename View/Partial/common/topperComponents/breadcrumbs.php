<forumbreadcrumbs>
    <a href="/forum">Forum</a> &raquo;
    <?php if(!$cat_link): ?>
        <?=$cat_title_text?>
    <?php else: ?>
        <a href="/forum/category/<?=$cat_link?>">
            <?=$cat_title_text?>
        </a> &raquo;
    <?=$top_title_text?>

    <?php endif;?>
</forumbreadcrumbs>