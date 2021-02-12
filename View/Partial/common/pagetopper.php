
<?php if($h1): ?>
    <titleholder><h1><?= $h1 ?></h1></titleholder>
<?php endif; ?>

<?php if($topperFull || $topperRight || $topperLeft): ?>

<pagetopper class="<?=$class?>">

    <?php if ($topperFull): ?>
        <topperfull><?php if (is_array($topperFull)): ?>
                <?= View::partialView('Partial/common/topperComponents/' . $topperFull['view'], $topperFull['data']) ?>
            <?php else: ?>
                <toppertext><?= $topperFull ?></toppertext>
            <?php endif; ?>
        </topperfull>
    <?php endif; ?>
    <?php if ($topperLeft): ?>
        <topperLeft>
            <?php if (is_array($topperLeft)): ?>
                <?= View::partialView('Partial/common/topperComponents/' . $topperLeft['view'], $topperLeft['data']) ?>
            <?php else: ?>
                <toppertext>
                    <?= $topperLeft ?>
                </toppertext>
            <?php endif; ?>
        </topperLeft>
    <?php endif; ?>
    <?php if ($topperRight): ?>
        <topperright>
            <?php if (is_array($topperRight)): ?>
                <?= View::partialView('Partial/common/topperComponents/' . $topperRight['view'],$topperRight['data']) ?>
            <?php else: ?>
                <toppertext>
                    <?= $topperRight ?>
                </toppertext>
            <?php endif; ?>
        </topperright>
    <?php endif; ?>
</pagetopper>

<?php endif; ?>