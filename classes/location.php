<?php
$i18n = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

switch($i18n) {
    case 'es':
    $_SESSION['lang'] = 'es';
    break;
    default:
    $_SESSION['lang'] = 'en';
    break;
}

include './i18n/'.$_SESSION['lang'].'.php';
?>