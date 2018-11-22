<?php
header('Content-type: application/json;charset=utf-8'); // Set for JSON content.
error_reporting(0); // For warnings and notices.
include_once('./../session.php'); // For auth session
include_once('./../util.php');
header('Access-Control-Allow-Origin: *'); // For CORS policy.

$response = array();

$username = $_POST['user'];
$reason = $_POST['reason'];
$expiry = $_POST['expiry'];
$UA = 'Wiki Activity Notifier (saektide.com/wan) by LemonSaektide'; // Default User-Agent

if ($_SESSION['auth']) {
    $response['successAuth'] = true;
    if (!$username && !$reason && !$expiry) {
        $response['errorinfo'] = 'Bad parameters requested';
        die(json_encode($response));
    }

    if (!isset($_SESSION['token'])) {
        $response['errorinfo'] = 'Current session doesn\'t have a token var';
        die(json_encode($response));
    }

    if (isset($_POST['domain'])) {
        $url = 'http://'.$_POST['domain'].'/api.php';
        // init curl request
        $request = curl_init($url);

        //auth token
        $tkn = $_SESSION['token'];
        $mwtkn = $_SESSION['mwtoken'];

        curl_setopt($request, CURLOPT_HTTPHEADER, array(
            "Cookie: access_token=$tkn",
            "User-Agent: $UA"
        ));

        curl_setopt($request, CURLOPT_POST, 1);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, 
            http_build_query(array(
                'token'  => $mwtkn,
                'action' => 'block',
                'user'   => $username,
                'reason' => $reason,
                'expiry' => $expiry,
                'format' => 'json'
            ))
        );

        $result = curl_exec($request);
        curl_close($request);

        $response['result'] = json_decode($result);
    } else {
        $response['errorinfo'] = 'Wiki domain no provided!';
        die(json_encode($response));
    }
} else {
    $response['successAuth'] = false;
    $response['errorinfo'] = 'You are not authed to this service!';
}

echo json_encode($response, 442);

?>