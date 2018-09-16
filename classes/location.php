<?php
/**
 * The i18n string, gets the current user language browser.
 * 
 * @var string
 */
$i18n = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

switch($i18n) {
    case 'es':
    $_SESSION['lang'] = 'es';
    break;
    default:
    $_SESSION['lang'] = 'en';
    break;
}

// Then i18n lang will be loaded.
include './i18n/'.$_SESSION['lang'].'.php';
?>