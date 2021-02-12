<?= View::partialView('Partial/common/pagetopper',
    ['h1' => $h1, 'topperLeft' => $topperLeft, 'topperRight' => $topperRight, 'topperFull' => $topperFull, 'class' => $topperClass ?? ""]) ?>

<?php foreach($extraToppers as $extraTopper): ?><?= $extraTopper['left'] || $extraTopper['right'] || $extraTopper['full'] ? View::partialView('Partial/common/pagetopper',
    ['topperLeft' => $extraTopper['left'], 'topperRight' => $extraTopper['right'], 'topperFull' => $extraTopper['full'], 'class' => 'extratopper']) : "" ?>
<?php endforeach; ?>

<?php if($RE = \SiteSession::getSessionVar('RedirectError')): ?>
    <?=View::partialView('Partial/common/pagetopper', ['topperFull' => $RE, 'class' => 'errortopper']) ?>
<?php endif; ?>

<?php if(array_filter($errors)): ?>
    <?=View::partialView('Partial/common/pagetopper', ['topperFull' => sizeof($errors) == 1 ? $errors[0] : '<ul><li>'.implode("</li><li>",$errors).'</li></ul>', 'class' => 'errortopper']) ?>
<?php endif; ?>