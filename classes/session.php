<?php

session_start();

// Review session

if (!$_SESSION['wikis']) {
    $_SESSION['wikis'] = array();
}

if (!$_SESSION['lang']) {
    $_SESSION['lang'] = 'en';
}

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
}

?>