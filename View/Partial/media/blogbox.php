<itembox class="video col-sm-12 col-md-3">
    <a href="/post/<?= $type ?? 'video' ?>/<?= $video->slug_text ?>">
        <titlesection><?= $video->title_text ?></titlesection>
        <datesection><?= date("l jS F Y", strtotime($video->publish_date)) ?></datesection>
        <img src="<?= $video->getThumbnailImage() ?>"/>
        <i class="fa fa-comment"></i><?= $video->getCommentCount() ?>
        <i class="fa fa-heart"></i><?= $video->getHeartCount() ?>
    </a>
</itembox>