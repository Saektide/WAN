<?php
header('Content-type: application/json;charset=utf-8');
error_reporting(0);

include_once('./session.php');
header('Access-Control-Allow-Origin: *');

function r__($wiki, $userAgent) {
    $url = 'http://'.$wiki.'.wikia.com/api.php';
    $data = array(
        'action'  => 'query',
        'list'    => 'recentchanges',
        'rclimit' => '1',
        'rcprop'  => 'user|title|ids|loginfo|sizes|timestamp|comment|sizes',
        'rcshow'  => '!bot',
        'format'  => 'json'
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                         "User-Agent: $userAgent\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}

$response = array();

$w = $_GET['w'];
$UA = 'Wikia Activity Notifier (saektide.com/wan) by LemonSaektide';

$wikis = explode('|', $w);

$response['wikisList'] = $wikis;

if ($_SESSION['auth']) {
    $response['successAuth'] = true;
    foreach ($wikis as $wiki) {

        $url = 'http://'.$wiki.'.wikia.com/api.php';
        $data = array(
            'action'  => 'query',
            'list'    => 'recentchanges',
            'rclimit' => '1',
            'rcprop'  => 'user|title|ids|loginfo|sizes|timestamp|comment|sizes',
            'rcshow'  => '!bot',
            'format'  => 'json'
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                            "User-Agent: $UA\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        preg_match('/HTTP\/1.1 (.*?) /', $http_response_header[0], $responseMatch, PREG_OFFSET_CAPTURE);
        $response['wikisRC'][$wiki]['status'] = $responseMatch[1][0];
        if ($responseMatch[1][0] == '301') {
            $response['wikisRC'][$wiki]['from'] = $wiki;
            preg_match('/Location: http(s)?:\/\/(.*).wikia.com\/api.php/', $http_response_header[6], $redirectMatch, PREG_OFFSET_CAPTURE);
            $response['wikisRC'][$wiki]['to'] = $redirectMatch[2][0];

            $result = r__($redirectMatch[2][0], $UA);
        }
        if ($result === FALSE) {
            $response['wikisRC'][$wiki]['rc'] = null;
        } else {
            $response['wikisRC'][$wiki]['rc'] = json_decode($result)->query->recentchanges[0];
        }
    }
} else {
    $response['successAuth'] = false;
    $response['info'] = 'Invalid session';
}

echo json_encode($response, 448);

?>