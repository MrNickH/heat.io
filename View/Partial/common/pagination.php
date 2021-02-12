<?php
    $p = $count != 1;
    $tp = $totalcount > 1;

    $nonGetArray = $_GET;
    $pageGetArray = $_GET;
    unset($nonGetArray['P_one']);
    unset($nonGetArray['P_two']);
    unset($nonGetArray['P_three']);
    unset($nonGetArray['P_four']);
    unset($nonGetArray['P_five']);
    unset($nonGetArray['P_six']);
    unset($nonGetArray['page']);

    $ngaString = [];
    foreach ($nonGetArray as $key => $val) {
        $ngaString[] = $key.'='.$val;
    }


    $pageString =
        "/".$pageGetArray['P_one']."/"
        .($pageGetArray['P_two']   ? $pageGetArray['P_two']."/":"")
        .($pageGetArray['P_three'] ? $pageGetArray['P_three']."/":"")
        .($pageGetArray['P_four'] ? $pageGetArray['P_four']."/":"")
        .($pageGetArray['P_five'] ? $pageGetArray['P_five']."/":"")
        .($pageGetArray['P_six'] ? $pageGetArray['P_six']."/":"")
        .'?'
        .implode('&', $ngaString)


?>
<?php if($info || $pages): ?>
<pagination>
    <?php if($info):?>
    <pageviewinfo>
        Viewing <?= $count ?> <?= $item ?><?= $p ? "s" : "" ?>

        <?php if ($count == 1): ?>
            - <?=$first?> only.
            (<?= $count ?> of <?= $totalcount ?>)
        <?php elseif ($count > 1): ?>
            - <?= $first ?> through <?= $last ?>.
            (<?= $count ?> of <?= $totalcount ?>)
        <?php endif; ?>

    </pageviewinfo>
    <?php endif; ?>
    <?php if ($pages): ?>
        <pagelisting>
            <?php for ($i = 1; $i <= $pageCount; $i++): ?>
                <a <?=$page == $i ? 'class="selected"' : ''?> href="<?=$pageString?>&page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </pagelisting>
    <?php endif; ?>
</pagination>
<?php endif; ?>