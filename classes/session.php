<?php

session_start(); // Session always starts

// Review session

if (!$_SESSION['wikis']) {
    $_SESSION['wikis'] = array();
}

if (!$_SESSION['lang']) {
    $_SESSION['lang'] = 'en';
}

if (!$_SESSION['auth']) {
    $_SESSION['auth'] = false;
}

/**
 * "action" is used as an API Session.
 * 
 * @var string
 */

$action = $_POST['action'];

if ($action == 'destroy') {
    session_unset();
    session_destroy();
} elseif ($action == 'saveWiki') {
    $domainWiki = $_POST['wiki'];
    if (!$domainWiki) echo 'failed';
    else {
        array_push($_SESSION['wikis'],$domainWiki);
        echo 'ok';
    }
} elseif ($action == 'removeWiki') {
    $wikiID = $_POST['id'];
    if ($wikiID == null) echo 'failed';
    else {
        if ($wikiID != 0) unset($_SESSION['wikis'][$wikiID]);
        else array_shift($_SESSION['wikis']);
        echo 'ok';
    }
} elseif ($action == 'setTempAuthKey') {
    $authkey = $_POST['authkey'];
    if (isset($authkey)) {
        $_SESSION['auth_key'] = $authkey;
    }
}

?>