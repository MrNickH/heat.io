<?php
try {
    require 'Core/requires.php';
    $navigation = new CORE_Navigation();
    $output = $navigation->get_output();
} catch (Exception $e) {
    var_dump($e);
} echo $output;