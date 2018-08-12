<?php

include '../classes/db.php';
include '../classes/session.php';

DB::GETAuth($_SESSION['auth_key'], function($username){
    $_SESSION['auth'] = true;
    $_SESSION['username'] = $username;
    header("Location: ../");
}, function(){
    echo 'invalid';
    header("Location: ../?failed=1");
});

$_SESSION['auth_key'] = null;

exit();

?>