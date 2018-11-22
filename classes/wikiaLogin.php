<?php
header('Content-type: application/json;charset=utf-8'); // Set for JSON content.
error_reporting(0); // For warnings and notices.
include_once('./session.php'); // For auth session
include_once('./util.php');
header('Access-Control-Allow-Origin: *'); // For CORS policy.

$response = array();

$usr = $_POST['username'];
$psw = $_POST['password'];
$UA = 'Wiki Activity Notifier (saektide.com/wan) by LemonSaektide'; // Default User-Agent

if ($_SESSION['auth']) { // API will be responds for authed users
    $response['successAuth'] = true;
    $response['details']['username'] = $usr;
    if (!isset($usr) && !isset($psw)) {
        $response['errorinfo'] = 'Username or password no provided!';
    } else {
        $url = 'https://services.wikia.com/auth/token';
        $data = array(
            'username' => $usr,
            'password' => $psw
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                             "User-Agent: $UA\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if (isset(json_decode($result)->access_token)) {
            $_SESSION['token'] = json_decode($result)->access_token;
            $_SESSION['wikiauser'] = $usr;
        }
    }
} else {
    $response['successAuth'] = false;
    $response['errorinfo'] = 'You are not authed to this service!';
}

if (isset($result)) $response['result'] = json_decode($result);

echo json_encode($response, 442);
?>