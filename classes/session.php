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

if ($_SERVER['HTTP_HOST'] == 'localhost') $onDevRelease = true;
else $onDevRelease = false;

if ($onDevRelease) $_SESSION['auth'] = true;

if (isset($_POST['action'])) $action = $_POST['action'];
else $action = null;

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
        echo 'Wiki target: '.$_SESSION['wikis'][$wikiID];
        if ($wikiID != 0) unset($_SESSION['wikis'][$wikiID]);
        else array_shift($_SESSION['wikis']);
        echo ' // ok';
    }
} elseif ($action == 'setTempAuthKey') {
    $authkey = $_POST['authkey'];
    if (isset($authkey)) {
        $_SESSION['auth_key'] = $authkey;
    }
} elseif ($action == 'getVar') {
    if (isset($_POST['name'])) $varname = $_POST['name'];
    else die('Error: Varname no provided!');

    if (isset($_SESSION[$varname])) echo json_encode($_SESSION[$varname]);
    else die('Error: Session var no found!');
}

?>