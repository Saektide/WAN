<?php
header('Content-type: application/json;charset=utf-8'); // Set for JSON content.
error_reporting(0); // For warnings and notices.
include_once('./../session.php'); // For auth session
include_once('./../util.php');
header('Access-Control-Allow-Origin: *'); // For CORS policy.

$response = array();

$UA = 'Wiki Activity Notifier (saektide.com/wan) by LemonSaektide'; // Default User-Agent

if ($_SESSION['auth']) {
    $response['successAuth'] = true;

    if (!isset($_SESSION['token'])) {
        $response['errorinfo'] = 'Current session doesn\'t have a token var';
        die(json_encode($response));
    }

    if (isset($_GET['domain'])) {
        $url = 'http://'.$_GET['domain'].'/api.php';
        // init curl request
        $request = curl_init($url);
        $requestToken = curl_init($url);

        // auth token
        $tkn = $_SESSION['token'];

        // Get current user's rights
        curl_setopt($request, CURLOPT_HTTPHEADER, array(
            "Cookie: access_token=$tkn",
            "User-Agent: $UA"
        ));

        curl_setopt($request, CURLOPT_POST, 1);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, 
            http_build_query(array(
                'action' => 'query',
                'meta'   => 'userinfo',
                'uiprop' => 'rights',
                'format' => 'json'
            ))
        );

        // Get current user's mw token
        curl_setopt($requestToken, CURLOPT_HTTPHEADER, array(
            "Cookie: access_token=$tkn",
            "User-Agent: $UA"
        ));

        curl_setopt($requestToken, CURLOPT_POST, 1);
        curl_setopt($requestToken, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($requestToken, CURLOPT_POSTFIELDS, 
            http_build_query(array(
                'action'  => 'query',
                'prop'    => 'info',
                'titles'  => 'Main Page',
                'intoken' => 'edit',
                'format'  => 'json'
            ))
        );

        $result = curl_exec($request);
        $resultToken = curl_exec($requestToken);

        curl_close($request);
        curl_close($requestToken);

        $keysToken = (array) json_decode($resultToken)->query->pages;
        $response['result'] = json_decode($result)->query->userinfo->rights;
        $response['mwtoken'] = array_values($keysToken)['0']->edittoken;

        $_SESSION['mwtoken'] = $response['mwtoken'];
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