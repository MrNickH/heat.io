<topperstreamersbox>
    <?php foreach ($streamers as $streamer): ?>
        <?= View::partialView('Partial/common/topperComponents/live',
            ['showFullStatus' => false, 'streamer' => $streamer]) ?>
    <?php endforeach; ?>
    <topperstreamersbox>