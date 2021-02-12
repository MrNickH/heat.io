<!-- Modal -->
<?php if (isset($modal)) : ?>
    <?php if (isset($modal['gate']) && $modal['gate']) : ?>
    <gate class="modalblocker">&nbsp;</gate>
    <?php endif; ?>
    <modal>
        <?= View::partialView('Partial/common/modals/'.$modal['file'])?>
    </modal>
<?php endif; ?>
