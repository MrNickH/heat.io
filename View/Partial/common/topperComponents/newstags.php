<tagtopper>
    <?php foreach ($tags as $tag): ?>
        <?=\Text::buttonGen('/news/tag/'.$tag->tag_slug, 'fa fa-tag', $tag->tag_name, 'btn-primary') ?>
    <?php endforeach; ?>
</tagtopper>
