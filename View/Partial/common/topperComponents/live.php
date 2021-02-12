<?php
$streaming = $streamer->isStreaming();
$on = $streamer->isOnline();
?>

<live>
    <a href="<?= $streaming ? "/streams/watch/" : "/videos/channel/" ?><?= $streamer->slug_text ?>">
        <liveblob class="<?= ($on || $streaming) ? "on":"off" ?>"><?=$streaming ? "streaming" : ($on ? "online":"offline") ?></liveblob>
    </a>
    <avatar><img src="<?= $streamer->thumbnail_image ?>"></avatar>
</live>

