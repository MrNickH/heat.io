<<?=$button?"button":"a"?> class='btn <?= $class ?>' <?php if ($link ?? false): ?> href='<?= $link ?>' <?php endif;?>  <?= $aCustom ?>>
    <?php if($fa): ?><i class='fa <?= $fa ?>'></i><?php endif;?> <?= $text ?>
</<?=$button?"button":"a"?>>